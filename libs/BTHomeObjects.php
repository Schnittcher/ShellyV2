<?php

declare(strict_types=1);

//BTHome (Shelly BLU-Geräte an einem Gateway): bthomedevice:2xx (das BLU-Gerät) und bthomesensor:2xx (je ein Messwert).
//Typ, Name und Einheit eines Sensors kommen - soweit verfügbar - vom Gerät selbst: BTHome.GetObjectInfos liefert zu
//jeder obj_id obj_name, type und unit (siehe requestBTHomeObjectInfos()). Die Tabellen unten sind nur Rückfall, falls
//das Gerät (noch) nicht geantwortet hat, und liefern die Anzeigetexte für binäre Sensoren (Fenster offen/zu usw.).
//
//AM ECHTEN BLU GATEWAY G3 GEPRÜFT (04.10.2026, nur lesend): Der Zeitstempel heißt last_updated_ts, "value" kommt schon
//umgerechnet an (22.1 statt 221, Batterie 100), BTHome.GetObjectInfos liefert type "sensor" bzw. "button" und
//Einheiten wie "° C". Tasten (obj_id 58, type "button") haben keinen "value", nur den Zeitstempel.
//NOCH OFFEN: der Typ "binary..." für Boolean ist angenommen (am Gateway sind aktuell keine binären Sensoren).
trait BTHomeObjects
{
    //obj_id => [Name, Einheit] - Rückfall für Zahlenwerte (Quelle: BTHome v2 Spezifikation, bthome.io/format)
    private static $bthomeFallbackObjects = [
        1  => ['battery', '%'],
        2  => ['temperature', '°C'],
        3  => ['humidity', '%'],
        4  => ['pressure', 'hPa'],
        5  => ['illuminance', 'lx'],
        8  => ['dewpoint', '°C'],
        9  => ['count', ''],
        10 => ['energy', 'kWh'],
        11 => ['power', 'W'],
        12 => ['voltage', 'V'],
        14 => ['pm10', 'µg/m³'],
        13 => ['pm2.5', 'µg/m³'],
        18 => ['co2', 'ppm'],
        19 => ['tvoc', 'µg/m³'],
        20 => ['moisture', '%'],
        46 => ['humidity', '%'],
        47 => ['moisture', '%'],
        63 => ['rotation', '°'],
        64 => ['distance', 'mm'],
        67 => ['current', 'A'],
        69 => ['temperature', '°C'],
    ];

    //Taster (BTHome-Objekt 58 "button event", Typ "button"): haben keinen Status-Wert, ein Tastendruck kommt als Ereignis
    //(NotifyEvent mit component bthomedevice:N und event z.B. single_push). Die Variable zeigt den letzten Tastendruck.
    private static $bthomeButtonObjectIds = [58];

    //BTHome-Geräte (attrs.model_id aus Shelly.GetComponents), von denen bekannt ist, dass sie eine Taste haben, ohne dass das
    //aus den Sensoren hervorgeht: 23 = Shelly BLU Button Tough 1 ZB. Weitere Geräte bekommen die Variable beim ersten
    //Tastendruck.
    private static $bthomeButtonModelIds = [23];

    //Ereignisname => Anzeigetext der Tasten-Variable
    private static $bthomeButtonEvents = [
        'single_push' => 'Single press',
        'double_push' => 'Double press',
        'triple_push' => 'Triple press',
        'long_push'   => 'Long press',
        'hold_press'  => 'Hold press',
    ];

    //obj_id => [Name, Text für true, Text für false] - binäre Sensoren (BTHome v2 Spezifikation)
    private static $bthomeBinaryObjects = [
        15 => ['generic boolean', 'On', 'Off'],
        16 => ['power', 'On', 'Off'],
        17 => ['opening', 'Open', 'Closed'],
        21 => ['battery low', 'Low', 'OK'],
        22 => ['battery charging', 'Charging', 'Not charging'],
        23 => ['carbon monoxide', 'Detected', 'Clear'],
        24 => ['cold', 'Cold', 'Normal'],
        25 => ['connectivity', 'Connected', 'Disconnected'],
        26 => ['door', 'Open', 'Closed'],
        27 => ['garage door', 'Open', 'Closed'],
        28 => ['gas', 'Detected', 'Clear'],
        29 => ['heat', 'Hot', 'Normal'],
        30 => ['light', 'Light', 'Dark'],
        31 => ['lock', 'Unlocked', 'Locked'],
        32 => ['moisture', 'Wet', 'Dry'],
        33 => ['motion', 'Motion', 'No motion'],
        34 => ['moving', 'Moving', 'Not moving'],
        35 => ['occupancy', 'Occupied', 'Free'],
        36 => ['plug', 'Plugged in', 'Unplugged'],
        37 => ['presence', 'Present', 'Away'],
        38 => ['problem', 'Problem', 'OK'],
        39 => ['running', 'Running', 'Not running'],
        40 => ['safety', 'Unsafe', 'Safe'],
        41 => ['smoke', 'Detected', 'Clear'],
        42 => ['sound', 'Detected', 'Clear'],
        43 => ['tamper', 'Tampered', 'OK'],
        44 => ['vibration', 'Detected', 'Clear'],
        45 => ['window', 'Open', 'Closed'],
    ];

    //Typ der "value"-Werte je Komponenten-Key (boolean/string/float), aus dem Status der Antwort - der Wert eines
    //Sensors kann je nach obj_id Zahl, Text oder Boolean sein, die Definition in components.php ist nur der Standard.
    protected function collectBTHomeValueTypes(array $statusDict)
    {
        $types = [];
        foreach ($statusDict as $key => $status) {
            if (strpos((string) $key, 'bthomesensor:') !== 0 || !is_array($status) || !array_key_exists('value', $status)) {
                continue;
            }
            $value = $status['value'];
            $types[$key] = is_bool($value) ? 'boolean' : (is_string($value) ? 'string' : 'float');
        }
        return $types;
    }

    //Fragt Name/Typ/Einheit der vorkommenden obj_ids beim Gerät ab (BTHome.GetObjectInfos). Wird per
    //RegisterOnceTimer aus ReceiveData() heraus aufgerufen (SendDataToParent() direkt aus ReceiveData() hat den
    //Datenfluss schon einmal blockiert). Die Antwort kommt auf <topic>/getObjectInfos/<InstanzID>/rpc.
    public function RequestBTHomeObjectInfos(): void
    {
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        $objIds = [];
        foreach (is_array($configs) ? $configs : [] as $key => $config) {
            if (strpos((string) $key, 'bthomesensor:') === 0 && isset($config['obj_id'])) {
                $objIds[(int) $config['obj_id']] = true;
            }
        }
        if (count($objIds) == 0) {
            return;
        }
        $topic = $this->ReadPropertyString('MQTTTopic');
        $payload = [
            'id'     => 1,
            'src'    => $topic . '/getObjectInfos/' . $this->InstanceID,
            'method' => 'BTHome.GetObjectInfos',
            'params' => ['obj_ids' => array_keys($objIds)],
        ];
        $this->sendMQTT($topic . '/rpc', json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    //Antwort von BTHome.GetObjectInfos in den Buffer übernehmen (obj_id => [name, type, unit]). Gibt true zurück,
    //wenn Objekte enthalten waren.
    protected function storeBTHomeObjectInfos(array $Payload)
    {
        $objects = $Payload['result']['objects'] ?? null;
        if (!is_array($objects)) {
            return false;
        }
        $infos = json_decode($this->GetBuffer('bthomeObjectInfos'), true);
        $infos = is_array($infos) ? $infos : [];
        foreach ($objects as $object) {
            if (is_array($object) && isset($object['obj_id'])) {
                $infos[(int) $object['obj_id']] = [
                    'name' => (string) ($object['obj_name'] ?? ''),
                    'type' => (string) ($object['type'] ?? ''),
                    'unit' => (string) ($object['unit'] ?? ''),
                ];
            }
        }
        $this->SetBuffer('bthomeObjectInfos', json_encode($infos));
        return true;
    }

    //Gehört die Komponente (bthomesensor:N oder blutrv:N) zum BLU-Gerät (bthomedevice:M)? Die Zuordnung läuft über die
    //MAC-Adresse (Config addr), beim Thermostat zusätzlich über das Config-Feld "trv" (z.B. "bthomedevice:200") -
    //dadurch kann eine Instanz für ein BLU-Gerät automatisch alle seine Sensoren und den Thermostat mit seinen
    //Bedienelementen (Zieltemperatur, Position) anlegen.
    protected function bthomeComponentBelongsToDevice(string $componentKey, string $deviceKey)
    {
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        if (!is_array($configs)) {
            return false;
        }
        if (($configs[$componentKey]['trv'] ?? '') === $deviceKey) {
            return true;
        }
        $addr = (string) ($configs[$componentKey]['addr'] ?? '');
        $deviceAddr = (string) ($configs[$deviceKey]['addr'] ?? '');
        return $addr != '' && strcasecmp($addr, $deviceAddr) == 0;
    }

    //Doppelt der Sensor (bthomesensor:N) eine Variable, die das BLU-Gerät (bthomedevice:M) in seiner Instanz ohnehin hat?
    //- Batterie (obj_id 1): steht schon als bthomedevice.battery in der Instanz.
    //- Thermostat (blutrv am Gerät): Temperatur idx 0 ist die Zieltemperatur und idx 2 die externe Temperatur - beides
    //  steht schon als blutrv.target_C / blutrv.current_C (mit Aktion) in der Instanz. idx 1 ist die gemessene Temperatur.
    protected function bthomeSensorDuplicatesDeviceVariable(string $sensorKey, string $deviceKey)
    {
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        if (!is_array($configs) || !isset($configs[$sensorKey]['obj_id'])) {
            return false;
        }
        $objId = (int) $configs[$sensorKey]['obj_id'];
        if ($objId == 1) {
            return true;
        }
        if ($objId == 69 && in_array((int) ($configs[$sensorKey]['idx'] ?? -1), [0, 2], true)) {
            foreach (array_keys($configs) as $key) {
                if (strpos((string) $key, 'blutrv:') === 0 && $this->bthomeComponentBelongsToDevice((string) $key, $deviceKey)) {
                    return true;
                }
            }
        }
        return false;
    }

    //Merkt den automatisch vergebenen Namen einer Sensor-Variable (nur, wenn die Variable genau so heißt, also nicht
    //vom Nutzer umbenannt wurde).
    protected function rememberBTHomeAutoName(string $ident, string $name)
    {
        $variableID = @$this->GetIDForIdent($ident);
        if (!$variableID || IPS_GetName($variableID) != $name) {
            return;
        }
        $auto = json_decode($this->GetBuffer('bthomeAutoNames'), true);
        $auto = is_array($auto) ? $auto : [];
        $auto[$ident] = $name;
        $this->SetBuffer('bthomeAutoNames', json_encode($auto));
    }

    //Benennt Sensor-Variablen um, die noch ihren automatisch vergebenen Namen tragen, wenn sich der Name durch neu
    //eingetroffene Objektinfos geändert hat. Vom Nutzer geänderte Namen bleiben unberührt.
    protected function syncBTHomeVariableNames()
    {
        $auto = json_decode($this->GetBuffer('bthomeAutoNames'), true);
        $auto = is_array($auto) ? $auto : [];
        foreach ($this->bthomeSensorVariableNames() as $ident => $name) {
            $variableID = @$this->GetIDForIdent($ident);
            if ($variableID && isset($auto[$ident]) && $auto[$ident] != $name && IPS_GetName($variableID) == $auto[$ident]) {
                IPS_SetName($variableID, $name);
                $auto[$ident] = $name;
            }
        }
        $this->SetBuffer('bthomeAutoNames', json_encode($auto));
    }

    //Namen aller Sensor-Variablen (Ident => Name) nach dem aktuellen Stand der Objektinfos.
    protected function bthomeSensorVariableNames()
    {
        $names = [];
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        foreach (is_array($configs) ? array_keys($configs) : [] as $key) {
            if (strpos((string) $key, 'bthomesensor:') !== 0) {
                continue;
            }
            $number = substr((string) $key, strlen('bthomesensor:'));
            $name = $this->getBTHomeSensorVariable((string) $key)['name'];
            $names['bthomesensor_' . $number . '_value'] = $name;
            $names['bthomesensor_' . $number . '_last_updated_ts'] = $name . ' - ' . $this->Translate('Last update');
        }
        return $names;
    }

    //Ist das ein Taster (Typ "button" laut Objektinfos bzw. BTHome-Objekt 58)? $config = Config des Sensors,
    //$info = Eintrag aus BTHome.GetObjectInfos (oder null).
    protected function isBTHomeButtonSensor(array $config, $info)
    {
        if (is_array($info) && strtolower((string) ($info['type'] ?? '')) == 'button') {
            return true;
        }
        return isset($config['obj_id']) && in_array((int) $config['obj_id'], self::$bthomeButtonObjectIds, true);
    }

    //Name des BLU-Geräts (bthomedevice mit gleicher MAC-Adresse), sonst die MAC-Adresse selbst - als Namenspräfix.
    protected function bthomeDevicePrefix(array $configs, string $addr)
    {
        if ($addr == '') {
            return '';
        }
        foreach ($configs as $deviceKey => $deviceConfig) {
            if (strpos((string) $deviceKey, 'bthomedevice:') === 0 && strcasecmp((string) ($deviceConfig['addr'] ?? ''), $addr) == 0 && trim((string) ($deviceConfig['name'] ?? '')) != '') {
                return trim((string) $deviceConfig['name']);
            }
        }
        return $addr;
    }

    //Darstellung der Tasten-Variable (bthomedevice.button): VALUE_PRESENTATION mit den bekannten Tastendrücken.
    protected function bthomeButtonPresentation()
    {
        $options = [];
        foreach (self::$bthomeButtonEvents as $event => $caption) {
            $options[] = ['Value' => $event, 'Caption' => $this->Translate($caption), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1, 'ContentColorActive' => false, 'ContentColorValue' => -1];
        }
        return ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode($options)];
    }

    //Ist der Sensor bthomesensor:N ein Taster (ohne Status-Wert)? Für Taster gibt es keine Sensor-Variablen, der Tastendruck
    //steht in der Variable des BLU-Geräts (bthomedevice.button).
    protected function isBTHomeButtonSensorKey(string $key)
    {
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        $infos = json_decode($this->GetBuffer('bthomeObjectInfos'), true);
        $config = is_array($configs) ? ($configs[$key] ?? null) : null;
        if (!is_array($config)) {
            return false;
        }
        $infos = is_array($infos) ? $infos : [];
        return $this->isBTHomeButtonSensor($config, $infos[(int) ($config['obj_id'] ?? -1)] ?? null);
    }

    //Ereignis eines BLU-Geräts (NotifyEvent: component bthomedevice:N, event z.B. single_push) in die Tasten-Variable des
    //Geräts schreiben. Am echten Gateway kommen Tastendrücke ausschließlich als Ereignis (am Gerät gibt es keinen
    //Status-Wert dafür).
    protected function setBTHomeButtonEvent(array $event)
    {
        $component = (string) ($event['component'] ?? '');
        $eventName = (string) ($event['event'] ?? '');
        if (strpos($component, 'bthomedevice:') !== 0 || $eventName == '') {
            return;
        }
        $ident = 'bthomedevice_' . substr($component, strlen('bthomedevice:')) . '_button';
        //Erster Tastendruck dieses Geräts: ab jetzt gehört die Tasten-Variable dazu (legt die Variable an, sofern diese
        //Instanz das Gerät enthält).
        $seen = json_decode($this->GetBuffer('bthomeButtonDevices'), true);
        $seen = is_array($seen) ? $seen : [];
        if (!in_array($component, $seen, true)) {
            $seen[] = $component;
            $this->SetBuffer('bthomeButtonDevices', json_encode($seen));
            if (!@$this->GetIDForIdent($ident)) {
                $this->rebuildVariables();
            }
        }
        $this->SetValue($ident, $eventName);
    }

    //Soll das BLU-Gerät (bthomedevice:N) die Tasten-Variable bekommen? Ja, wenn es einen Taster-Sensor hat (z.B. der
    //Thermostat), von seinem Modell bekannt ist, dass es eine Taste hat, oder schon ein Tastendruck eingetroffen ist.
    protected function bthomeDeviceShowsButton(string $deviceKey)
    {
        $seen = json_decode($this->GetBuffer('bthomeButtonDevices'), true);
        if (is_array($seen) && in_array($deviceKey, $seen, true)) {
            return true;
        }
        $models = json_decode($this->GetBuffer('bthomeDeviceModels'), true);
        if (is_array($models) && isset($models[$deviceKey]) && in_array((int) $models[$deviceKey], self::$bthomeButtonModelIds, true)) {
            return true;
        }
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        foreach (is_array($configs) ? array_keys($configs) : [] as $key) {
            if (strpos((string) $key, 'bthomesensor:') === 0 && $this->isBTHomeButtonSensorKey((string) $key) && $this->bthomeComponentBelongsToDevice((string) $key, $deviceKey)) {
                return true;
            }
        }
        return false;
    }

    //Gerätemodelle (attrs.model_id) der BLU-Geräte aus der Antwort von Shelly.GetComponents: Komponenten-Key => model_id.
    protected function collectBTHomeDeviceModels(array $components)
    {
        $models = [];
        foreach ($components as $component) {
            if (is_array($component) && isset($component['key'], $component['attrs']['model_id']) && strpos((string) $component['key'], 'bthomedevice:') === 0) {
                $models[(string) $component['key']] = (int) $component['attrs']['model_id'];
            }
        }
        return $models;
    }

    //Typ, Name und Darstellung der Variable "value" eines bthomesensor:N. Rückgabe:
    //['type' => VARIABLETYPE_*, 'name' => string, 'presentation' => array]
    protected function getBTHomeSensorVariable(string $key)
    {
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        $configs = is_array($configs) ? $configs : [];
        $valueTypes = json_decode($this->GetBuffer('bthomeValueTypes'), true);
        $valueTypes = is_array($valueTypes) ? $valueTypes : [];
        $infos = json_decode($this->GetBuffer('bthomeObjectInfos'), true);
        $infos = is_array($infos) ? $infos : [];

        $config = $configs[$key] ?? [];
        $objId = isset($config['obj_id']) ? (int) $config['obj_id'] : -1;
        $info = $infos[$objId] ?? null;
        $binary = self::$bthomeBinaryObjects[$objId] ?? null;
        $fallback = self::$bthomeFallbackObjects[$objId] ?? null;

        //Typ: Boolean, wenn der Wert ein Boolean ist, das Gerät den Typ als "binary..." meldet oder die obj_id ein
        //bekannter binärer Sensor ist; Text, wenn der Wert ein String ist; sonst Zahl.
        $valueType = $valueTypes[$key] ?? null;
        $infoType = strtolower((string) ($info['type'] ?? ''));
        if ($valueType === 'boolean' || strpos($infoType, 'binary') !== false || ($valueType === null && $binary !== null)) {
            $type = VARIABLETYPE_BOOLEAN;
        } elseif ($valueType === 'string') {
            $type = VARIABLETYPE_STRING;
        } else {
            $type = VARIABLETYPE_FLOAT;
        }

        //Name: vom Nutzer vergebener Name, sonst Name des BTHome-Objekts (Gerät, sonst Rückfall-Tabelle).
        $name = trim((string) ($config['name'] ?? ''));
        if ($name == '') {
            $objectName = trim((string) ($info['name'] ?? ''));
            if ($objectName == '') {
                $objectName = $binary !== null ? $binary[0] : ($fallback !== null ? $fallback[0] : 'value');
            }
            $name = $this->Translate(ucfirst(str_replace('_', ' ', $objectName)));
        }
        //Gerätename (bthomedevice mit gleicher MAC-Adresse) bzw. die MAC-Adresse als Präfix, damit die Messwerte
        //mehrerer BLU-Geräte an einem Gateway unterscheidbar sind.
        $prefix = $this->bthomeDevicePrefix($configs, (string) ($config['addr'] ?? ''));
        if ($prefix != '') {
            $name = $prefix . ' - ' . $name;
        }

        $presentation = ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION];
        if ($type == VARIABLETYPE_BOOLEAN) {
            $trueCaption = $binary !== null ? $binary[1] : 'On';
            $falseCaption = $binary !== null ? $binary[2] : 'Off';
            $presentation['OPTIONS'] = json_encode([
                ['Value' => true, 'Caption' => $this->Translate($trueCaption), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1, 'ContentColorActive' => false, 'ContentColorValue' => -1],
                ['Value' => false, 'Caption' => $this->Translate($falseCaption), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1, 'ContentColorActive' => false, 'ContentColorValue' => -1],
            ]);
        } elseif ($type == VARIABLETYPE_FLOAT) {
            //Einheit: vom Gerät, sonst Rückfall-Tabelle ("° C" -> "°C").
            $unit = trim((string) ($info['unit'] ?? ''));
            if ($unit == '' && $fallback !== null) {
                $unit = $fallback[1];
            }
            $unit = str_replace('° ', '°', $unit);
            if ($unit != '') {
                $presentation['SUFFIX'] = ' ' . $unit;
            }
        }

        return ['type' => $type, 'name' => $name, 'presentation' => $presentation];
    }
}
