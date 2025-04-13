<?php

declare(strict_types=1);
trait ComponentDefinitionHelper
{
    // Funktion, um alle rekursiven Keys eines Arrays zu extrahieren
    protected function getArrayKeyPaths(array $array, string $prefix = '')
    {
        $keys = [];
        foreach ($array as $key => $value) {
            // Bei JSON: Doppelpunkt entfernen
            $key = explode(':', $key)[0];

            $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

            $keys[] = $fullKey;

            if (is_array($value)) {
                $keys = array_merge($keys, $this->getArrayKeyPaths($value, $fullKey));
            }
        }
        return $keys;
    }

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
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } else {
                return null; // Key nicht gefunden
            }
        }

        return $value;
    }
}