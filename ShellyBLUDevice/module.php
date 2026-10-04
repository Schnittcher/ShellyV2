<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

//Ein Shelly BLU-Gerät (BTHome), das an einem oder mehreren Gateways (BLU Gateway, Shelly mit Bluetooth-Gateway) angelernt ist.
//Das Gerät wird über seine MAC-Adresse erkannt (am Gateway hat es je Gateway eine andere Komponentennummer). Die Instanz
//fragt alle Gateways ab und führt die Ergebnisse zusammen: Messwerte vom Gateway mit dem neuesten Zeitstempel, der Tastendruck
//von jedem Gateway (doppelte Meldungen derselben Taste werden verworfen), RSSI und "zuletzt gehört" je Gateway sowie das
//Gateway mit dem stärksten Signal ("Stärkstes Gateway", z.B. zur Raumerkennung). Die Nummerierung (Idents) kommt vom
//Haupt-Gateway (Property MQTTTopic); danach läuft alles über die Basisklasse wie bei einer einzelnen bthomedevice-Instanz.
class ShellyBLUDevice extends ShellyModuleBase
{
    public function Create(): void
    {
        parent::Create();
        //Die Basisklasse liest "Component" für die Auswahl der Variablen - im Formular nicht änderbar.
        $this->RegisterPropertyString('Component', 'bthomedevice');
        $this->RegisterPropertyString('BLUAddress', '');
        $this->RegisterPropertyString('AdditionalGateways', '[]');
        $this->RegisterPropertyInteger('NearestMaxAge', 900);
    }

    public function GetConfigurationForm(): string
    {
        $Form = json_decode((string) file_get_contents(__DIR__ . '/form.json'), true);
        foreach ($Form['elements'] as &$element) {
            if (($element['name'] ?? '') == 'VariableList') {
                $element['values'] = json_decode($this->GetBuffer('variableList'), true) ?: [];
            }
        }
        unset($element);
        return json_encode($Form);
    }

    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();

        $topics = $this->gateways();
        if (count($topics) > 0) {
            $this->SetReceiveDataFilter('.*(' . implode('|', array_map('preg_quote', $topics)) . ').*');
        }
        //Daten von Gateways, die nicht mehr eingetragen sind, verwerfen.
        $data = json_decode($this->GetBuffer('bluData'), true);
        if (is_array($data)) {
            $this->SetBuffer('bluData', json_encode(array_intersect_key($data, array_flip($topics))));
        }
        $this->maintainGatewayVariables($topics);

        if ($this->HasActiveParent() && count($topics) > 0 && $this->bluAddress() != '') {
            $this->requestComponentsStatus();
        }
    }

    //HINWEIS: Der seitenweise Abruf (hier und in handleComponentsPage()) ist die Variante je Gateway des Abrufs in ShellyModuleBase
    //(requestComponentsStatus()/ReceiveData()/RunNextComponentsPageAsync()) - gleiche Logik (Sperre 15 s, Seitenlimit 30, Folgeseiten per
    //Timer), nur mit Buffern je Gateway. Änderungen an der Seitenlogik in beiden Klassen machen.
    //
    //Fragt alle Gateways ab (jedes mit eigener Antwort-Adresse und eigenem Abrufzustand).
    public function requestComponentsStatus(): void
    {
        if ($this->bluAddress() == '') {
            $this->SendDebug('requestComponentsStatus', 'Keine MAC-Adresse eingetragen.', 0);
            return;
        }
        foreach ($this->gateways() as $topic) {
            //Läuft für dieses Gateway schon ein Abruf, wird kein zweiter gestartet (siehe ShellyModuleBase::requestComponentsStatus()).
            $started = (float) $this->GetBuffer('bluRun_' . md5($topic));
            if ($started > 0 && (microtime(true) - $started) < 15) {
                continue;
            }
            $this->SetBuffer('bluRun_' . md5($topic), (string) microtime(true));
            $this->SetBuffer('bluAcc_' . md5($topic), json_encode([]));
            $this->SetBuffer('bluPages_' . md5($topic), '0');
            $this->requestGatewayPage($topic, 0);
        }
    }

    //Öffentlicher Einstiegspunkt für den per RegisterOnceTimer() registrierten Timer: nächste Seite für ein Gateway
    //(entkoppelt von ReceiveData(), siehe ShellyModuleBase::RunNextComponentsPageAsync()).
    public function RunNextGatewayPageAsync(string $Topic): void
    {
        $this->requestGatewayPage($Topic, (int) $this->GetBuffer('bluNext_' . md5($Topic)));
    }

    public function ReceiveData(string $JSONString): string
    {
        $Buffer = json_decode($JSONString, true);
        $topic = (string) ($Buffer['Topic'] ?? '');
        $gateway = $this->gatewayOfTopic($topic);
        if ($gateway === null) {
            return '';
        }
        $rest = substr($topic, strlen($gateway));

        //Antwort auf eine KVS-Anfrage an das Haupt-Gateway (siehe libs/ShellyKVS.php).
        if (strpos($rest, '/kvs/') === 0) {
            $this->storeKVSResponse($topic, $this->decodeMQTTPayload($Buffer));
            return '';
        }

        if ($rest == '/online') {
            $this->handleOnline($gateway, json_decode($this->decodeMQTTPayload($Buffer), true));
            return '';
        }
        //Antwort auf BTHome.GetObjectInfos kommt vom Haupt-Gateway und wird von der Basisklasse verarbeitet.
        if ($rest == '/getObjectInfos/' . $this->InstanceID . '/rpc') {
            return $gateway === $this->gateways()[0] ? parent::ReceiveData($JSONString) : '';
        }

        $Payload = json_decode($this->decodeMQTTPayload($Buffer), true);
        if (!is_array($Payload)) {
            return '';
        }
        if ($rest == '/getComponents/' . $this->InstanceID . '/rpc') {
            $this->handleComponentsPage($gateway, $Payload);
        } elseif ($rest == '/events/rpc') {
            $this->handleEvents($gateway, $Payload);
        } elseif ($rest == '/getBTHomeStatus/' . $this->InstanceID . '/rpc') {
            //Status des Geräts nach einem Tastendruck: wie eine Statusmeldung des Gateways behandeln.
            $this->handleEvents($gateway, ['params' => $this->bthomeStatusParams($Payload)]);
        }
        return '';
    }

    //Nach einem Tastendruck den Status des Geräts bei allen Gateways abfragen (je Gateway mit dessen eigener Komponentennummer).
    public function RefreshBTHomeDevice(): void
    {
        $keys = json_decode($this->GetBuffer('bthomeRefreshKeys'), true);
        $this->SetBuffer('bthomeRefreshKeys', '[]');
        $keyMap = json_decode($this->GetBuffer('bluKeyMap'), true);
        if (!is_array($keys) || count($keys) == 0 || !is_array($keyMap)) {
            return;
        }
        foreach ($keyMap as $gateway => $map) {
            $localKeys = [];
            foreach ($map as $localKey => $mainKey) {
                if (in_array($mainKey, $keys, true) && strpos((string) $localKey, 'bthomedevice:') === 0) {
                    $localKeys[] = (string) $localKey;
                }
            }
            if (count($localKeys) > 0) {
                $this->requestBTHomeDeviceStatus((string) $gateway, $localKeys);
            }
        }
    }

    //Kanal (Nummer des bthomedevice am Haupt-Gateway) - ergibt sich aus der MAC-Adresse, nicht aus einer Property.
    protected function componentChannel()
    {
        return (int) $this->GetBuffer('bluChannel');
    }

    //Haupt-Gateway zuerst, danach die weiteren (ohne Doppelte und leere Einträge).
    private function gateways(): array
    {
        $topics = [];
        $main = trim($this->ReadPropertyString('MQTTTopic'));
        if ($main != '') {
            $topics[] = $main;
        }
        $additional = json_decode($this->ReadPropertyString('AdditionalGateways'), true);
        foreach (is_array($additional) ? $additional : [] as $row) {
            $topic = trim((string) ($row['MQTTTopic'] ?? ''));
            if ($topic != '' && !in_array($topic, $topics, true)) {
                $topics[] = $topic;
            }
        }
        return $topics;
    }

    private function bluAddress(): string
    {
        return strtolower(trim($this->ReadPropertyString('BLUAddress')));
    }

    //Zu welchem Gateway gehört das Topic? (Bei Topics, die mit einem anderen Gateway beginnen, gewinnt das längste.)
    private function gatewayOfTopic(string $topic): ?string
    {
        $found = null;
        foreach ($this->gateways() as $gateway) {
            if (strpos($topic, $gateway . '/') === 0 && ($found === null || strlen($gateway) > strlen($found))) {
                $found = $gateway;
            }
        }
        return $found;
    }

    private function requestGatewayPage(string $topic, int $offset): void
    {
        $Payload['id'] = 1;
        $Payload['src'] = $topic . '/getComponents/' . $this->InstanceID;
        $Payload['method'] = 'Shelly.GetComponents';
        $Payload['params'] = ['include' => ['status', 'config'], 'offset' => $offset];
        $this->sendMQTT($topic . '/rpc', json_encode($Payload, JSON_UNESCAPED_SLASHES));
    }

    //Seite einer Shelly.GetComponents-Antwort eines Gateways: sammeln, bei der letzten Seite die Komponenten des
    //BLU-Geräts (gleiche MAC) merken und alle Gateways zusammenführen.
    private function handleComponentsPage(string $gateway, array $Payload): void
    {
        $h = md5($gateway);
        if ((float) $this->GetBuffer('bluRun_' . $h) <= 0) {
            return;
        }
        if (!isset($Payload['result']['components']) || !is_array($Payload['result']['components'])) {
            return;
        }
        $accumulated = json_decode($this->GetBuffer('bluAcc_' . $h), true) ?: [];
        $accumulated = array_merge($accumulated, $Payload['result']['components']);
        $this->SetBuffer('bluAcc_' . $h, json_encode($accumulated));

        $offset = $Payload['result']['offset'] ?? 0;
        $total = $Payload['result']['total'] ?? count($accumulated);
        $received = $offset + count($Payload['result']['components']);
        $pages = (int) $this->GetBuffer('bluPages_' . $h) + 1;
        $this->SetBuffer('bluPages_' . $h, (string) $pages);

        if ($received < $total && count($Payload['result']['components']) > 0 && $pages < 30) {
            $this->SetBuffer('bluNext_' . $h, (string) $received);
            $this->RegisterOnceTimer('GwNextPage_' . $h, 'SHY_RunNextGatewayPageAsync($_IPS["TARGET"], "' . addslashes($gateway) . '");');
            return;
        }

        $this->SetBuffer('bluRun_' . $h, '0');
        $this->SetBuffer('bluAcc_' . $h, json_encode([]));
        $this->SetBuffer('bluPages_' . $h, '0');

        $data = json_decode($this->GetBuffer('bluData'), true);
        $data = is_array($data) ? $data : [];
        $data[$gateway] = $this->componentsOfDevice($accumulated, $this->bluAddress());
        $this->SetBuffer('bluData', json_encode($data));
        $this->mergeAndApply();
    }

    //Nur die Komponenten des BLU-Geräts: bthomedevice und bthomesensor mit dieser MAC-Adresse.
    private function componentsOfDevice(array $components, string $mac): array
    {
        $result = [];
        foreach ($components as $component) {
            if (!is_array($component) || !isset($component['key'])) {
                continue;
            }
            $key = (string) $component['key'];
            if ((strpos($key, 'bthomedevice:') === 0 || strpos($key, 'bthomesensor:') === 0) && strtolower((string) ($component['config']['addr'] ?? '')) == $mac) {
                $result[] = $component;
            }
        }
        return $result;
    }

    //Führt die Ergebnisse aller Gateways zusammen und gibt sie in der Form eines einzelnen Gateways an die Basisklasse:
    //Komponenten und Nummern vom Haupt-Gateway, je Messwert der neueste Status aller Gateways.
    private function mergeAndApply(): void
    {
        $gateways = $this->gateways();
        $data = json_decode($this->GetBuffer('bluData'), true);
        $data = is_array($data) ? $data : [];
        $main = $gateways[0] ?? '';
        if (!isset($data[$main])) {
            return;
        }
        $mainEntries = $data[$main];
        $mainDevice = null;
        foreach ($mainEntries as $entry) {
            if (strpos((string) $entry['key'], 'bthomedevice:') === 0) {
                $mainDevice = $entry;
                break;
            }
        }
        if ($mainDevice === null) {
            $this->SendDebug('mergeAndApply', 'Das Gerät ' . $this->bluAddress() . ' ist am Haupt-Gateway ' . $main . ' nicht angelernt.', 0);
            return;
        }
        $this->SetBuffer('bluChannel', (string) substr((string) $mainDevice['key'], strlen('bthomedevice:')));

        //Je Gateway: Gerät und Sensoren (Sensoren über obj_id und idx zugeordnet).
        $deviceOf = [];
        $sensorsOf = [];
        foreach ($gateways as $gateway) {
            foreach ($data[$gateway] ?? [] as $entry) {
                if (strpos((string) $entry['key'], 'bthomedevice:') === 0) {
                    $deviceOf[$gateway] = $entry;
                } else {
                    $sensorsOf[$gateway][((int) ($entry['config']['obj_id'] ?? -1)) . '_' . ((int) ($entry['config']['idx'] ?? 0))] = $entry;
                }
            }
        }

        //RSSI und "zuletzt gehört" je Gateway.
        $state = [];
        foreach ($deviceOf as $gateway => $entry) {
            $state[$gateway] = ['rssi' => (int) ($entry['status']['rssi'] ?? 0), 'seen' => (int) ($entry['status']['last_updated_ts'] ?? 0)];
        }
        $this->SetBuffer('bluGwState', json_encode($state));

        //Gerät: Nummer vom Haupt-Gateway, Status vom Gateway mit dem neuesten Zeitstempel (RSSI vom stärksten Gateway).
        $merged = [];
        $keyMap = [];
        $lastTs = [];
        $deviceKey = (string) $mainDevice['key'];
        $modelId = (int) ($mainDevice['attrs']['model_id'] ?? 0);
        $best = $mainDevice;
        $keyMap[$main][$deviceKey] = $deviceKey;
        foreach ($gateways as $gateway) {
            if ($gateway === $main || !isset($deviceOf[$gateway])) {
                continue;
            }
            $keyMap[$gateway][(string) $deviceOf[$gateway]['key']] = $deviceKey;
            $modelId = $modelId > 0 ? $modelId : (int) ($deviceOf[$gateway]['attrs']['model_id'] ?? 0);
            if ((int) ($deviceOf[$gateway]['status']['last_updated_ts'] ?? 0) > (int) ($best['status']['last_updated_ts'] ?? 0)) {
                $best = $deviceOf[$gateway];
            }
        }
        $deviceEntry = $mainDevice;
        $deviceEntry['status'] = $best['status'] ?? [];
        $nearest = $this->nearestGateway($state);
        if ($nearest !== '') {
            $deviceEntry['status']['rssi'] = $state[$nearest]['rssi'];
        }
        $lastTs[$deviceKey] = (int) ($deviceEntry['status']['last_updated_ts'] ?? 0);
        $merged[] = $deviceEntry;

        //Sensoren: je (obj_id, idx) der neueste Status aller Gateways. Die Nummern der Sensoren sind je Gateway verschieden,
        //deshalb bekommen sie feste Schlüssel aus obj_id und idx (bthomesensor:<obj_id * 100 + idx>) - dadurch bleiben die Idents
        //der Variablen stabil. Dazu Platzhalter für die Sensoren, die das Modell laut Tabelle (libs/BTHomeModels.php) sendet,
        //auch wenn noch kein Gateway sie gemeldet hat: die Variablen gibt es von Anfang an (z.B. Fenster, Luftfeuchtigkeit).
        $candidates = [];
        foreach ($gateways as $gateway) {
            foreach ($sensorsOf[$gateway] ?? [] as $index => $entry) {
                $candidates[$index][$gateway] = $entry;
            }
        }
        foreach ($this->bthomeModelObjects($modelId) as [$objId, $idx]) {
            if (!isset($candidates[$objId . '_' . $idx])) {
                $candidates[$objId . '_' . $idx] = [];
            }
        }
        ksort($candidates);
        foreach ($candidates as $index => $byGateway) {
            [$objId, $idx] = array_map('intval', explode('_', (string) $index));
            $code = $objId * 100 + $idx;
            $sensorKey = 'bthomesensor:' . $code;
            $best = null;
            foreach ($gateways as $gateway) {
                $candidate = $byGateway[$gateway] ?? null;
                if ($candidate === null) {
                    continue;
                }
                $keyMap[$gateway][(string) $candidate['key']] = $sensorKey;
                if ($best === null || (int) ($candidate['status']['last_updated_ts'] ?? 0) > (int) ($best['status']['last_updated_ts'] ?? 0)) {
                    $best = $candidate;
                }
            }
            if ($best === null) {
                $entry = [
                    'key'    => $sensorKey,
                    'status' => ['id' => $code, 'value' => null, 'last_updated_ts' => 0],
                    'config' => ['id' => $code, 'addr' => $this->bluAddress(), 'name' => null, 'meta' => null, 'obj_id' => $objId, 'idx' => $idx],
                ];
            } else {
                $entry = $best;
                $entry['key'] = $sensorKey;
                $entry['status']['id'] = $code;
                $entry['config']['id'] = $code;
            }
            $lastTs[$sensorKey] = (int) ($entry['status']['last_updated_ts'] ?? 0);
            $merged[] = $entry;
        }
        $this->SetBuffer('bluKeyMap', json_encode($keyMap));
        $this->SetBuffer('bluLastTs', json_encode($lastTs));

        $this->applyComponentsResult($merged);
        $this->updateGatewayVariables($state);
    }

    //Statusmeldungen und Ereignisse eines Gateways (NotifyStatus/NotifyEvent): auf die Komponenten des Haupt-Gateways
    //übersetzen und nur übernehmen, wenn sie neuer sind als der bekannte Wert. Ein Tastendruck-Ereignis liefert außerdem die
    //aktuellen Sensorwerte des Geräts ("sensors") mit.
    private function handleEvents(string $gateway, array $Payload): void
    {
        if (!isset($Payload['params']) || !is_array($Payload['params'])) {
            return;
        }
        $keyMap = json_decode($this->GetBuffer('bluKeyMap'), true);
        $keyMap = is_array($keyMap) ? ($keyMap[$gateway] ?? []) : [];
        if (count($keyMap) == 0) {
            return;
        }
        $lastTs = json_decode($this->GetBuffer('bluLastTs'), true);
        $lastTs = is_array($lastTs) ? $lastTs : [];
        $state = json_decode($this->GetBuffer('bluGwState'), true);
        $state = is_array($state) ? $state : [];

        $params = $Payload['params'];
        $events = is_array($params['events'] ?? null) ? $params['events'] : [];
        //Sensorwerte aus den Ereignissen wie eine Statusmeldung behandeln; die Batterie (obj_id 1) setzt zusätzlich die
        //Batterie-Variable des Geräts (der Batterie-Sensor selbst hat in dieser Instanz keine Variable).
        $batteryKeys = [];
        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }
            foreach ($this->bthomeEventSensors($event) as $sensor) {
                $localKey = 'bthomesensor:' . $sensor['id'];
                $status = ['id' => $sensor['id']];
                if (isset($sensor['value'])) {
                    $status['value'] = $sensor['value'];
                }
                if (isset($sensor['last_updated_ts'])) {
                    $status['last_updated_ts'] = $sensor['last_updated_ts'];
                }
                $params[$localKey] = $status;
                if ($sensor['obj_id'] == 1) {
                    $batteryKeys[$localKey] = true;
                }
            }
        }

        $translated = [];
        $battery = null;
        $stateChanged = false;
        foreach ($params as $key => $status) {
            if (!is_array($status) || !isset($keyMap[$key])) {
                continue;
            }
            $mainKey = $keyMap[$key];
            $ts = isset($status['last_updated_ts']) ? (int) $status['last_updated_ts'] : null;
            if (strpos((string) $key, 'bthomedevice:') === 0) {
                //Empfang dieses Gateways merken, den RSSI-Wert der Instanz setzt updateGatewayVariables().
                $state[$gateway] = [
                    'rssi' => (int) ($status['rssi'] ?? ($state[$gateway]['rssi'] ?? 0)),
                    'seen' => $ts ?? time(),
                ];
                $stateChanged = true;
                unset($status['rssi']);
            }
            if ($ts !== null && $ts < ($lastTs[$mainKey] ?? 0)) {
                continue;
            }
            if ($ts !== null) {
                $lastTs[$mainKey] = $ts;
            }
            $translated[$mainKey] = $status;
            if (isset($batteryKeys[$key]) && isset($status['value'])) {
                $battery = (int) $status['value'];
            }
        }
        $this->SetBuffer('bluLastTs', json_encode($lastTs));
        if (count($translated) > 0) {
            $this->parsePayloadIntoVariables($translated);
        }
        if ($battery !== null) {
            $this->SetValue('bthomedevice_' . $this->componentChannel() . '_battery', $battery);
        }
        if ($stateChanged) {
            $this->SetBuffer('bluGwState', json_encode($state));
            $this->updateGatewayVariables($state);
        }

        //Tastendrücke: von jedem Gateway, doppelte Meldungen derselben Taste (zwei Gateways hören das gleiche Paket) verwerfen.
        foreach ($events as $event) {
            if (!is_array($event) || !isset($keyMap[(string) ($event['component'] ?? '')])) {
                continue;
            }
            $ts = isset($event['ts']) ? (float) $event['ts'] : microtime(true);
            if ($this->isDuplicateButtonEvent($gateway, (int) ($event['idx'] ?? 0) . ':' . (string) ($event['event'] ?? '') . ':' . (int) ($event['steps'] ?? 0), $ts)) {
                continue;
            }
            $event['component'] = $keyMap[(string) $event['component']];
            //Die Sensor-IDs gelten nur für dieses Gateway und wurden oben schon übersetzt übernommen.
            unset($event['sensors']);
            $this->setBTHomeButtonEvent($event);
        }
    }

    //Derselbe Tastendruck erreicht jedes Gateway mit (fast) demselben Zeitstempel (im Test höchstens 0,3 s Unterschied). Als Doppelte
    //gelten gleiche Ereignisse von einem anderen Gateway innerhalb von 0,8 s nach dem zuletzt gezählten; zwei Drücke hintereinander
    //liegen weiter auseinander. Fehlt der Zeitstempel, zählt der Empfangszeitpunkt.
    private function isDuplicateButtonEvent(string $gateway, string $event, float $ts): bool
    {
        $last = json_decode($this->GetBuffer('bluLastButton'), true);
        $duplicate = is_array($last) && ($last['event'] ?? '') === $event && ($last['gateway'] ?? '') !== $gateway && abs($ts - (float) ($last['ts'] ?? 0)) <= 0.8;
        if (!$duplicate) {
            $this->SetBuffer('bluLastButton', json_encode(['event' => $event, 'gateway' => $gateway, 'ts' => $ts]));
        }
        return $duplicate;
    }

    //Erreichbar: online, solange mindestens ein Gateway online ist.
    private function handleOnline(string $gateway, mixed $payload): void
    {
        $online = json_decode($this->GetBuffer('bluOnline'), true);
        $online = is_array($online) ? $online : [];
        $online[$gateway] = (bool) $payload;
        $this->SetBuffer('bluOnline', json_encode($online));
        $any = in_array(true, $online, true);
        $this->SetValue('Reachable', $any);
        if (!$any) {
            $this->zeroingValues();
        }
    }

    //Gateway mit dem stärksten Signal unter denen, die das Gerät in den letzten NearestMaxAge Sekunden gehört haben;
    //gibt es keines, das zuletzt gehörte. Leer, wenn noch kein Gateway etwas gemeldet hat.
    private function nearestGateway(array $state): string
    {
        $maxAge = max(60, $this->ReadPropertyInteger('NearestMaxAge'));
        $best = '';
        foreach ($state as $gateway => $info) {
            if ($info['seen'] > 0 && (time() - $info['seen']) <= $maxAge && ($best === '' || $info['rssi'] > $state[$best]['rssi'])) {
                $best = (string) $gateway;
            }
        }
        if ($best === '') {
            foreach ($state as $gateway => $info) {
                if ($info['seen'] > 0 && ($best === '' || $info['seen'] > $state[$best]['seen'])) {
                    $best = (string) $gateway;
                }
            }
        }
        return $best;
    }

    //Variablen je Gateway (RSSI und "zuletzt gehört") sowie "Stärkstes Gateway"; nicht mehr eingetragene Gateways entfallen.
    private function maintainGatewayVariables(array $topics): void
    {
        $wanted = [];
        foreach ($topics as $topic) {
            $suffix = substr(md5($topic), 0, 8);
            $wanted[] = 'GwRssi_' . $suffix;
            $wanted[] = 'GwSeen_' . $suffix;
            $this->RegisterVariableInteger('GwRssi_' . $suffix, $this->Translate('RSSI') . ' (' . $topic . ')', [
                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                'SUFFIX'       => ' dBm',
            ], 0);
            $this->RegisterVariableInteger('GwSeen_' . $suffix, $this->Translate('Last update') . ' (' . $topic . ')', [
                'PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME,
                'DATE'         => 1,
                'TIME'         => 2,
            ], 0);
        }
        $this->RegisterVariableString('NearestGateway', $this->Translate('Strongest gateway'), [
            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
        ], 0);
        foreach (IPS_GetChildrenIDs($this->InstanceID) as $child) {
            $ident = IPS_GetObject($child)['ObjectIdent'];
            if ((strpos($ident, 'GwRssi_') === 0 || strpos($ident, 'GwSeen_') === 0) && !in_array($ident, $wanted, true)) {
                $this->UnregisterVariable($ident);
            }
        }
    }

    private function updateGatewayVariables(array $state): void
    {
        foreach ($state as $gateway => $info) {
            $suffix = substr(md5((string) $gateway), 0, 8);
            $this->SetValue('GwRssi_' . $suffix, $info['rssi']);
            $this->SetValue('GwSeen_' . $suffix, $info['seen']);
        }
        $nearest = $this->nearestGateway($state);
        if ($nearest !== '') {
            $this->SetValue('NearestGateway', $nearest);
            //RSSI des Geräts ist das Signal des stärksten Gateways.
            $this->SetValue('bthomedevice_' . $this->componentChannel() . '_rssi', $state[$nearest]['rssi']);
        }
    }
}
