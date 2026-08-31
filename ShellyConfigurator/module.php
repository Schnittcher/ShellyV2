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
        //$this->SetReceiveDataFilter('.*(getComponentsConfigurator/rpc|getComponentsConfiguratorViaStatus/rpc||/announce).*');

        $BaseTopic = 'shellies';

        //Setze Filter für ReceiveData
        //Die Shelly-ID im "src" (und damit im Antwort-Topic) macht die Antworten pro Gerät
        //unterscheidbar, damit mehrere getComponents-Anfragen parallel laufen können.
        $Filter1 = preg_quote('"Topic":"' . $BaseTopic . '/getComponentsConfigurator/') . '[^"]*' . preg_quote('/rpc"');
        $Filter2 = preg_quote('"Topic":"' . $BaseTopic . '/getComponentsConfiguratorViaStatus/') . '[^"]*' . preg_quote('/rpc"');
        $Filter3 = '"Topic":"[^"]*/announce"';
        $this->SendDebug('Filter', '.*(' . $Filter1 . '|' . $Filter2 . '|' . $Filter3 . ').*', 0);
        $this->SetReceiveDataFilter('.*(' . $Filter1 . '|' . $Filter2 . '|' . $Filter3 . ').*');
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

        $idCount = 0;

        if (count($Shellies) > 0) {
            $idCount++;

            //Phase 1: Für alle Geräte mit gültigem Modell die Anfragen sofort verschicken, ohne zu warten.
            $pendingBufferKeys = [];
            foreach ($Shellies as $Shelly) {
                if (!array_key_exists('App', $Shelly) || $Shelly['Model'] == '') {
                    continue;
                }
                $this->requestComponentsViaStatus($Shelly['ID']);
                $pendingBufferKeys[] = 'LastComponentResponse_' . $Shelly['ID'];

                // ### TEST / EXPERIMENTELL - Dynamisch angelegte Komponenten ###
                // Shelly.GetComponents wird für jedes Gerät abgefragt, da BLU TRVs, Boolean/Number/
                // Enum/Text-Komponenten und presencezone (Shelly Presence) nur darüber auffindbar sind.
                $this->requestComponents($Shelly['ID']);
                $pendingBufferKeys[] = 'LastComponentResponse2_' . $Shelly['ID'];
                // ### ENDE TEST / EXPERIMENTELL ###
            }

            //Phase 2: Einmal gemeinsam auf alle Antworten warten (max. 5 Sekunden insgesamt statt pro Gerät seriell).
            $componentResponses = $this->waitForComponentResponses($pendingBufferKeys, 5);

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

                $shellyComponents = $componentResponses['LastComponentResponse_' . $Shelly['ID']] ?? null;
                if ($shellyComponents != null) {
                    $shellyComponents = json_decode($shellyComponents, true);
                } else {
                    $shellyComponents = [];
                }

                if (array_key_exists($Shelly['Model'], self::$shellyModels)) {
                    $DeviceType = self::$shellyModels[$Shelly['Model']]['Name'];
                } else {
                    $DeviceType = $this->Translate('Unknown') . ' (' . $Shelly['Model'] . ')';
                }

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
                        //Shelly.GetComponents wird für BLU TRVs und dynamisch angelegte Komponenten
                        //gebraucht - beide nutzen dieselbe Antwort, daher einmal zentral decodieren
                        //statt für jeden Zweck erneut.
                        $shellyComponentsFullRaw = $componentResponses['LastComponentResponse2_' . $Shelly['ID']] ?? null;
                        $shellyComponentsFull = ($shellyComponentsFullRaw != null) ? json_decode($shellyComponentsFullRaw, true) : [];
                        $shellyComponentsFull = $this->fetchRemainingComponentsPages($Shelly['ID'], $shellyComponentsFull);
                        $this->SendDebug('Shelly GetComponents', json_encode($shellyComponentsFull), 0);

                        //Ausnahme für Shelly TRV
                        if ($Shelly['App'] == 'BluGwG3' && array_key_exists('result', $shellyComponentsFull)) {
                            $BLUTRVs = $this->getBLUTRVs($shellyComponentsFull['result']);
                            foreach ($BLUTRVs as $key => $BLUTRV) {
                                $cleanedPath = $this->cleanComponentPath($key);
                                $component = $cleanedPath['clean'];
                                $componentChannel = intval($cleanedPath['number']);
                                $componentInstanceID = $this->getShellyComponentInstances($Shelly['ID'], $component, $componentChannel);
                                if ($this->componentDefinitionExists($component)) {
                                    $AddComponent = [
                                        'parent'                    => $idCount,
                                        'name'                      => $key,
                                        'MQTTTopic'                 => $key,
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
                                    $Values[] = $AddComponent;
                                }
                            }
                        }
                        //ENDE Shelly BLUTRV Ausnahme

                        // ### TEST / EXPERIMENTELL - Dynamisch angelegte Komponenten im Configurator ###
                        if (array_key_exists('result', $shellyComponentsFull)) {
                            $dynamicComponents = $this->getDynamicallyAddedComponents($shellyComponentsFull['result']);
                            foreach (array_keys($dynamicComponents['status']) as $key) {
                                $cleanedPath = $this->cleanComponentPath($key);
                                $component = $cleanedPath['clean'];
                                $componentChannel = intval($cleanedPath['number']);
                                $componentInstanceID = $this->getShellyComponentInstances($Shelly['ID'], $component, $componentChannel);

                                //Vom Nutzer auf dem Gerät vergebenen Namen mit anzeigen, z.B. "boolean:200 (Test)".
                                $displayName = $key;
                                $componentName = $dynamicComponents['config'][$key]['name'] ?? '';
                                if ($componentName != '') {
                                    $displayName = $key . ' (' . $componentName . ')';
                                }

                                if ($this->componentDefinitionExists($component)) {
                                    $AddComponent = [
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
                                    $Values[] = $AddComponent;
                                }
                            }
                        }
                        // ### ENDE TEST / EXPERIMENTELL ###

                        if (array_key_exists('result', $shellyComponents)) {
                            foreach ($shellyComponents['result'] as $key => $shellyComponent) {
                                $cleanedPath = $this->cleanComponentPath($key);
                                $component = $cleanedPath['clean'];
                                $componentChannel = intval($cleanedPath['number']);
                                $componentInstanceID = $this->getShellyComponentInstances($Shelly['ID'], $component, $componentChannel);
                                if ($this->componentDefinitionExists($component)) {
                                    $AddComponent = [
                                        'parent'                    => $idCount,
                                        'name'                      => $key,
                                        'MQTTTopic'                 => $key,
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
                                    $Values[] = $AddComponent;
                                }
                            }
                        }
                    }
                }
            }
            $Form['actions'][0]['values'] = $Values;
        }
        return json_encode($Form);
    }

    //Wird aktuell nur für die Shelly BLUTRV genutzt, da diese nicht über getStatus zu finden sind - in getComponents, fehlen dafür Komponenten wie RGB, daher nutze ich weiterhin Shelly.GetStatus um die Komponenten zu finden.
    //Bleibt für Aufrufer, die eine einzelne synchrone Anfrage erwarten, unverändert (sendet + wartet auf genau ein Gerät).
    public function getComponents($ShellyMQTTGTopic)
    {
        $this->requestComponents($ShellyMQTTGTopic);
        $responses = $this->waitForComponentResponses(['LastComponentResponse2_' . $ShellyMQTTGTopic], 5);
        return $responses['LastComponentResponse2_' . $ShellyMQTTGTopic] ?? null;
    }

    /**
    public function getComponentsViaStatus($ShellyMQTTGTopic)
    {
        $this->requestComponentsViaStatus($ShellyMQTTGTopic);
        $responses = $this->waitForComponentResponses(['LastComponentResponse_' . $ShellyMQTTGTopic], 5);
        return $responses['LastComponentResponse_' . $ShellyMQTTGTopic] ?? null;
    }
    */

    //Verschickt nur die Shelly.GetComponents-Anfrage, ohne auf die Antwort zu warten.
    //Die Shelly-ID im "src" macht das Antwort-Topic pro Gerät eindeutig.
    private function requestComponents($ShellyMQTTGTopic, $offset = 0)
    {
        $Topic = $ShellyMQTTGTopic . '/rpc';

        $this->SendDebug(__FUNCTION__, 'Topic: ' . $Topic . ' Offset: ' . $offset, 0);

        $Payload['id'] = 1;
        $Payload['src'] = 'shellies/getComponentsConfigurator/' . $ShellyMQTTGTopic;
        $Payload['method'] = 'Shelly.GetComponents';
        //dynamic_only: Shelly.GetComponents ist paginiert - bei vielen Komponenten (sys/wifi/cloud/...)
        //fielen dynamisch angelegte (BLU TRVs, Boolean/Number/Enum/Text/presencezone/etc.) sonst auf
        //eine spätere Seite und wurden nie abgerufen. Mit dynamic_only kommen von vornherein nur die
        //relevanten Komponenten zurück (mit echten Geräten verifiziert). $offset erlaubt trotzdem das
        //Nachladen weiterer Seiten, falls ein Gerät mehr dynamische Komponenten hat, als auf eine
        //Seite passen - siehe fetchRemainingComponentsPages().
        $Payload['params'] = ['dynamic_only' => true, 'offset' => $offset];
        $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
    }

    //Lädt bei Bedarf synchron weitere Seiten von Shelly.GetComponents nach (falls
    //offset + Anzahl_erhalten < total) und führt sie mit der ersten Seite zusammen. Wird nur für das
    //jeweils EINE Gerät aufgerufen, das mehr als eine Seite braucht - der Normalfall (eine Seite
    //reicht) bleibt so schnell wie bisher (parallel über Phase 1/2).
    private function fetchRemainingComponentsPages($ShellyID, $shellyComponentsFull)
    {
        if (!array_key_exists('result', $shellyComponentsFull) || !array_key_exists('components', $shellyComponentsFull['result'])) {
            return $shellyComponentsFull;
        }

        $components = $shellyComponentsFull['result']['components'];
        $offset = $shellyComponentsFull['result']['offset'] ?? 0;
        $total = $shellyComponentsFull['result']['total'] ?? count($components);
        $receivedSoFar = $offset + count($components);

        while ($receivedSoFar < $total) {
            $this->requestComponents($ShellyID, $receivedSoFar);
            $responses = $this->waitForComponentResponses(['LastComponentResponse2_' . $ShellyID], 5);
            $nextPageRaw = $responses['LastComponentResponse2_' . $ShellyID] ?? null;
            if ($nextPageRaw == null) {
                //Zeitüberschreitung beim Nachladen - mit dem bisher Vorhandenen weitermachen statt zu blockieren.
                break;
            }
            $nextPage = json_decode($nextPageRaw, true);
            if (!array_key_exists('result', $nextPage) || !array_key_exists('components', $nextPage['result']) || count($nextPage['result']['components']) === 0) {
                //Leere Seite trotz offener "total" (z.B. unerwartete Geräteantwort) - abbrechen statt
                //endlos weiterzufragen, mit dem bisher Vorhandenen weitermachen.
                break;
            }
            $components = array_merge($components, $nextPage['result']['components']);
            $offset = $nextPage['result']['offset'] ?? $receivedSoFar;
            $receivedSoFar = $offset + count($nextPage['result']['components']);
        }

        $shellyComponentsFull['result']['components'] = $components;
        return $shellyComponentsFull;
    }

    //Verschickt nur die Shelly.GetStatus-Anfrage, ohne auf die Antwort zu warten.
    private function requestComponentsViaStatus($ShellyMQTTGTopic)
    {
        $Topic = $ShellyMQTTGTopic . '/rpc';

        $this->SendDebug(__FUNCTION__, 'Topic: ' . $Topic, 0);

        $Payload['id'] = 1;
        $Payload['src'] = 'shellies/getComponentsConfiguratorViaStatus/' . $ShellyMQTTGTopic;
        $Payload['method'] = 'Shelly.GetStatus';
        $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
    }

    //Wartet gemeinsam auf mehrere zuvor per requestComponents()/requestComponentsViaStatus() gestellte
    //Anfragen (max. $maxWaitSeconds insgesamt, nicht pro Gerät), statt seriell pro Gerät zu blockieren.
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
            if (preg_match('#^shellies/getComponentsConfiguratorViaStatus/([^/]+)/rpc$#', $Buffer['Topic'], $matches)) {
                $this->SetBuffer('LastComponentResponse_' . $matches[1], $Buffer['Payload']);
            }
            if (preg_match('#^shellies/getComponentsConfigurator/([^/]+)/rpc$#', $Buffer['Topic'], $matches)) {
                $this->SetBuffer('LastComponentResponse2_' . $matches[1], $Buffer['Payload']);
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

    public function setMQTTSettings($selectedValue, $broker, $port, $username, $password)
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
