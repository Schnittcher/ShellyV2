<?php

declare(strict_types=1);
trait ComponentDefinitionHelper
{
    // Funktion, um alle End-Key-Pfade zu extrahieren (inkl. leerer Arrays)
    protected function getArrayLeafKeyPaths(array $array, string $prefix = '')
    {
        $keys = [];

        foreach ($array as $key => $value) {
            // Bei JSON: Doppelpunkt entfernen
            //$key = explode(':', $key)[0];

            $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                if (empty($value)) {
                    // Leeres Array = Endpunkt
                    $keys[] = $fullKey;
                } else {
                    // Rekursiv weitergehen
                    $keys = array_merge($keys, $this->getArrayLeafKeyPaths($value, $fullKey));
                }
            } else {
                // Kein Array = Endpunkt
                $keys[] = $fullKey;
            }
        }

        return $keys;
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
        $parts = explode('_', $input);
        $number = null;

        // Prüfen, ob an zweiter Stelle eine Zahl ist
        if (isset($parts[1]) && is_numeric($parts[1])) {
            $number = $parts[1]; // Zahl merken
            unset($parts[1]);    // Zahl entfernen
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
        }

        // ident = original mit . und : ersetzt durch _
        $ident = str_replace(['.', ':'], '_', $componentPath);

        return [
            'original' => $componentPath,
            'clean'    => $cleanKey,
            'number'   => $tempVar,
            'ident'    => $ident
        ];
    }
}