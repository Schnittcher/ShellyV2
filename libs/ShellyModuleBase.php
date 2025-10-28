<?php

declare(strict_types=1);

require_once __DIR__ . '/MQTTHelper.php';
require_once __DIR__ . '/vendor/SymconModulHelper/DebugHelper.php';
require_once __DIR__ . '/components.php';
require_once __DIR__ . '/ComponentDefinitionHelper.php';

    class ShellyModuleBase extends IPSModule
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
            $this->RegisterPropertyBoolean('DebugMissingIdents', false);
            $this->RegisterPropertyString('VariableList', '{}');

            $this->RegisterVariableBoolean('Reachable', $this->Translate('Reachable'), [
                'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                'OPTIONS'         => json_encode([
                    [
                        'Value'            => true,
                        'Caption'          => 'Online',
                        'IconActive'       => false,
                        'Icon'             => 'Information',
                        'ColorActive'      => true,
                        'ColorValue'       => 65280
                    ],
                    [
                        'Value'            => false,
                        'Caption'          => 'Offline',
                        'IconActive'       => false,
                        'Icon'             => 'Information',
                        'ColorActive'      => true,
                        'ColorValue'       => 16711680,
                    ],
                ]
                    )
            ], 99);
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges()
        {
            parent::ApplyChanges();
            //Never delete this line!
            $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
            $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');

            if ($MQTTTopic != '') {
                $this->getComponents();
            }
        }

        public function RequestAction($Ident, $Value)
        {
            //Um den originalen Ident zu behalten, zum Beispiel für actionWithExtraVariable
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
                }
            }

            $this->callRPCFunction($tmpComponents['action']['method'], $tmpComponents['action']['params']);
        }

        public function ReceiveData($JSONString)
        {
            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            $Payload = json_decode($Buffer['Payload'], true);
            if (array_key_exists('Topic', $Buffer)) {
                if (fnmatch('*/online', $Buffer['Topic'])) {
                    $this->SetValue('Reachable', $Payload);
                    if (!$Payload) {
                        $this->zeroingValues();
                    }
                }
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getComponents/rpc', $Buffer['Topic'])) {
                    $tmpComponents = $Payload['result'];
                    $allComponentsFromShelly = $this->getArrayLeafKeyPaths($tmpComponents);

                    // Duplikate entfernen
                    $allComponentsFromShelly = array_unique($allComponentsFromShelly);

                    $propertyChannel = @$this->ReadPropertyInteger('Channel');
                    //IPS_LogMessage('test', $this->InstanceID . ' ' . $propertyChannel);
                    $propertyComponent = @$this->ReadPropertyString('Component');
                    //IPS_LogMessage('test', $this->InstanceID . ' ' . $propertyComponent);

                    $this->createariableListForForm($allComponentsFromShelly, $propertyComponent, $propertyChannel);
                    $this->registerComponentVariables();

                    if (array_key_exists('result', $Payload)) {
                        $this->parsePayloadIntoVariables($Payload['result']);
                    }

                    //Shelly muss online sein, da es sonst keine Antwort gegeben hatte, deswegen die Variable auf true setzen.
                    $this->SetValue('Reachable', true);
                }
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/events/rpc', $Buffer['Topic'])) {
                    if (array_key_exists('params', $Payload)) {
                        $this->parsePayloadIntoVariables($Payload['params']);
                    }

                    /**
                     * //Components vom Shelly Params Payload holen.
                     * $components = $this->getArrayLeafKeyPaths($Payload['params']);
                     * foreach ($components as $key => $component) {
                     * //Clean Path holen
                     * $componentsFromShellyResult = $this->cleanComponentPath($component);
                     * //Mit clean keypath Value vom self::components array holen
                     * $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);
                     *
                     * //Value vom Params array holen mit dem originalen keypath
                     * $value = $this->getValueByKeyPathFromArray($Payload['params'], $componentsFromShellyResult['original']);
                     * //ggf. umrechnung druchführen
                     * if ($tmpComponent != null) {
                     * if (array_key_exists('factor', $tmpComponent)) {
                     * $this->SendDebug('Factor calculation', 'Factor: ' . $tmpComponent['factor'], 0);
                     * $value = $value * $tmpComponent['factor'];
                     * }
                     * }
                     *
                     * //Ausnahme RGB
                     * if ($componentsFromShellyResult['clean'] == 'rgb.rgb.0') {
                     * $value = json_encode([
                     * 'r' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][0],
                     * 'g' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][1],
                     * 'b' => $Payload['params']['rgb:' . $componentsFromShellyResult['number']]['rgb'][2]
                     * ]);
                     * }
                     *
                     * $this->SetValue($componentsFromShellyResult['ident'], $value);
                     * }
                     */
                }
            }
        }

        public function getComponents()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = $this->ReadPropertyString('MQTTTopic') . '/getComponents';
            $Payload['method'] = 'Shelly.GetStatus';
            $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
        }

        public function callRPCFunction($method, $params)
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = $method;
            $Payload['params'] = $params;

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        public function callRPCGetStatus()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = 'Shelly.GetStatus';
            $Payload['params'] = '';

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        protected function SetValue($Ident, $Value)
        {
            if (@$this->GetIDForIdent($Ident)) {
                $this->SendDebug('SetValue :: ' . $Ident, $Value, 0);
                parent::SetValue($Ident, $Value);
            } else {
                if ($this->ReadPropertyBoolean('DebugMissingIdents')) {
                    if (is_array($Value)) {
                        $Value = json_encode($Value);
                    }
                    $this->SendDebug('Missing Ident :: Value', $Ident . ' :: ' . $Value, 0);
                }
            }
        }

        //Alle Werte auf 0, false oder leer setzen, wenn die Funktion zeroing bei den Variablen aktiv geschaltet wurde
        protected function zeroingValues()
        {
            $Variables = json_decode($this->GetBuffer('variableList'), true);

            foreach ($Variables as $key => $variable) {
                if ($variable['Zeroing']) {
                    switch ($variable['VarType']) {
                        case VARIABLETYPE_BOOLEAN:
                            $this->SetValue($variable['Ident'], false);
                            break;
                        case VARIABLETYPE_STRING:
                            $this->SetValue($variable['Ident'], '');
                            break;
                        case VARIABLETYPE_FLOAT:
                        case VARIABLETYPE_INTEGER:
                            $this->SetValue($variable['Ident'], 0);
                            break;
                        default:
                            $this->LogMessage('Error by zeroing Values.', KL_ERROR);
                            break;
                    }
                }
            }
        }

        private function parsePayloadIntoVariables($Payload)
        {
            //Components vom Shelly Params Payload holen.
            $components = $this->getArrayLeafKeyPaths($Payload);
            foreach ($components as $key => $component) {
                //Clean Path holen
                $componentsFromShellyResult = $this->cleanComponentPath($component);
                //Mit clean keypath Value vom self::components array holen
                $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);

                //Value vom Params array holen mit dem originalen keypath
                $value = $this->getValueByKeyPathFromArray($Payload, $componentsFromShellyResult['original']);
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
                        'r' => $Payload['rgb:' . $componentsFromShellyResult['number']]['rgb'][0],
                        'g' => $Payload['rgb:' . $componentsFromShellyResult['number']]['rgb'][1],
                        'b' => $Payload['rgb:' . $componentsFromShellyResult['number']]['rgb'][2]
                    ]);
                }

                $this->SetValue($componentsFromShellyResult['ident'], $value);
            }
        }

        private function registerComponentVariables()
        {
            $allVariables = json_decode($this->GetBuffer('variableList'), true);

            foreach ($allVariables as $variable) {
                $tmpComponent = $this->getValueByKeyPath($variable['CleanKeyPath']);
                if (!$variable['actionWithExtraVariable']) {
                    if ($tmpComponent != null) {
                        $name = $this->Translate($tmpComponent['name']);
                        if ($variable['Channel'] > 0) {
                            $name = $this->Translate($tmpComponent['name']) . ' ' . $variable['Channel'];
                        }
                    }
                    //Legt alle Variablen an, wenn diese in der Liste aktiv geschaltet wurden.
                    $this->MaintainVariable($variable['Ident'], $name, $tmpComponent['type'], $tmpComponent['presentation'], 0, $variable['Selected']);
                    //Wenn die Komponetene eine Aktion besitzt, wird EnableAction aufgerufen
                    if (array_key_exists('action', $tmpComponent)) {
                        $this->EnableAction($variable['Ident']);
                    }
                } else {
                    //Mit Extra Action Variable - sprich wenn die Komponente mehrere Variablen zum bedienen hat z.B. Helligkeit in % und Dim down, Dim up, Dim stop
                    if (array_key_exists('actionWithExtraVariable', $tmpComponent)) {
                        $name = $this->Translate($tmpComponent['actionWithExtraVariable']['name']);
                        if ($variable['Channel'] > 0) {
                            $name = $this->Translate($tmpComponent['actionWithExtraVariable']['name']) . ' ' . $variable['Channel'];
                        }
                        $this->MaintainVariable($variable['Ident'], $name, $tmpComponent['actionWithExtraVariable']['type'], $tmpComponent['actionWithExtraVariable']['presentation'], 0, $variable['Selected']);
                        $this->EnableAction($variable['Ident']);
                    }
                }
            }
        }

        ################### Test für Liste mit Variablen um diese aktivieren / deaktivieren zu können.

        private function createariableListForForm($allComponentsFromShelly, $component = '', $channel = '')
        {
            $variableList = [];

            //Alte Liste laden, um die aktuellen Einstellungen (Selected / Zeroing) zu übernehmen
            $oldList = json_decode($this->ReadPropertyString('VariableList'), true);
            // Map zur schnellen Suche: Ident => Selected-Wert / Ident => Zeroiung
            $oldMap = [];
            foreach ($oldList as $item) {
                if (!empty($item['Ident'])) {
                    $oldMap[$item['Ident']]['selected'] = $item['Selected'];
                    $oldMap[$item['Ident']]['zeroing'] = $item['Zeroing'];
                }
            }

            //Standardwert für "Selected"
            $selected = true;
            //Standardwert für "Zeroing"
            $zeroing = false;

            foreach ($allComponentsFromShelly as $entry) {
                $componentsFromShellyResult = $this->cleanComponentPath($entry);

                // Überprüfen, ob der Ident in der alten Liste vorhanden ist
                if (!empty($componentsFromShellyResult['ident']) && isset($oldMap[$componentsFromShellyResult['ident']])) {
                    // Falls der Ident in der alten Liste existiert, den "Selected"-Wert übernehmen
                    $selected = $oldMap[$componentsFromShellyResult['ident']]['selected'];
                    $zeroing = $oldMap[$componentsFromShellyResult['ident']]['zeroing'];
                }

                $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);
                if ($tmpComponent != null) {
                    $name = $tmpComponent['name'];
                    if ($componentsFromShellyResult['number'] > 0) {
                        $name = $tmpComponent['name'] . ' ' . $componentsFromShellyResult['number'];
                    }

                    if (($componentsFromShellyResult['base'] == $component && $componentsFromShellyResult['number'] == $channel) || $componentsFromShellyResult['base'] == $component && $componentsFromShellyResult['number'] == '' || $component == '' && $channel == '') {
                        $variableList[] = [
                            'Name'                        => $this->Translate($name),
                            'Ident'                       => $componentsFromShellyResult['ident'],
                            'CleanKeyPath'                => $componentsFromShellyResult['clean'],
                            'Channel'                     => $componentsFromShellyResult['number'],
                            'actionWithExtraVariable'     => false,
                            'Selected'                    => $selected,
                            'Zeroing'                     => $zeroing
                        ];
                        //Mit Extra Action Variable - sprich wenn die Komponente mehrere Variablen zum bedienen hat z.B. Helligkeit in % und Dim down, Dim up, Dim stop
                        if (array_key_exists('actionWithExtraVariable', $tmpComponent)) {
                            $name = $tmpComponent['actionWithExtraVariable']['name'];
                            if ($componentsFromShellyResult['number'] > 0) {
                                $name = $tmpComponent['actionWithExtraVariable']['name'] . ' ' . $componentsFromShellyResult['number'];
                            }
                            $extraIdent = $componentsFromShellyResult['ident'] . '_ExtraAction';

                            $variableList[] = [
                                'Name'                        => $this->Translate($name),
                                'Ident'                       => $extraIdent,
                                'CleanKeyPath'                => $componentsFromShellyResult['clean'],
                                'Channel'                     => $componentsFromShellyResult['number'],
                                'actionWithExtraVariable'     => true,
                                'Selected'                    => $selected,
                                'Zeroing'                     => $zeroing
                            ];
                        }
                    }
                }
            }

            $this->SendDebug('variableList', $variableList, 0);
            //Setze variableList in Buffer, für GetConfiguration Form & zum Anlegen der Variablen
            $this->SetBuffer('variableList', json_encode($variableList));
        }
    }

