<?php

declare(strict_types=1);

require_once __DIR__ . '/../libs/MQTTHelper.php';
require_once __DIR__ . '/../libs/ShellyModels.php';
require_once __DIR__ . '/../libs/DebugHelper.php';
const GUID_SHELLY_DEVICE = '{86104D43-1A2F-EFA8-CB86-EBE8979F8D1A}';

    class ShellyConfigurator extends IPSModule
    {
        use MQTTHelper;
        use ShellyModels;
        use DebugHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
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
        }

        public function GetConfigurationForm()
        {
            $Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
            $this->getShellies();
            if (floatval(IPS_GetKernelVersion()) < 5.3) {
                return json_encode($Form);
            }

            $Shellies = json_decode($this->ReadAttributeString('Shellies'), true); //$this->findShellysOnNetwork();
            $Values = [];

            if (count($Shellies) == 0) {
                $Form['actions'][1]['visible'] = true;
            } else {
                $Form['actions'][1]['visible'] = false;
            }

            if (count($Shellies) > 0) {
                foreach ($Shellies as $key => $Shelly) {
                    $DeviceType = '';
                    $instanceID = $this->getShellyInstances($Shelly['ID']);
                    if ($Shelly['Model'] == '') {
                        $this->LogMessage('Shelly with IP: ' . $Shelly['IP'] . ' has no model! Check firmware updates.', KL_ERROR);
                        continue;
                    }

                    if (array_key_exists($Shelly['Model'], self::$shellyModels)) {
                        $DeviceType = self::$shellyModels[$Shelly['Model']]['Name'];
                    } else {
                        $DeviceType = $this->Translate('Unknown') . ' (' . $Shelly['Model'] . ')';
                    }
                    $AddValue = [
                        'name'                  => $Shelly['ID'],
                        'MQTTTopic'             => $Shelly['ID'],
                        'InstanceName'          => $this->getInstanceName($instanceID),
                        'DeviceType'            => $DeviceType,
                        'IPAddress'             => $Shelly['IP'],
                        'Firmware'              => $Shelly['Firmware'],
                        'instanceID'            => $instanceID,
                        'create'                => [
                            'moduleID'      => '{86104D43-1A2F-EFA8-CB86-EBE8979F8D1A}',
                            'info'          => $Shelly['ID'],
                            'configuration' => [
                                'MQTTTopic' => $Shelly['ID'],
                            ]
                        ]
                    ];

                    $Values[] = $AddValue;
                }
                $Form['actions'][0]['values'] = $Values;
            }
            return json_encode($Form);
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
            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            //Für MQTT Fix in IPS Version 6.3
            if (IPS_GetKernelDate() > 1670886000) {
                $Buffer['Payload'] = utf8_decode($Buffer['Payload']);
            }
            $Shellies = json_decode($this->ReadAttributeString('Shellies'), true); //$this->findShellysOnNetwork();

            if (array_key_exists('Topic', $Buffer)) {
                if ($Buffer['Topic'] == 'shellies/announce') {
                    $Shelly = [];

                    $Payload = json_decode($Buffer['Payload'], true);

                    if (array_key_exists('gen', $Payload)) {
                        if ($Payload['gen'] >= 2) {
                            $foundedKey = array_search($Payload['id'], array_column($Shellies, 'ID'));
                            if ($foundedKey !== false) {
                                $Shellies[$foundedKey]['LastActivity'] = time();
                                $Shellies[$foundedKey]['Model'] = (array_key_exists('model', $Payload)) ? ($Payload['model']) : '';
                                $Shellies[$foundedKey]['MAC'] = $Payload['mac'];
                                if (array_key_exists('gen', $Payload)) {
                                    $Shellies[$foundedKey]['Name'] = $Payload['name'];
                                    $Shellies[$foundedKey]['Firmware'] = $Payload['fw_id'];
                                } else {
                                    $Shellies[$foundedKey]['Firmware'] = $Payload['fw_ver'];
                                    $Shellies[$foundedKey]['IP'] = $Payload['ip'];
                                }
                                $this->WriteAttributeString('Shellies', json_encode($Shellies));
                                return;
                            }
                            $Shelly = [];
                            $Shelly['Name'] = '-';
                            $Shelly['ID'] = $Payload['id'];
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
        private function getShellyInstances($ShellyID)
        {
            $InstanceIDs[] = IPS_GetInstanceListByModuleID(GUID_SHELLY_DEVICE);

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
        private function getInstanceName($ID)
        {
            if ($ID != 0) {
                return IPS_GetObject($ID)['ObjectName'];
            }
            return '';
        }
    }