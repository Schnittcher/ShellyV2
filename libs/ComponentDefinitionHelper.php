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

                if ($allNumericKeys) {
                    // Arrays mit rein numerischen Keys sollen als Endpunkt gezählt werden
                    $keys[] = $fullKey;
                    continue;
                }

                // Sonst rekursiv weiter
                $keys = array_merge($keys, $this->getArrayLeafKeyPaths($value, $fullKey));
            } else {
                // Kein Array = Endpunkt
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }

    /**
     * // Funktion, um alle End-Key-Pfade zu extrahieren (inkl. leerer Arrays)
     * protected function getArrayLeafKeyPaths(array $array, string $prefix = '')
     * {
     * $keys = [];
     *
     * foreach ($array as $key => $value) {
     * // Bei JSON: Doppelpunkt entfernen
     * //$key = explode(':', $key)[0];
     *
     * $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;
     *
     * if (is_array($value)) {
     * if (empty($value)) {
     * // Leeres Array = Endpunkt
     * $keys[] = $fullKey;
     * } else {
     * // Rekursiv weitergehen
     * $keys = array_merge($keys, $this->getArrayLeafKeyPaths($value, $fullKey));
     * }
     * } else {
     * // Kein Array = Endpunkt
     * $keys[] = $fullKey;
     * }
     * }
     *
     * return $keys;
     * }
     */
    protected function componentDefinitionExists($component)
    {
        //IPS_LogMessage('test', $component);
        if (array_key_exists($component, self::$components)) {
            return true;
        }
        return false;
    }

    protected function getValueByKeyPath(string $keyPath, string $separator = '.')
    {
        $keys = explode($separator, $keyPath);
        $value = self::$components;
        IPS_LogMessage('keyPath', $keyPath);
        IPS_LogMessage('value', print_r($value, true));
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
                IPS_LogMessage('input', $input);
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
                    IPS_LogMessage('input 4', $result);
                    return [$result, $number];
                }
            }
            IPS_LogMessage('input else', $input);
            $parts = explode('_', $input);
            // Prüfen, ob an zweiter Stelle eine Zahl ist
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $number = $parts[1]; // Zahl merken
                unset($parts[1]);    // Zahl entfernen
            }
        }

        // Neu zusammensetzen mit Punkten
        $result = implode('.', array_values($parts)); // array_values zur Neuindexierung
                    IPS_LogMessage('input 4', $result);
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
    //Mir dieser Funktion werden wird das Payload von getComponents so aufgearbeitet, dass es aussieht, als wären die Informationen von getStatus bekommen, sodass der restliche Code vom Modul genutzt werden kann.
    protected function getBLUTRVs($Payload)
    {
        $trvs = [];
        // Prüfen ob 'components' existiert
        if (isset($Payload['components'])) {
            foreach ($Payload['components'] as $component) {
                if (!isset($component['key'])) {
                    continue;
                }
                // Prüft ob der Key mit "blutrv:" beginnt
                if (strpos($component['key'], 'blutrv:') === 0) {

                    // ID aus dem Key extrahieren, falls du sie brauchst
                    preg_match('/blutrv:(\d+)/', $component['key'], $matches);
                    $id = isset($matches[1]) ? (int) $matches[1] : null;

                    $trvs[$component['key']] = array_merge(
                            ['id' => $id],
                            $component['status'] ?? []
                        );
                    /**
                     * $trvs[$component['key']] = [
                     * //    'key'    => $component['key'],
                     * 'id'     => $id,
                     * 'status' => $component['status'] ?? null,
                     * //  'config' => $component['config'] ?? null,
                     * ];
                     */
                }
            }
        } else {
            IPS_LogMessage(__FUNCTION__, 'Keine Komponenten gefunden.');
        }
        return $trvs;
    }
}