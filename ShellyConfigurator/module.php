<?php

declare(strict_types=1);

require_once __DIR__ . '/../libs/MQTTHelper.php';

    class ShellyConfigurator extends IPSModule
    {
        
        

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
                    $instanceID = 0;//$this->getShellyInstances($Shelly['ID']);
                    if ($Shelly['Model'] == '') {
                        $this->LogMessage('Shelly with IP: ' . $Shelly['IP'] . ' has no model! Check firmware updates.', KL_ERROR);
                        continue;
                    }

                    $DeviceType = '';
                    $moduleID = '';
                    //if (array_key_exists($Shelly['Model'], self::$DeviceTypes)) {
                    //    $DeviceType = self::$DeviceTypes[$Shelly['Model']]['Name'];
                    //    $moduleID = self::$DeviceTypes[$Shelly['Model']]['GUID'];
                    //} else {
                        $DeviceType = $this->Translate('Unknown') . ' (' . $Shelly['Model'] . ')';
                   //}
                    $AddValue = [
                        'MQTTTopic'             => $Shelly['ID'],
                        'InstanceName'          => '',//$this->getInstanceName($instanceID),
                        'DeviceType'            => $DeviceType,
                        'IPAddress'             => $Shelly['IP'],
                        'Firmware'              => $Shelly['Firmware'],
                        'instanceID'            => $instanceID
                    ];
                    switch ($Shelly['Model']) {
                        case 'SHSW-1':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'Device'    => 'shelly1'
                                ]
                            ];
                            break;
                        case 'SHSW-PM':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'Device'    => 'shelly1pm'
                                ]
                            ];
                            break;
                        case 'SHSW-L':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'Device'    => 'shelly1l'
                                ]
                            ];
                            break;
                        case 'SHSW-21':
                            $AddValue['create'] = [
                                'Shelly 2 Relay' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'  => $Shelly['ID'],
                                        'Device'     => 'shelly2',
                                        'DeviceType' => 'relay'
                                    ]
                                ],
                                'Shelly 2 Shutter' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'  => $Shelly['ID'],
                                        'Device'     => 'shelly2',
                                        'DeviceType' => 'roller'
                                    ]
                                ]
                            ];
                            break;
                        case 'SHSW-25':
                            $AddValue['create'] = [
                                'Shelly 2.5 Relay' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'  => $Shelly['ID'],
                                        'Device'     => 'shelly2.5',
                                        'DeviceType' => 'relay'
                                    ]
                                ],
                                'Shelly 2.5 Shutter' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'  => $Shelly['ID'],
                                        'Device'     => 'shelly2.5',
                                        'DeviceType' => 'roller'
                                    ]
                                ]
                            ];
                            break;
                        case 'SHIX3-1':
                        case 'SHEM':
                        case 'SHEM-3':
                        case 'SHUNI-1':
                        case 'SHTRV-01':
                        case 'SHBTN-1':
                        case 'SHBTN-2':
                        case 'SHPLG2-1':
                        case 'SHPLG-S':
                        case 'SHPLG-1':
                        case 'SHDM-1':
                        case 'SHDM-2':
                        case 'S3DM-0A101WWL':
                        case 'SHVIN-1':
                        case 'SHBLB-1':
                        case 'SHHT-1':
                        case 'SHWT-1':
                        case 'SHGS':
                        case 'SHMOS-01':
                        case 'SHMOS-02':
                        case 'SNSW-002P16EU':
                        case 'SNSW-102P16EU':
                        case 'SNSN-0024X':
                        case 'SNSN-0D24X':
                        case 'SNSN-0013A':
                        case 'S3SN-0U12A':
                        case 'SNSN-0031Z':
                        case 'SNSW-001P8EU':
                        case 'SNPM-001PCEU16':
                        case 'SPSW-002PE16EU':
                        case 'SPSW-002XE16EU':
                        case 'SPSW-202XE16EU':
                        case 'SPSW-003XE16EU':
                        case 'SPEM-003CEBEU':
                        case 'SPEM-003CEBEU120':
                        case 'SPEM-003CEBEU400':
                        case 'SPEM-003CEBEU63':
                        case 'SPEM-002CEBEU50':
                        case 'SPSW-004PE16EU':
                        case 'SPSW-104PE16EU':
                        case 'SNDM-00100WW':
                        case 'S3DM-0010WW':
                        case 'S3DM-0A1WW':
                        case 'SNGW-BT01':
                        case 'S3PM-001PCEU16':
                        case 'S3SW-002P16EU':
                        case 'S3PL-00112EU':
                        case 'S3SN-0024X':
                        case 'SNSN-0043X':
                        case 'SPSH-002PE16EU':
                        case 'SAWD1':
                        case 'S3GW-1DBT001':
                            $AddValue['create'] = [
                                'name'          => $Shelly['ID'],
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID']
                                ]
                            ];
                            break;
                        case 'SHRGBW2':
                            $AddValue['create'] = [
                                'Shelly RGBW2 Color' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'       => $Shelly['ID'],
                                        'DeviceType'      => 'Color'
                                    ]
                                ],
                                'Shelly RGBW2 White' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'       => $Shelly['ID'],
                                        'DeviceType'      => 'White'
                                    ]
                                ]
                            ];
                            break;
                        case 'SNDC-0D4P10WW':
                            $AddValue['create'] = [
                                'Shelly RGBW PM 4 x Light' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'       => $Shelly['ID'],
                                        'Device'          => 'light'
                                    ]
                                ],
                                'Shelly RGBW PM RGB' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'       => $Shelly['ID'],
                                        'Device'          => 'rgb'
                                    ]
                                ],
                                'Shelly RGBW PM RGBW' => [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'       => $Shelly['ID'],
                                        'Device'          => 'rgbw'
                                    ]
                                ]
                            ];
                            break;
                        case 'SHBDUO-1':
                        case 'SHSPOT-1':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'Device'    => 'light'
                                ]
                            ];
                            break;
                        case 'SHCB-1':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID'],
                                    'Device'    => 'color'
                                ]
                            ];
                            break;
                        case 'SHDW-1':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic'  => $Shelly['ID'],
                                    'Device'     => 'DW'
                                ]
                            ];
                            break;
                        case 'SHDW-2':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic'  => $Shelly['ID'],
                                    'Device'     => 'DW2'
                                ]
                            ];
                            break;
                        case 'SNSW-001X16EU':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => strtolower($Shelly['ID']),
                                    'Device'    => 'shellyplus1'
                                ]
                            ];
                            break;
                        case 'SNSW-001P16EU':
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => strtolower($Shelly['ID']),
                                    'Device'    => 'shellyplus1pm'
                                ]
                            ];
                            break;
                        case 'SNPL-00110IT':
                            case 'SNPL-00112EU':
                            case 'SNPL-10112EU':
                            case 'SNPL-00112UK':
                            case 'SNPL-00116US':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'shellyplusplugs'
                                    ]
                                ];
                                break;
                            case 'S3SW-001X16EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'gen3shelly1'
                                    ]
                                ];
                                break;
                            case 'S3SW-001P16EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'gen3shelly1pm'
                                    ]
                                ];
                                break;
                            case 'S3SW-001X8EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'gen3shelly1mini'
                                    ]
                                ];
                                break;
                            case 'S3SW-001P8EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'gen3shelly1pmmini'
                                    ]
                                ];
                                break;
                            case 'SNSW-001X8EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'shellyplus1mini'
                                    ]
                                ];
                                break;
                            case 'SNSW-001P8EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'shellyplus1pmmini'
                                    ]
                                ];
                                break;
                            case 'SNSW-001P8EU':
                            case 'SPSW-201XE16EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'shellypro1'
                                    ]
                                ];
                                break;
                            case 'SPSW-001PE16EU':
                            case 'SPSW-201PE16EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => strtolower($Shelly['ID']),
                                        'Device'    => 'shellypro1pm'
                                    ]
                                ];
                                break;
                            case 'SPDM-001PE01EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => $Shelly['ID'],
                                        'Device'    => 'shellyprodimmer1pm'
                                    ]
                                ];
                                break;
                            case 'SPDM-002PE01EU':
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic' => $Shelly['ID'],
                                        'Device'    => 'shellyprodimmer2pm'
                                    ]
                                ];
                                break;
                            case 'SPSW-202PE16EU': //Eine Version fehlt - fehlt in der Doku von Shelly?!
                                $moduleID = '{A7B9C446-E5C6-4DE9-AF1E-B9FE20FFF3FF}';
                                $DeviceType = 'Shelly Pro 2PM';
                                $AddValue['create'] = [
                                    'moduleID'      => $moduleID,
                                    'info'          => $Shelly['IP'],
                                    'configuration' => [
                                        'MQTTTopic'  => strtolower($Shelly['ID']),
                                        'Device'     => 'shellypro2pm',
                                        'DeviceType' => 'relay'
                                    ]
                                ];
                                break;
                        case 'shellysense': // model id unbekannt
                            $moduleID = '{F86F268B-BC23-41AC-B107-16EEF661A4D7}';
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID']
                                ]
                            ];
                            break;
                        case 'shellysmoke': // model id unbekannt
                            $moduleID = '{88A5611C-CD57-4255-9F57-E420CE784C81}';
                            $DeviceType = 'Shelly Smoke';
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID']
                                ]
                            ];
                            break;
                        case 'shellyair': // model id unbekannt
                            $moduleID = '{55840D9D-BB28-4D66-91B5-66C8859FAE83}';
                            $DeviceType = 'Shelly Air';
                            $AddValue['create'] = [
                                'moduleID'      => $moduleID,
                                'info'          => $Shelly['IP'],
                                'configuration' => [
                                    'MQTTTopic' => $Shelly['ID']
                                ]
                            ];
                            break;
                        default:
                            $this->SendDebug(__FUNCTION__ . ' DeviceType', 'Invalid Device Type:' . $Shelly['Model'], 0);
                            break;
                        }

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
                $this->WriteAttributeString('Shellies', json_encode($Shellies));
            }
        }
    }