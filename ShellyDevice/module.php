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
            $this->RegisterPropertyString('Components', '');
            $this->SetBuffer('ComponentsID', null);
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

                    IPS_LogMessage('allComponentsFromShelly', print_r($allComponentsFromShelly, true));

                    $allComponentsFromDefinition = $this->getArrayKeyPaths(self::$components);
                    // Duplikate entfernen
                    $allComponentsFromShelly = array_unique($allComponentsFromShelly);
                    $allComponentsFromDefinition = array_unique($allComponentsFromDefinition);

                    // Vergleich
                    $commonKeys = array_intersect($allComponentsFromShelly, $allComponentsFromDefinition);

                    //IPS_LogMessage('All Keys', print_r($commonKeys, true));

                    foreach ($allComponentsFromShelly as $value) {
                        $this->registerComponentVariables($allComponentsFromShelly);
                    }

                    //$this->searchComponents($tmpComponents);
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

        private function searchComponents($tmpComponents)
        {
            // Ein neues Array für die gefundenen Keys
            $foundKeys = [];

            // Schleife durch die Keys des ersten Arrays
            foreach ($tmpComponents as $key => $value) {
                // Entferne den Teil nach dem Doppelpunkt
                $keyWithoutColon = strtok($key, ':'); // strtok() teilt den Key am Doppelpunkt und gibt nur den ersten Teil zurück

                // Überprüfe, ob der Key ohne den Doppelpunkt im zweiten Array existiert
                if (array_key_exists($keyWithoutColon, self::$components)) {
                    // Wenn der Key gefunden wird, füge ihn zum neuen Array hinzu
                    $foundKeys[$key] = $value;
                    $this->registerComponentVariables($keyWithoutColon, $tmpComponents[$key], self::$components[$keyWithoutColon]);
                    //IPS_LogMessage('found', print_r(self::$components[$keyWithoutColon], true));
                    //IPS_LogMessage('found', print_r($tmpComponents[$key], true));
                }
            }

            // Ausgabe der gefundenen Keys
            //IPS_LogMessage('found',print_r($foundKeys,true));
        }

        private function registerComponentVariables($allComponentsFromShelly)
        {
            foreach ($allComponentsFromShelly as $entry) {
                // Prüfen auf Doppelpunkt mit Zahl
                if (preg_match('/(.*):(\d+)(.*)/', $entry, $matches)) {
                    $base = $matches[1];      // z. B. "input"
                    $number = $matches[2];    // z. B. "0"
                    $rest = $matches[3];      // z. B. ".id" oder ".temperature.tC"

                    $cleanKey = $base . $rest;
                    $tempVar = $number;
                } else {
                    $cleanKey = $entry;
                    $tempVar = '';
                }

                // ident = original mit . und : ersetzt durch _
                $ident = str_replace(['.', ':'], '_', $entry);

                $componentsFromShellyResult = [
                    'original' => $entry,
                    'clean'    => $cleanKey,
                    'number'   => $tempVar,
                    'ident'    => $ident
                ];

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
