<?php

declare(strict_types=1);

//Liste der Shelly BLU-Gerätemodelle (BTHome). Das Gateway liefert zu jedem BLU-Gerät in Shelly.GetComponents die Modell-ID
//(attrs.model_id, z.B. 8 beim BLU TRV, 23 beim BLU Button Tough 1 ZB), aber keinen Namen und keine Liste der Messwerte - die
//Sensoren legt das Gateway erst an, wenn das Gerät sie zum ersten Mal gesendet hat. Mit dieser Liste kennt das Modul das Gerät
//von Anfang an: Gerätetyp im Konfigurator, Variable "Taste" für Geräte mit Taste, Erkennung der Thermostate und alle Sensoren,
//die das Modell sendet (Kontakt, Luftfeuchtigkeit, Temperatur, ...), werden direkt angelegt.
//
//Neues Modell eintragen: Modell-ID (attrs.model_id der bthomedevice-Komponente im Debug von Shelly.GetComponents), Name und die
//BTHome-Objekte des Geräts laut Shelly-Doku (https://shelly-api-docs.shelly.cloud/docs-ble/Devices/...). Unbekannte Modelle
//funktionieren weiter, nur aus dem, was das Gateway meldet.
//
//Felder je Modell:
//  name      Anzeigename (im Konfigurator als Gerätetyp)
//  button    true = Taste ist eine Hauptfunktion, die Variable "Taste" gibt es von Anfang an (bei den übrigen Modellen, z.B. Tür-/
//            Fenstersensor, entsteht sie beim ersten Tastendruck)
//  buttons   Anzahl der Tasten (nur Information)
//  dial      true = Drehrad (Variablen "Rad" und "Rad Schritte" von Anfang an)
//  trv       true = Thermostat (BLU TRV): hängt an genau einem Gateway und wird über die Komponenten-Instanz angelegt
//  objects   BTHome-Objekte, die das Gerät als Sensor sendet: [obj_id, idx] oder [obj_id, idx, 'Name'] (Name, wenn das Gerät
//            ein Objekt mehrfach sendet und die Variablen unterscheidbar sein sollen, z.B. 3 x Drehwinkel der Remote Control). Die Batterie (obj_id 1) und die Taste (obj_id 58)
//            stehen nicht in der Liste (eigene Variablen des Geräts). idx unterscheidet gleiche Objekte (z.B. mehrere Temperaturen).
//
//Herkunft: Namen und IDs aus der Weboberfläche des BLU Gateway G3 (04.10.2026), Objekte aus den Doku-Seiten der Geräte. An echten
//Geräten bestätigt sind bisher nur 8 (BLU TRV) und 23 (BLU Button Tough 1 ZB).
trait BTHomeModels
{
    private static $bthomeModels = [
        1    => ['name' => 'Shelly BLU Button1', 'button' => true, 'buttons' => 1, 'objects' => []],
        2    => ['name' => 'Shelly BLU Door/Window', 'objects' => [[45, 0], [5, 0], [63, 0]]],
        3    => ['name' => 'Shelly BLU HT', 'objects' => [[69, 0], [46, 0]]],
        5    => ['name' => 'Shelly BLU Motion', 'objects' => [[33, 0], [5, 0]]],
        6    => ['name' => 'Shelly BLU Wall Switch 4', 'button' => true, 'buttons' => 4, 'objects' => []],
        7    => ['name' => 'Shelly BLU RC Button 4', 'button' => true, 'buttons' => 4, 'objects' => []],
        8    => ['name' => 'Shelly BLU TRV', 'button' => true, 'trv' => true, 'objects' => [[69, 0], [69, 1], [69, 2]]],
        9    => ['name' => 'Shelly BLU Remote ZB', 'button' => true, 'dial' => true, 'objects' => [[63, 0, 'Rotation 1'], [63, 1, 'Rotation 2'], [63, 2, 'Rotation 3'], [96, 0]]],
        10   => ['name' => 'Shelly BLU Distance', 'objects' => [[64, 0]]],
        11   => ['name' => 'Shelly BLU Weather Station', 'objects' => [[5, 0], [32, 0], [68, 0], [68, 1], [70, 0], [94, 0], [4, 0], [8, 0], [12, 0], [46, 0], [69, 0], [95, 0]]],
        12   => ['name' => 'Shelly BLU H&T Display ZB', 'objects' => [[69, 0], [46, 0], [100, 0], [30, 0], [21, 0]]],
        17   => ['name' => 'Shelly BLU HT ZB', 'objects' => [[69, 0], [46, 0]]],
        19   => ['name' => 'Shelly BLU Motion ZB', 'objects' => [[33, 0], [100, 0]]],
        20   => ['name' => 'Shelly BLU Door/Window ZB', 'objects' => [[45, 0], [63, 0], [100, 0]]],
        21   => ['name' => 'Shelly BLU Wall Switch 4 ZB', 'button' => true, 'buttons' => 4, 'objects' => []],
        22   => ['name' => 'Shelly BLU RC Button 4 ZB', 'button' => true, 'buttons' => 4, 'objects' => []],
        23   => ['name' => 'Shelly BLU Button Tough 1 ZB', 'button' => true, 'buttons' => 1, 'objects' => []],
        25   => ['name' => 'Shelly BLU Soil', 'objects' => []],
        32   => ['name' => 'Shelly BLU Door/Window Mini ZB', 'objects' => [[45, 0]]],
        33   => ['name' => 'Shelly BLU Door/Window Outdoor ZB', 'objects' => [[45, 0]]],
        8218 => ['name' => 'Shelly BLU MCB 1P+N B6 ZB', 'objects' => []],
        8219 => ['name' => 'Shelly BLU MCB 1P+N B10 ZB', 'objects' => []],
        8220 => ['name' => 'Shelly BLU MCB 1P+N B13 ZB', 'objects' => []],
        8221 => ['name' => 'Shelly BLU MCB 1P+N B16 ZB', 'objects' => []],
        8222 => ['name' => 'Shelly BLU MCB 1P+N B20 ZB', 'objects' => []],
        8223 => ['name' => 'Shelly BLU MCB 1P+N B25 ZB', 'objects' => []],
        8250 => ['name' => 'Shelly BLU MCB 1P+N C6 ZB', 'objects' => []],
        8251 => ['name' => 'Shelly BLU MCB 1P+N C10 ZB', 'objects' => []],
        8252 => ['name' => 'Shelly BLU MCB 1P+N C13 ZB', 'objects' => []],
        8253 => ['name' => 'Shelly BLU MCB 1P+N C16 ZB', 'objects' => []],
        8254 => ['name' => 'Shelly BLU MCB 1P+N C20 ZB', 'objects' => []],
        8255 => ['name' => 'Shelly BLU MCB 1P+N C25 ZB', 'objects' => []],
    ];

    //Modellname, sonst leer (unbekannte Modell-ID).
    protected function bthomeModelName(int $modelId): string
    {
        return (string) (self::$bthomeModels[$modelId]['name'] ?? '');
    }

    //Gerätetyp für die Anzeige: Modellname, bei unbekannter ID "Unbekannt (Modell-ID n)".
    protected function bthomeModelLabel(int $modelId): string
    {
        $name = $this->bthomeModelName($modelId);
        if ($name != '') {
            return $name;
        }
        return $modelId > 0 ? $this->Translate('Unknown') . ' (' . $this->Translate('Model ID') . ' ' . $modelId . ')' : '';
    }

    protected function bthomeModelHasButton(int $modelId): bool
    {
        return !empty(self::$bthomeModels[$modelId]['button']);
    }

    //Anzahl der Tasten des Modells: 'buttons' aus der Liste, sonst 1 bei Geräten mit Taste als Hauptfunktion, sonst 0.
    protected function bthomeModelButtons(int $modelId): int
    {
        if (!empty(self::$bthomeModels[$modelId]['buttons'])) {
            return (int) self::$bthomeModels[$modelId]['buttons'];
        }
        return $this->bthomeModelHasButton($modelId) ? 1 : 0;
    }

    //Eigener Name des Sensors (obj_id, idx) laut Modelltabelle, sonst leer.
    protected function bthomeModelObjectLabel(int $modelId, int $objId, int $idx): string
    {
        foreach (self::$bthomeModels[$modelId]['objects'] ?? [] as $object) {
            if ($object[0] == $objId && $object[1] == $idx && isset($object[2])) {
                return (string) $object[2];
            }
        }
        return '';
    }

    //Hat das Modell ein Drehrad (Ereignisse rotate_left/rotate_right)?
    protected function bthomeModelHasDial(int $modelId): bool
    {
        return !empty(self::$bthomeModels[$modelId]['dial']);
    }

    protected function bthomeModelIsTrv(int $modelId): bool
    {
        return !empty(self::$bthomeModels[$modelId]['trv']);
    }

    //Sensoren, die das Modell sendet: Liste von [obj_id, idx], leer bei unbekanntem Modell.
    protected function bthomeModelObjects(int $modelId): array
    {
        return self::$bthomeModels[$modelId]['objects'] ?? [];
    }
}
