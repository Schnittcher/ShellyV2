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
        }

        public function RequestAction($Ident, $Value)
        {
            $IdentKeyPath = $this->convertIdentToKeyPath($Ident);
            IPS_LogMessage('IdentKeyPath', print_r($IdentKeyPath, true));
            $tmpComponents = $this->getValueByKeyPath($IdentKeyPath[0]);

            // 1. Hole alle Keys als Array
            $keys = array_keys($tmpComponents['action']['params']);

            $tmpComponents['action']['params'][$keys[0]] = $IdentKeyPath[1];
            $tmpComponents['action']['params'][$keys[1]] = $Value;

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
        }

        public function ReceiveData($JSONString)
        {
            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            $Payload = json_decode($Buffer['Payload'], true);
            if (array_key_exists('Topic', $Buffer)) {
                if (fnmatch('getComponents/rpc', $Buffer['Topic'])) {
                    $tmpComponents = $Payload['result'];
                    $allComponentsFromShelly = $this->getArrayLeafKeyPaths($tmpComponents);
                    $this->WriteAttributeString('Components', json_encode($allComponentsFromShelly));

                    IPS_LogMessage('test', print_r($allComponentsFromShelly, true));

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
                        $components = $this->getArrayLeafKeyPaths($Payload['params']);
                        IPS_LogMessage('components', print_r($components, true));

                        foreach ($components as $key => $value) {
                            $componentsFromShellyResult = $this->cleanComponentPath($value);
                            $this->SetValue($componentsFromShellyResult['ident'], $this->getValueByKeyPathFromArray($Payload['params'], $componentsFromShellyResult['original']));
                            IPS_LogMessage('test', print_r($componentsFromShellyResult, true));
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
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = $method;
            $Payload['params'] = $params; //['id' => $switch, 'on' => $value];

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        private function registerComponentVariables($allComponentsFromShelly)
        {
            foreach ($allComponentsFromShelly as $entry) {
                $componentsFromShellyResult = $this->cleanComponentPath($entry);
                $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);
                if ($tmpComponent != null) {
                    switch ($tmpComponent['type']) {
                case VARIABLETYPE_BOOLEAN:
                    $this->RegisterVariableBoolean($componentsFromShellyResult['ident'], $tmpComponent['name'] . ' ' . $componentsFromShellyResult['number'], $tmpComponent['presentation'], 0);
                    break;
                case VARIABLETYPE_FLOAT:
                    $this->RegisterVariableFloat($componentsFromShellyResult['ident'], $tmpComponent['name'] . ' ' . $componentsFromShellyResult['number'], $tmpComponent['presentation'], 0);
                    break;

                default:

                    break;
            }
                    if ($tmpComponent['writable']) {
                        $this->EnableAction($componentsFromShellyResult['ident']);
                    }
                }
            }
        }
    }
