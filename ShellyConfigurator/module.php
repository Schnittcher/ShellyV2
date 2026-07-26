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
        $Filter1 = preg_quote('"Topic":"' . $BaseTopic . '/getComponentsConfigurator/rpc"');
        $Filter2 = preg_quote('"Topic":"' . $BaseTopic . '/getComponentsConfiguratorViaStatus/rpc"');
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

                $shellyComponents = $this->getComponentsViaStatus($Shelly['ID']);
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
                        //Ausnahme für Shelly TRV
                        if ($Shelly['App'] == 'BluGwG3') {
                            $shellyComponentsTRV = $this->getComponents($Shelly['ID']);
                            if ($shellyComponentsTRV != null) {
                                $shellyComponentsTRV = json_decode($shellyComponentsTRV, true);
                            } else {
                                $shellyComponentsTRV = [];
                            }
                            $this->SendDebug('Shelly TRV Components', json_encode($shellyComponentsTRV), 0);
                            if (array_key_exists('result', $shellyComponentsTRV)) {
                                $BLUTRVs = $this->getBLUTRVs($shellyComponentsTRV['result']);
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
                        }
                        //ENDE Shelly BLUTRV Ausnahme

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
    public function getComponents($ShellyMQTTGTopic)
    {
        $Topic = $ShellyMQTTGTopic . '/rpc';

        $this->SendDebug(__FUNCTION__, 'Topic: ' . $Topic, 0);

        $Payload['id'] = 1;
        $Payload['src'] = 'shellies/getComponentsConfigurator';
        $Payload['method'] = 'Shelly.GetComponents';
        $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));

        //$this->sendMQTT($Topic, json_encode($Payload));

        $start = microtime(true);
        do {
            $value = $this->GetBuffer('LastComponentResponse2');
            if ($value != '') {
                $this->SetBuffer('LastComponentResponse2', ''); // Reset
                return $value;
            }
            IPS_Sleep(100); // 100ms warten
        } while ((microtime(true) - $start) < 5);
    }

    public function getComponentsViaStatus($ShellyMQTTGTopic)
    {
        $Topic = $ShellyMQTTGTopic . '/rpc';

        $this->SendDebug(__FUNCTION__, 'Topic: ' . $Topic, 0);

        $Payload['id'] = 1;
        $Payload['src'] = 'shellies/getComponentsConfiguratorViaStatus';
        $Payload['method'] = 'Shelly.GetStatus';
        $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));

        $start = microtime(true);
        do {
            $value = $this->GetBuffer('LastComponentResponse');
            if ($value != '') {
                $this->SetBuffer('LastComponentResponse', ''); // Reset
                return $value;
            }
            IPS_Sleep(100); // 100ms warten
        } while ((microtime(true) - $start) < 5);
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
            if (fnmatch('shellies/getComponentsConfiguratorViaStatus/rpc', $Buffer['Topic'])) {
                $this->SetBuffer('LastComponentResponse', $Buffer['Payload']);
            }
            if (fnmatch('shellies/getComponentsConfigurator/rpc', $Buffer['Topic'])) {
                $this->SetBuffer('LastComponentResponse2', $Buffer['Payload']);
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

        $IPAddress = $selectedValue['IPAddress'];
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
                'IPAddress'               => $Shelly['IPv4'],
                'App'                     => '',
                'Firmware'                => '',
                'Generation'              => '',
            ];
        }
        return $Values;
    }

    private function mdnsSearch()
    {
        $mDNSInstanceIDs = IPS_GetInstanceListByModuleID('{780B2D48-916C-4D59-AD35-5A429B2355A5}');
        $resultServiceTypes = ZC_QueryServiceType($mDNSInstanceIDs[0], '_shelly._tcp', 'local');
        $shellies = [];
        foreach ($resultServiceTypes as $key => $device) {
            $shelly = [];
            $deviceInfo = ZC_QueryService($mDNSInstanceIDs[0], $device['Name'], '_shelly._tcp', 'local.');
            //print_r($deviceInfo);
            $shelly['Hostname'] = $device['Name'];
            if (!empty($deviceInfo)) {
                $shelly['Port'] = $deviceInfo[0]['Port'] ?? null;
                $shelly['IPv6'] = $deviceInfo[0]['IPv6'][0] ?? null;
                $shelly['IPv4'] = $deviceInfo[0]['IPv4'][0] ?? null;
            }
            array_push($shellies, $shelly);
        }
        return $shellies;
    }
}
