<?php

declare(strict_types=1);

require_once __DIR__ . '/../libs/MQTTHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/DebugHelper.php';
require_once __DIR__ . '/../libs/components.php';
require_once __DIR__ . '/../libs/ComponentDefinitionHelper.php';

    class ShellyDevice extends IPSModule
    {
        use MQTTHelper;
        use DebugHelper;
        use Components;
        use ComponentDefinitionHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('MQTTTopic', '');
            $this->RegisterAttributeString('Components', '');
            $this->RegisterPropertyBoolean('DebugMissingIdents', false);

            $this->RegisterVariableBoolean('Reachable', $this->Translate('Reachable'), [
                'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                'OPTIONS'         => json_encode([
                    [
                        'Value'       => true,
                        'Caption'     => 'Online',
                        'IconActive'  => false,
                        'Icon'        => 'Information',
                        'ColorActive' => true,
                        'Color'       => 65280
                    ],
                    [
                        'Value'       => false,
                        'Caption'     => 'Offline',
                        'IconActive'  => false,
                        'Icon'        => 'Information',
                        'ColorActive' => true,
                        'Color'       => 16711680,
                    ],
                ]
                    )
            ], 99);
        }

        public function RequestAction($Ident, $Value)
        {
            //Um den originalen Ident zu behalten, zum Beispeil für actionWithExtraVariable
            $originalIdent = $Ident;
            $Ident = preg_replace('/_?ExtraAction/', '', $Ident);

            $IdentKeyPath = $this->convertIdentToKeyPath($Ident);
            $tmpComponents = $this->getValueByKeyPath($IdentKeyPath[0]);

            if (strpos($originalIdent, 'ExtraAction') !== false) {
                if (array_key_exists('actionWithExtraVariable', $tmpComponents)) {
                    $tmpComponents = $tmpComponents['actionWithExtraVariable'];
                }
            }

            if (array_key_exists('list', $tmpComponents['action'])) {
                $tmpComponents['action']['method'] = $tmpComponents['action']['method'] . $Value;
            }

            // 1. Hole alle Keys als Array
            $keys = array_keys($tmpComponents['action']['params']);
            $tmpComponents['action']['params'][$keys[0]] = $IdentKeyPath[1];

            if (count($keys) > 1) {
                $tmpComponents['action']['params'][$keys[1]] = $Value;
                //Ausnahme für RGB
                if ($IdentKeyPath[0] == 'rgb.rgb.0') {
                    $rgb = json_decode($Value, true);
                    $tmpComponents['action']['params'][$keys[1]] = array_values($rgb);
                    IPS_LogMessage('rgb action', print_r($tmpComponents, true));
                }
            }

            $this->callRPCFunction($tmpComponents['action']['method'], $tmpComponents['action']['params']);
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges()
        {
            //Neverdelete this line!
            parent::ApplyChanges();
            $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
            $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');

            if ($MQTTTopic != '') {
                $this->getComponents();
            }
        }

        public function ReceiveData($JSONString)
        {
            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            $Payload = json_decode($Buffer['Payload'], true);
            if (array_key_exists('Topic', $Buffer)) {
                if (fnmatch('*/online', $Buffer['Topic'])) {
                    $this->SetValue('Reachable', $Payload);
                }
                if (fnmatch('getComponents/rpc', $Buffer['Topic'])) {
                    $tmpComponents = $Payload['result'];
                    $allComponentsFromShelly = $this->getArrayLeafKeyPaths($tmpComponents);
                    $this->WriteAttributeString('Components', json_encode($allComponentsFromShelly));

                    //IPS_LogMessage('test', print_r($allComponentsFromShelly, true));

                    //$allComponentsFromDefinition = $this->getArrayKeyPaths(self::$components);
                    // Duplikate entfernen
                    $allComponentsFromShelly = array_unique($allComponentsFromShelly);
                    //$allComponentsFromDefinition = array_unique($allComponentsFromDefinition);

                    // Vergleich
                    //$commonKeys = array_intersect($allComponentsFromShelly, $allComponentsFromDefinition);

                    //IPS_LogMessage('All Keys', print_r($commonKeys, true));

                    foreach ($allComponentsFromShelly as $value) {
                        $this->registerComponentVariables($allComponentsFromShelly);
                    }
                }
                if (fnmatch('*/events/rpc', $Buffer['Topic'])) {
                    if (array_key_exists('params', $Payload)) {
                        //Components vom Shelly Params Payload holen.
                        $components = $this->getArrayLeafKeyPaths($Payload['params']);
                        foreach ($components as $key => $component) {
                            //IPS_LogMessage('component', print_r($component, true));
                            //Clean Path holen
                            $componentsFromShellyResult = $this->cleanComponentPath($component);
                            //Mit clean keypath Value vom self::components array holen
                            $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);

                            //IPS_LogMessage('test', print_r($componentsFromShellyResult, true));
                            //Value vom Patams array holen mit dem originalen keypath
                            $value = $this->getValueByKeyPathFromArray($Payload['params'], $componentsFromShellyResult['original']);
                            //ggf. umrechnung druchführen
                            if ($tmpComponent != null) {
                                if (array_key_exists('factor', $tmpComponent)) {
                                    $this->SendDebug('Factor calculation', 'Factor: ' . $tmpComponent['factor'], 0);
                                    $value = $value * $tmpComponent['factor'];
                                }
                            }

                            //Ausnahme RGB
                            if ($componentsFromShellyResult['clean'] == 'rgb.rgb.0') {
                                $value = json_encode([
                                    'r' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][0],
                                    'g' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][1],
                                    'b' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][2]
                                ]);
                            }

                            $this->SetValue($componentsFromShellyResult['ident'], $value);
                            //IPS_LogMessage('test', print_r($componentsFromShellyResult, true));
                        }
                    }
                }
            }
        }

        public function getComponents()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'getComponents';
            $Payload['method'] = 'Shelly.GetStatus';
            //$Payload['params'] = ['id' => $switch, 'on' => $value];

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        public function callRPCFunction($method, $params)
        {
            //IPS_LogMessage('method', print_r($method, true));
            //IPS_LogMessage('params', print_r($params, true));

            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = $method;
            $Payload['params'] = $params; //['id' => $switch, 'on' => $value];

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        protected function SetValue($Ident, $Value)
        {
            if (@$this->GetIDForIdent($Ident)) {
                $this->SendDebug('SetValue :: ' . $Ident, $Value, 0);
                parent::SetValue($Ident, $Value);
            } else {
                if ($this->ReadPropertyBoolean('DebugMissingIdents')) {
                    $this->SendDebug('Missing Ident :: Value', $Ident . ' :: ' . $Value, 0);
                }
            }
        }

        private function registerComponentVariables($allComponentsFromShelly)
        {
            foreach ($allComponentsFromShelly as $entry) {
                $componentsFromShellyResult = $this->cleanComponentPath($entry);
                //IPS_LogMessage('register', print_r($componentsFromShellyResult, true));
                $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);
                if ($tmpComponent != null) {
                    $name = $tmpComponent['name'];
                    if ($componentsFromShellyResult['number'] > 0) {
                        $name = $tmpComponent['name'] . ' ' . $componentsFromShellyResult['number'];
                    }
                    switch ($tmpComponent['type']) {
                case VARIABLETYPE_BOOLEAN:
                    $this->RegisterVariableBoolean($componentsFromShellyResult['ident'], $name, $tmpComponent['presentation'], 0);
                    break;
                case VARIABLETYPE_FLOAT:
                    $this->RegisterVariableFloat($componentsFromShellyResult['ident'], $name, $tmpComponent['presentation'], 0);
                    break;
                case VARIABLETYPE_INTEGER:
                    $this->RegisterVariableInteger($componentsFromShellyResult['ident'], $name, $tmpComponent['presentation'], 0);
                    break;
                case VARIABLETYPE_STRING:
                    $this->RegisterVariableString($componentsFromShellyResult['ident'], $name, $tmpComponent['presentation'], 0);
                    break;
                default:

                    break;
            }
                    //IPS_LogMessage('test1', print_r($tmpComponent, true));
                    if (array_key_exists('action', $tmpComponent)) {
                        $this->EnableAction($componentsFromShellyResult['ident']);
                    }

                    //With Extra Action Variable
                    //IPS_LogMessage('tmpComponent', print_r($tmpComponent, true));
                    if (array_key_exists('actionWithExtraVariable', $tmpComponent)) {
                        $name = $tmpComponent['actionWithExtraVariable']['name'];
                        if ($componentsFromShellyResult['number'] > 0) {
                            $name = $tmpComponent['actionWithExtraVariable']['name'] . ' ' . $componentsFromShellyResult['number'];
                        }
                        $plusIdent = '_ExtraAction';
                        switch ($tmpComponent['actionWithExtraVariable']['type']) {
                        case VARIABLETYPE_BOOLEAN:
                            $this->RegisterVariableBoolean($componentsFromShellyResult['ident'] . $plusIdent, $name, $tmpComponent['actionWithExtraVariable']['presentation'], 0);
                            break;
                        case VARIABLETYPE_FLOAT:
                            $this->RegisterVariableFloat($componentsFromShellyResult['ident'] . $plusIdent, $name, $tmpComponent['actionWithExtraVariable']['presentation'], 0);
                            break;
                        case VARIABLETYPE_INTEGER:
                            $this->RegisterVariableInteger($componentsFromShellyResult['ident'] . $plusIdent, $name, $tmpComponent['actionWithExtraVariable']['presentation'], 0);
                            break;
                        case VARIABLETYPE_STRING:
                            $this->RegisterVariableString($componentsFromShellyResult['ident'] . $plusIdent, $name, $tmpComponent['actionWithExtraVariable']['presentation'], 0);
                            break;
                        default:

                            break;
                    }
                        $this->EnableAction($componentsFromShellyResult['ident'] . $plusIdent);
                    }
                }
            }
        }
    }
