<?php

declare(strict_types=1);

require_once __DIR__ . '/../libs/MQTTHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/DebugHelper.php';
require_once __DIR__ . '/../libs/components.php';

    class ShellyDevice extends IPSModule
    {
        use MQTTHelper;
        use DebugHelper;
        use Components;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('MQTTTopic', '');
            $this->RegisterPropertyString('Components', '');
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
                    $this->searchComponents($tmpComponents);
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

        private function registerComponentVariables($component, $tmpComponents, $components)
        {
            $id = $tmpComponents['id'];
            foreach ($tmpComponents as $key => $value) {
                if (array_key_exists($key, $components)) {
                    if (!is_array($value)) {
                        switch ($components[$key]['type']) {
                            case VARIABLETYPE_BOOLEAN:
                                //IPS_LogMessage('test', $firstKey);
                                $this->RegisterVariableBoolean($key . '_' . $id, $components[$key]['name'], $components[$key]['presentation'], 0);
                                break;
							case VARIABLETYPE_FLOAT:
								//IPS_LogMessage('test', $firstKey);
								$this->RegisterVariableFLOAT($key . '_' . $id, $components[$key]['name'], $components[$key]['presentation'], 0);
								break;

                            default:

                                break;
                        }
                        if ($components[$key]['writable']) {
                            $this->EnableAction($key . '_' . $id);
                        }
                    } else {
						//TODO Array mit Komponenten
						$this->registerComponentVariables($component,$value, $components);
						IPS_LogMessage('test',print_r($value,true));
					}
                }
            }
        }
    }