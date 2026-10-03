<?php

declare(strict_types=1);
trait ComponentDefinitionHelper
{
    protected function getArrayLeafKeyPaths(array $array, string $prefix = '')
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                if (empty($value)) {
                    // Leeres Array = Endpunkt
                    $keys[] = $fullKey;
                    continue;
                }

                // Prüfen: Bestehen die Keys ausschließlich aus numerischen?
                $allNumericKeys = array_keys($value) === range(0, count($value) - 1);
                // Prüfen: Der vollständige Key darf nicht "events" enthalten - damit events berücksichtigt wird
                $containsNoEvents = stripos($fullKey, 'events') === false;

                if ($allNumericKeys && $containsNoEvents) {
                    // Arrays mit rein numerischen Keys sollen als Endpunkt gezählt werden
                    $keys[] = $fullKey;
                    continue;
                }

                // Sonst rekursiv weiter
                array_push($keys, ...$this->getArrayLeafKeyPaths($value, $fullKey));
            } else {
                // Kein Array = Endpunkt
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }

    protected function componentDefinitionExists($component)
    {
        if (array_key_exists($component, self::$components)) {
            return true;
        }
        return false;
    }

    protected function getValueByKeyPath(string $keyPath, string $separator = '.')
    {
        $keys = explode($separator, $keyPath);
        $value = self::$components;
        foreach ($keys as $key) {
            //Ausnahme für RGB
            if (is_array($value) && $keyPath == 'rgb.rgb.0' && array_key_exists($key, $value)) {
                return $value[$key][$key];
            }

            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } else {
                return null; // Key nicht gefunden
            }
        }

        return $value;
    }

    protected function getValueByKeyPathFromArray($array, string $keyPath, string $separator = '.')
    {
        $keys = explode($separator, $keyPath);
        $value = $array;

        foreach ($keys as $key) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } else {
                return null; // Key nicht gefunden
            }
        }

        return $value;
    }

    protected function convertIdentToKeyPath($input)
    {
        $number = null;
        //Ausnahmen: Bei dem der Unterstrich nicht gegen einen Punkt ersetzt werden darf
        $exceptions = ['current_pos', 'target_C', 'current_C'];

        foreach ($exceptions as $ending) {
            // Prüfen, ob die Endung im String vorkommt
            if (str_contains($input, $ending)) {
                // Splitte String an der ersten Vorkommen der Endung
                $parts = explode($ending, $input, 2);
                $before = $parts[0]; // Alles vor der Endung
                $after = $ending . ($parts[1] ?? ''); // Endung + Rest

                // Ersetze ALLE Unterstriche im "before"-Teil durch Punkte
                $before = str_replace('_', '.', rtrim($before, '_'));

                // Ergebnis zusammensetzen
                $input = $before . '.' . $after;
                $parts = explode('.', $input);
                // Prüfen, ob an zweiter Stelle eine Zahl ist
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $number = $parts[1]; // Zahl merken
                    unset($parts[1]);    // Zahl entfernen
                    // Neu zusammensetzen mit Punkten
                    $result = implode('.', array_values($parts)); // array_values zur Neuindexierung
                    return [$result, $number];
                }
            }
            $parts = explode('_', $input);
            // Prüfen, ob an zweiter Stelle eine Zahl ist
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $number = $parts[1]; // Zahl merken
                unset($parts[1]);    // Zahl entfernen
            }
        }

        // Neu zusammensetzen mit Punkten
        $result = implode('.', array_values($parts)); // array_values zur Neuindexierung
        return [$result, $number];
    }

    protected function cleanComponentPath($componentPath)
    {
        // Prüfen auf Doppelpunkt mit Zahl
        if (preg_match('/(.*):(\d+)(.*)/', $componentPath, $matches)) {
            $base = $matches[1];      // z. B. "input"
            $number = $matches[2];    // z. B. "0"
            $rest = $matches[3];      // z. B. ".id" oder ".temperature.tC"

            $cleanKey = $base . $rest;
            $tempVar = $number;
        } else {
            $cleanKey = $componentPath;
            $tempVar = '';
            $base = explode('.', $componentPath)[0];
        }

        // ident = original mit . und : ersetzt durch _
        $ident = str_replace(['.', ':'], '_', $componentPath);

        return [
            'base'     => $base,
            'original' => $componentPath,
            'clean'    => $cleanKey,
            'number'   => $tempVar,
            'ident'    => $ident
        ];
    }
    // ### TEST / EXPERIMENTELL - Dynamisch angelegte Komponenten ###
    // Extrahiert Komponenten aus dem Shelly.GetComponents-Ergebnis, die per RPC dynamisch
    // hinzugefügt werden (ID-Raum ab 200) und deshalb weder in Shelly.GetStatus noch in
    // Shelly.GetConfig auftauchen, sondern nur hier: Boolean/Number/Enum/Text (Shelly "User-defined
    // components") sowie presencezone (Shelly Presence-Zonen - kein Boolean/Number/Enum/Text, aber
    // genauso dynamisch angelegt und nur hier auffindbar). Details/Beispiel-Payload siehe
    // components.php (Kommentar über dem 'boolean'-Eintrag). Wird nur noch vom Configurator genutzt
    // (Namen der dynamischen Komponenten in der Liste, z.B. "boolean:200 (Test)") - die Geräte-Module
    // lesen Config und Status aller Komponenten direkt aus der Gesamtantwort (getComponentConfigs()/
    // getAllComponentsAsStatusDict()).
    //
    // $Payload = das 'result' einer Shelly.GetComponents-Antwort, z.B.:
    //   {"components": [
    //     {"key": "boolean:200", "status": {"value": false, ...}, "config": {"name": "Test", ...}},
    //     {"key": "pm1:0",       "status": {...},                 "config": {"name": "Trockner", ...}},
    //     ...
    //   ], "cfg_rev": 49, "offset": 0, "total": 16}
    //
    // Rückgabe (nur die boolean:/number:/enum:/text:/presencezone:-Einträge, alles andere wie
    // "pm1:0" wird ignoriert):
    //   [
    //     'status' => ['boolean:200' => ['value' => false, ...]],   // -> Werte/Discovery
    //     'config' => ['boolean:200' => ['name' => 'Test', ...]],   // -> Name-/Optionen-Override
    //   ]
    protected function getDynamicallyAddedComponents($Payload)
    {
        $dynamicComponentTypes = ['boolean', 'number', 'enum', 'text', 'presencezone', 'object'];
        $status = [];
        $config = [];
        if (isset($Payload['components'])) {
            foreach ($Payload['components'] as $component) {
                if (!isset($component['key'])) {
                    continue;
                }
                $prefix = explode(':', $component['key'])[0];
                if (!in_array($prefix, $dynamicComponentTypes, true)) {
                    continue;
                }
                $status[$component['key']] = $component['status'] ?? [];
                $config[$component['key']] = $component['config'] ?? [];
            }
        }
        return ['status' => $status, 'config' => $config];
    }
    // ### ENDE TEST / EXPERIMENTELL ###

    // Baut - anders als getDynamicallyAddedComponents(), das nach Präfix filtert - das GESAMTE
    // Shelly.GetComponents-Ergebnis in ein flaches {"switch:0":{...},"sys":{...},...}-Dict um (die Form,
    // die der Rest der Module-Pipeline kennt, ursprünglich die von Shelly.GetStatus). Der INHALT (Feldnamen
    // pro Komponente) ist live verifiziert identisch - hier wird nur die HÜLLE übersetzt:
    //   {"components": [{"key":"switch:0","status":{...},"config":{...}}, ...]}
    //   ->
    //   {"switch:0": {...}, ...}
    protected function getAllComponentsAsStatusDict($Payload)
    {
        $status = [];
        if (isset($Payload['components'])) {
            foreach ($Payload['components'] as $component) {
                if (!isset($component['key'])) {
                    continue;
                }
                $status[$component['key']] = $component['status'] ?? [];
            }
        }
        return $status;
    }

    // ### TEST / EXPERIMENTELL - Vereinheitlichte Config-Quelle für ALLE Komponenten ###
    // Liefert {"switch:0": {...volle config...}, "boolean:200": {...volle config...}, ...} aus dem
    // GESAMTEN Shelly.GetComponents-Ergebnis - für JEDEN Komponententyp, nicht nur die dynamischen
    // (anders als getDynamicallyAddedComponents(), das nach Präfix filtert). Einzige Config-Quelle der
    // Geräte-Module: eine frühere zweite, dynamic_only-gefilterte Abfrage hat eine Race Condition
    // zwischen zwei unabhängigen Anfragen provoziert (live beobachtet: "Boolean 200" statt "Power
    // supply", weil die separate Antwort noch nicht da war). Wird in registerComponentVariables()/
    // createVariableListForForm() genutzt: für physische Komponenten als Namens-Präfix (z.B.
    // "Waschmaschine - Active power"), für dynamische Komponenten über getDynamicComponentMetadata()
    // (Name, Optionen, Min/Max, Access).
    protected function getComponentConfigs($Payload)
    {
        $configs = [];
        if (isset($Payload['components'])) {
            foreach ($Payload['components'] as $component) {
                if (!isset($component['key'])) {
                    continue;
                }
                $configs[$component['key']] = $component['config'] ?? [];
            }
        }
        return $configs;
    }
    // ### ENDE TEST / EXPERIMENTELL ###
}