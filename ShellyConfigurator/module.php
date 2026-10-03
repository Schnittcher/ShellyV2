<?php

declare(strict_types=1);

require_once __DIR__ . '/../libs/MQTTHelper.php';
require_once __DIR__ . '/../libs/ShellyRPCHelper.php';
require_once __DIR__ . '/../libs/ShellyModels.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/DebugHelper.php';
require_once __DIR__ . '/../libs/components.php';
require_once __DIR__ . '/../libs/ComponentDefinitionHelper.php';
const GUID_SHELLY_DEVICE = '{86104D43-1A2F-EFA8-CB86-EBE8979F8D1A}';
const GUID_SHELLY_XT1DEVICE = '{88774A56-2453-2EEC-24F5-BBC37D63B506}';
const GUID_SHELLY_COMOPONENT_DEVICE = '{50980B9E-BB37-7C7A-FDBD-A823BC53C8EF}';

class ShellyConfigurator extends IPSModule
{
    use Components;
    use ComponentDefinitionHelper;
    use MQTTHelper;
    use ShellyModels;
    use DebugHelper;
    use ShellyRPCHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        if (IPS_GetKernelVersion() < 8.2) {
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
        }
        $this->RegisterAttributeString('Shellies', '{}');
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $BaseTopic = 'shellies';

        //Setze Filter für ReceiveData
        //Die Shelly-ID im "src" (und damit im Antwort-Topic) macht die Antworten pro Gerät
        //unterscheidbar, damit mehrere GetComponents-Anfragen parallel laufen können. Ein zusätzliches
        //"dyn/" im Topic trennt die Abfrage der dynamischen Komponenten von der der Schlüsselliste (der
        //Filter bleibt dafür unverändert, da er nur in ApplyChanges() gesetzt wird).
        $Filter1 = preg_quote('"Topic":"' . $BaseTopic . '/getComponentsConfigurator/') . '[^"]*' . preg_quote('/rpc"');
        $Filter2 = '"Topic":"[^"]*/announce"';
        $this->SendDebug('Filter', '.*(' . $Filter1 . '|' . $Filter2 . ').*', 0);
        $this->SetReceiveDataFilter('.*(' . $Filter1 . '|' . $Filter2 . ').*');
    }

    public function GetConfigurationForm()
    {
        $Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $this->getShellies();
        if (floatval(IPS_GetKernelVersion()) < 5.3) {
            return json_encode($Form);
        }
        $Form['actions'][2]['values'] = $this->getFormForMdnsDevices();

        $Shellies = json_decode($this->ReadAttributeString('Shellies'), true); //$this->findShellysOnNetwork();
        $Values = [];

        if (count($Shellies) == 0) {
            $Form['actions'][3]['visible'] = true;
        } else {
            $Form['actions'][3]['visible'] = false;
        }

        //Eindeutige ID je Gerätezeile - die Komponenten verweisen darüber per "parent" auf ihr Gerät.
        $idCount = 0;

        if (count($Shellies) > 0) {
            //Phase 1: Komponentenlisten aller Geräte mit gültigem Modell einsammeln. Shelly.GetComponents ist die
            //einzige Quelle (inkl. BLU TRVs, dynamischer Komponenten und Add-on-Sensoren wie bei The Pill, die in
            //Shelly.GetStatus fehlen). Die Antworten sind paginiert - siehe collectComponents().
            $deviceIDs = [];
            foreach ($Shellies as $Shelly) {
                if (!array_key_exists('App', $Shelly) || $Shelly['Model'] == '') {
                    continue;
                }
                $deviceIDs[] = $Shelly['ID'];
            }
            //Zusätzlich (parallel) für die Namen der dynamischen Komponenten, z.B. "boolean:200 (Test)": dynamic_only inkl. Config.
            $collected = $this->collectComponents($deviceIDs);
            $componentLists = $collected['keys'];
            $dynamicComponentLists = $collected['dynamic'];

            //Phase 3: Formular wie gewohnt aufbauen, jetzt aus den bereits eingesammelten Antworten.
            foreach ($Shellies as $key => $Shelly) {
                $DeviceType = '';
                if (array_key_exists('App', $Shelly)) {
                    $instanceID = $this->getShellyInstances($Shelly['ID'], $Shelly['App']);
                } else {
                    $this->SendDebug('Shelly App Key not exists', $Shelly, 0);
                    continue;
                }

                if ($Shelly['Model'] == '') {
                    $this->LogMessage('Shelly with IP: ' . $Shelly['IP'] . ' has no model! Check firmware updates.', KL_ERROR);
                    continue;
                }

                if (array_key_exists($Shelly['Model'], self::$shellyModels)) {
                    $DeviceType = self::$shellyModels[$Shelly['Model']]['Name'];
                } else {
                    $DeviceType = $this->Translate('Unknown') . ' (' . $Shelly['Model'] . ')';
                }

                $idCount++;

                if ($Shelly['App'] == 'XT1') {
                    $Values[] = [
                        'id'                    => $idCount,
                        'name'                  => $Shelly['ID'],
                        'MQTTTopic'             => $Shelly['ID'],
                        'InstanceName'          => $this->getInstanceName($instanceID),
                        'DeviceType'            => $DeviceType,
                        'IPAddress'             => $Shelly['IP'],
                        'App'                   => $Shelly['App'] ?? '',
                        'Firmware'              => $Shelly['Firmware'],
                        'instanceID'            => $instanceID,
                        'create'                => [
                            'ST802' => [
                                'moduleID'      => GUID_SHELLY_XT1DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic'       => $Shelly['ID'],
                                    'XMODServiceType' => 'linkedgo-st-802-hvac'
                                ]
                            ],
                            'ST1820' => [
                                'moduleID'      => GUID_SHELLY_XT1DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic'       => $Shelly['ID'],
                                    'XMODServiceType' => 'linkedgo-st1820-floor-thermostat'
                                ]
                            ],
                            'Smart Water Valve' => [
                                'moduleID'      => GUID_SHELLY_XT1DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic'       => $Shelly['ID'],
                                    'XMODServiceType' => 'simple-water-valve-controller'
                                ]
                            ],
                            'Neo Smart Water Valve' => [
                                'moduleID'      => GUID_SHELLY_XT1DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic'       => $Shelly['ID'],
                                    'XMODServiceType' => 'neo-water-valve-advanced'
                                ]
                            ],
                            'EV Charger' => [
                                'moduleID'      => GUID_SHELLY_XT1DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic'       => $Shelly['ID'],
                                    'XMODServiceType' => 'shelly-ev-charger'
                                ]
                            ],
                            //TEST/EXPERIMENTELL: XT1-Geräte zusätzlich auch als generisches
                            //ShellyDevice anbieten (nicht nur mit XMODServiceType) - seit der
                            //Migration auf Shelly.GetComponents (siehe TODO/ROADMAP in
                            //ShellyModuleBase.php) funktioniert der generische Pfad auch für XT1-Geräte
                            //wie z.B. die Smart WaterValve, ohne dass eine feste XMODServices-Definition
                            //nötig wäre.
                            'ShellyDevice (generisch)' => [
                                'moduleID'      => GUID_SHELLY_DEVICE,
                                'info'          => $Shelly['ID'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'ModelID'   => $Shelly['Model'],
                                ]
                            ]
                        ]
                    ];
                } else {
                    $Values[] = [
                        'id'                    => $idCount,
                        'name'                  => $Shelly['ID'],
                        'MQTTTopic'             => $Shelly['ID'],
                        'InstanceName'          => $this->getInstanceName($instanceID),
                        'DeviceType'            => $DeviceType,
                        'IPAddress'             => $Shelly['IP'],
                        'App'                   => $Shelly['App'] ?? '',
                        'Firmware'              => $Shelly['Firmware'],
                        'instanceID'            => $instanceID,
                        'create'                => [
                            'moduleID'      => GUID_SHELLY_DEVICE,
                            'info'          => $Shelly['ID'],
                            'configuration' => [
                                'MQTTTopic' => $Shelly['ID'],
                                'ModelID'   => $Shelly['Model'],
                            ]
                        ]
                    ];

                    if (array_key_exists('App', $Shelly)) {
                        //Vom Nutzer auf dem Gerät vergebene Namen der dynamischen Komponenten (Boolean/Number/
                        //Enum/Text/...) mit anzeigen, z.B. "boolean:200 (Test)".
                        $dynamicComponentNames = $this->getDynamicallyAddedComponents(['components' => $dynamicComponentLists[$Shelly['ID']] ?? []])['config'];

                        foreach ($componentLists[$Shelly['ID']] ?? [] as $shellyComponent) {
                            if (!isset($shellyComponent['key'])) {
                                continue;
                            }
                            $key = $shellyComponent['key'];
                            $cleanedPath = $this->cleanComponentPath($key);
                            $component = $cleanedPath['clean'];
                            if (!$this->componentDefinitionExists($component)) {
                                continue;
                            }
                            $componentChannel = intval($cleanedPath['number']);
                            $componentInstanceID = $this->getShellyComponentInstances($Shelly['ID'], $component, $componentChannel);

                            $displayName = $key;
                            $componentName = $dynamicComponentNames[$key]['name'] ?? '';
                            if ($componentName != '') {
                                $displayName = $key . ' (' . $componentName . ')';
                            }

                            $Values[] = [
                                'parent'                    => $idCount,
                                'name'                      => $displayName,
                                'MQTTTopic'                 => $displayName,
                                'InstanceName'              => $this->getInstanceName($componentInstanceID),
                                'DeviceType'                => '',
                                'IPAddress'                 => '',
                                'App'                       => '',
                                'Firmware'                  => '',
                                'instanceID'                => $componentInstanceID,
                                'create'                    => [
                                    'moduleID'      => GUID_SHELLY_COMOPONENT_DEVICE,
                                    'info'          => $Shelly['ID'],
                                    'configuration' => [
                                        'MQTTTopic' => $Shelly['ID'],
                                        'Component' => $component,
                                        'Channel'   => $componentChannel,
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
            $Form['actions'][0]['values'] = $Values;
        }
        return json_encode($Form);
    }

    //Sammelt für alle übergebenen Geräte die Komponenten per Shelly.GetComponents - und zwar zwei Listen
    //gleichzeitig: 'keys' (Schlüssel ALLER Komponenten) und 'dynamic' (nur die dynamischen inkl. Config, für
    //deren Namen). Die Antworten sind paginiert (die Seitengröße bestimmt das Gerät), deshalb wird
    //rundenweise jeweils die nächste Seite für ALLE noch unvollständigen Abfragen gleichzeitig verschickt und
    //gemeinsam abgewartet, statt Gerät für Gerät oder Liste für Liste seriell.
    //Rückgabe: ['keys' => [Shelly-ID => [Komponenteneinträge, ...]], 'dynamic' => [...]]
    private function collectComponents(array $shellyIDs)
    {
        $components = ['' => [], 'Dynamic' => []];
        $nextOffset = [];
        $pending = [];
        foreach (['', 'Dynamic'] as $kind) {
            foreach ($shellyIDs as $id) {
                $pending[] = [$kind, $id];
                $nextOffset[$kind . '_' . $id] = 0;
            }
        }

        //Obergrenze der Runden als Schutz vor Endlosschleifen bei unerwarteten Geräteantworten.
        for ($round = 0; $round < 20 && count($pending) > 0; $round++) {
            $bufferKeys = [];
            foreach ($pending as [$kind, $id]) {
                $job = $kind . '_' . $id;
                $bufferKeys[$job] = 'LastComponentResponse' . $job;
                //Verspätete Antworten früherer Aufrufe verwerfen, bevor neu angefragt wird.
                $this->SetBuffer($bufferKeys[$job], '');
                $this->requestComponents($id, $nextOffset[$job], $kind == 'Dynamic');
            }
            $responses = $this->waitForComponentResponses(array_values($bufferKeys), 5);

            $stillPending = [];
            foreach ($pending as [$kind, $id]) {
                $job = $kind . '_' . $id;
                $raw = $responses[$bufferKeys[$job]] ?? null;
                if ($raw == null) {
                    //Zeitüberschreitung - mit dem bisher Vorhandenen weitermachen statt zu blockieren.
                    continue;
                }
                $decoded = json_decode($raw, true);
                $result = is_array($decoded) ? ($decoded['result'] ?? null) : null;
                $page = is_array($result) ? ($result['components'] ?? []) : [];
                if (count($page) === 0) {
                    //Fehler oder leere Seite - abbrechen statt endlos weiterzufragen.
                    continue;
                }
                $offset = $result['offset'] ?? $nextOffset[$job];
                if ($offset != $nextOffset[$job]) {
                    //Seite gehört nicht zur aktuellen Anfrage (doppelt/verspätet) - dieselbe Seite erneut anfragen.
                    $stillPending[] = [$kind, $id];
                    continue;
                }
                $components[$kind][$id] = array_merge($components[$kind][$id] ?? [], $page);
                $nextOffset[$job] = $offset + count($page);
                if ($nextOffset[$job] < ($result['total'] ?? 0)) {
                    $stillPending[] = [$kind, $id];
                }
            }
            $pending = $stillPending;
        }

        $this->SendDebug(__FUNCTION__, json_encode($components), 0);
        return ['keys' => $components[''], 'dynamic' => $components['Dynamic']];
    }

    //Verschickt nur eine Seite der Shelly.GetComponents-Anfrage, ohne auf die Antwort zu warten.
    //Die Shelly-ID im "src" macht das Antwort-Topic pro Gerät eindeutig.
    private function requestComponents($ShellyMQTTGTopic, $offset, bool $dynamicOnly)
    {
        $Topic = $ShellyMQTTGTopic . '/rpc';

        $this->SendDebug(__FUNCTION__, 'Topic: ' . $Topic . ' Offset: ' . $offset . ($dynamicOnly ? ' (dynamic_only)' : ''), 0);

        $Payload['id'] = 1;
        $Payload['src'] = 'shellies/getComponentsConfigurator/' . ($dynamicOnly ? 'dyn/' : '') . $ShellyMQTTGTopic;
        $Payload['method'] = 'Shelly.GetComponents';
        //Ohne "include" liefert das Gerät Status UND Config je Komponente mit (und damit nur wenige
        //Komponenten pro Seite, bei älteren Firmwares 4) - mit "include": [] kommen nur die Schlüssel,
        //typischerweise alle Komponenten auf einer Seite.
        $Payload['params'] = $dynamicOnly
            ? ['dynamic_only' => true, 'include' => ['config'], 'offset' => $offset]
            : ['include' => [], 'offset' => $offset];
        $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
    }

    //Wartet gemeinsam auf mehrere zuvor per requestComponents() gestellte Anfragen (max. $maxWaitSeconds
    //insgesamt, nicht pro Gerät), statt seriell pro Gerät zu blockieren.
    private function waitForComponentResponses(array $bufferKeys, float $maxWaitSeconds)
    {
        $responses = [];
        if (empty($bufferKeys)) {
            return $responses;
        }

        $start = microtime(true);
        do {
            foreach ($bufferKeys as $bufferKey) {
                if (isset($responses[$bufferKey])) {
                    continue;
                }
                $value = $this->GetBuffer($bufferKey);
                if ($value != '') {
                    $responses[$bufferKey] = $value;
                    $this->SetBuffer($bufferKey, ''); // Reset
                }
            }
            if (count($responses) >= count($bufferKeys)) {
                break;
            }
            IPS_Sleep(100); // 100ms warten
        } while ((microtime(true) - $start) < $maxWaitSeconds);

        return $responses;
    }

    public function getShellies()
    {
        $Shellies = json_decode($this->ReadAttributeString('Shellies'), true);

        foreach ($Shellies as $key => $Shelly) {
            if ($Shelly['LastActivity'] + 86400 < time()) {
                unset($Shellies[$key]);
                $Shellies = array_values($Shellies);
            }

            $this->WriteAttributeString('Shellies', json_encode($Shellies));
        }

        if ($this->HasActiveParent()) {
            $this->sendMQTT('shellies/command', 'announce');
        }
    }

    public function ReceiveData($JSONString)
    {
        $this->SendDebug('JSONString', $JSONString, 0);
        $Buffer = json_decode($JSONString, true);
        $this->SendDebug('JSON', $Buffer, 0);

        //Für MQTT Fix in IPS Version 6.3
        if (IPS_GetKernelDate() > 1670886000) {
            $Buffer['Payload'] = utf8_decode($Buffer['Payload']);
        }
        $Shellies = json_decode($this->ReadAttributeString('Shellies'), true);

        if (array_key_exists('Topic', $Buffer)) {
            //Die Shelly-ID steckt als mittleres Topic-Segment drin (aus dem "src" der Anfrage),
            //damit Antworten mehrerer parallel angefragter Geräte unterscheidbar sind.
            if (preg_match('#^shellies/getComponentsConfigurator/(dyn/)?([^/]+)/rpc$#', $Buffer['Topic'], $matches)) {
                $this->SetBuffer('LastComponentResponse' . ($matches[1] != '' ? 'Dynamic' : '') . '_' . $matches[2], $Buffer['Payload']);
            }

            if (strpos($Buffer['Topic'], '/announce') !== false) {
                $Shelly = [];

                $parts = explode('/announce', $Buffer['Topic'], 2);
                $MQTTTopic = $parts[0];
                if ($MQTTTopic == 'shellies') {
                    return;
                }

                $Payload = json_decode($Buffer['Payload'], true);

                if (array_key_exists('gen', $Payload)) {
                    if ($Payload['gen'] >= 2) {
                        $foundedKey = array_search($MQTTTopic, array_column($Shellies, 'ID'));
                        if ($foundedKey !== false) {
                            $Shellies[$foundedKey]['LastActivity'] = time();
                            $Shellies[$foundedKey]['Model'] = (array_key_exists('model', $Payload)) ? ($Payload['model']) : '';
                            $Shellies[$foundedKey]['MAC'] = $Payload['mac'];
                            if (array_key_exists('gen', $Payload)) {
                                $Shellies[$foundedKey]['Name'] = $Payload['name'];
                                $Shellies[$foundedKey]['Firmware'] = $Payload['fw_id'];
                                $Shellies[$foundedKey]['App'] = $Payload['app'];
                            } else {
                                $Shellies[$foundedKey]['Firmware'] = $Payload['fw_ver'];
                                $Shellies[$foundedKey]['IP'] = $Payload['ip'];
                                $Shellies[$foundedKey]['App'] = '';
                            }
                            $this->WriteAttributeString('Shellies', json_encode($Shellies));
                            return;
                        }
                        $Shelly = [];
                        $Shelly['Name'] = '-';
                        $Shelly['ID'] = $MQTTTopic; //$Payload['id'];
                        //$Shelly['Model'] = $Payload['model'];
                        $Shelly['Model'] = (array_key_exists('model', $Payload)) ? ($Payload['model']) : '';
                        $Shelly['MAC'] = $Payload['mac'];
                        $Shelly['IP'] = '-';
                        $Shelly['Gen'] = 'gen1';
                        $Shelly['LastActivity'] = time();

                        if (array_key_exists('gen', $Payload)) {
                            $Shelly['Name'] = $Payload['name'];
                            $Shelly['Firmware'] = $Payload['fw_id'];
                            $Shelly['Gen'] = $Payload['gen'];
                        } else {
                            $Shelly['Firmware'] = $Payload['fw_ver'];
                            $Shelly['IP'] = $Payload['ip'];
                        }
                        array_push($Shellies, $Shelly);
                    }
                }
            }
            $this->WriteAttributeString('Shellies', json_encode($Shellies));
        }
    }

    public function setMQTTSettings(string $selectedValue, string $broker, int $port, string $username, string $password)
    {
        $selectedValue = json_decode($selectedValue, true);
        //IPS_LogMessage('SelectedValue', print_r($selectedValue, true));

        //IP-Adresse wird erst hier bei Bedarf per mDNS aufgelöst, statt beim Formular-Öffnen für jedes
        //gefundene Gerät (siehe mdnsSearch()/getFormForMdnsDevices()).
        $IPAddress = $this->resolveMdnsIPAddress($selectedValue['name']);
        if ($IPAddress == null) {
            $this->LogMessage('Shelly device with hostname: ' . $selectedValue['name'] . ' could not be resolved via mDNS.', KL_ERROR);
            return;
        }

        $method = 'MQTT.SetConfig';
        $params = [
            'config' => [
                'enable'   => true,
                'server'   => $broker,
                'port'     => $port,
                'user'     => $username,
                'pass'     => $password,
            ]
        ];
        $result = $this->ShellyRPCviaHTTP($IPAddress, $method, $params, $timeout = 5);

        if ($result['result']['restart_required'] == true) {
            $this->LogMessage('Shelly device with IP: ' . $IPAddress . ' will restart to apply MQTT settings.', KL_NOTIFY);
            $result = $this->ShellyRPCviaHTTP($IPAddress, 'Shelly.Reboot', [], $timeout = 5);
            $this->UpdateFormField('ShellyMQTTSettingsInfo', 'visible', true);
        }
    }
    private function getShellyInstances($ShellyID, $App)
    {
        $InstanceIDs[] = IPS_GetInstanceListByModuleID(GUID_SHELLY_DEVICE);

        $InstanceIDsXT1[] = IPS_GetInstanceListByModuleID(GUID_SHELLY_XT1DEVICE);

        if ($App == 'XT1') {
            foreach ($InstanceIDsXT1 as $IDs) {
                foreach ($IDs as $id) {
                    if (strtolower(IPS_GetProperty($id, 'MQTTTopic')) == strtolower($ShellyID)) {
                        if (IPS_GetInstance($id)['ConnectionID'] === IPS_GetInstance($this->InstanceID)['ConnectionID']) {
                            return $id;
                        }
                    }
                }
            }
        }

        foreach ($InstanceIDs as $IDs) {
            foreach ($IDs as $id) {
                if (strtolower(IPS_GetProperty($id, 'MQTTTopic')) == strtolower($ShellyID)) {
                    if (IPS_GetInstance($id)['ConnectionID'] === IPS_GetInstance($this->InstanceID)['ConnectionID']) {
                        return $id;
                    }
                }
            }
        }
        return 0;
    }

    private function getShellyComponentInstances($ShellyID, $Comopnent, $Channel)
    {
        $InstanceIDs[] = IPS_GetInstanceListByModuleID(GUID_SHELLY_COMOPONENT_DEVICE);

        foreach ($InstanceIDs as $IDs) {
            foreach ($IDs as $id) {
                if (strtolower(IPS_GetProperty($id, 'MQTTTopic')) == strtolower($ShellyID) && (strtolower(IPS_GetProperty($id, 'Component')) == strtolower($Comopnent)) && (IPS_GetProperty($id, 'Channel')) == $Channel) {
                    if (IPS_GetInstance($id)['ConnectionID'] === IPS_GetInstance($this->InstanceID)['ConnectionID']) {
                        return $id;
                    }
                }
            }
        }
        return 0;
    }

    private function getInstanceName($ID)
    {
        if ($ID != 0) {
            return IPS_GetObject($ID)['ObjectName'];
        }
        return '';
    }

    private function getFormForMdnsDevices()
    {
        $shellies = $this->mdnsSearch();
        $Values = [];
        foreach ($shellies as $key => $Shelly) {
            $Values[] = [

                'name'                    => $Shelly['Hostname'],
                //IP wird erst beim Speichern (setMQTTSettings) aufgelöst, siehe resolveMdnsIPAddress().
                'IPAddress'               => '',
                'App'                     => '',
                'Firmware'                => '',
                'Generation'              => '',
            ];
        }
        return $Values;
    }

    //Nur der Namens-Browse (schnell, ein Aufruf für alle Geräte). Die eigentliche IP-Auflösung
    //(ZC_QueryService) passiert bewusst nicht mehr hier für jedes gefundene Gerät, da das bei vielen
    //Shellys das Öffnen des Konfigurators spürbar verlangsamt - siehe resolveMdnsIPAddress().
    private function mdnsSearch()
    {
        $mDNSInstanceIDs = IPS_GetInstanceListByModuleID('{780B2D48-916C-4D59-AD35-5A429B2355A5}');
        $resultServiceTypes = ZC_QueryServiceType($mDNSInstanceIDs[0], '_shelly._tcp', 'local');
        $shellies = [];
        foreach ($resultServiceTypes as $key => $device) {
            $shellies[] = ['Hostname' => $device['Name']];
        }
        return $shellies;
    }

    //Löst die IP-Adresse für genau einen Hostnamen per mDNS auf - wird erst bei Bedarf
    //(beim Speichern der MQTT-Einstellungen) aufgerufen, nicht mehr für jedes gefundene Gerät beim Formular-Öffnen.
    private function resolveMdnsIPAddress($hostname)
    {
        $mDNSInstanceIDs = IPS_GetInstanceListByModuleID('{780B2D48-916C-4D59-AD35-5A429B2355A5}');
        $deviceInfo = ZC_QueryService($mDNSInstanceIDs[0], $hostname, '_shelly._tcp', 'local.');
        if (!empty($deviceInfo)) {
            return $deviceInfo[0]['IPv4'][0] ?? null;
        }
        return null;
    }
}
