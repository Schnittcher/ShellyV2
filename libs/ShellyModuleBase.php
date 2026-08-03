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
            if (IPS_GetKernelVersion() < 8.2) {
                $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            }
            $this->RegisterPropertyString('MQTTTopic', '');
            $this->RegisterPropertyBoolean('DebugMissingIdents', false);
            $this->RegisterPropertyString('VariableList', '{}');

            // ### TEST / EXPERIMENTELL - Virtuelle Komponenten (Boolean/Number/Enum/Text) ###
            // Standardmäßig AUS. Siehe ApplyChanges() / ReceiveData() / registerComponentVariables().
            $this->RegisterPropertyBoolean('EnableVirtualComponents', false);
            // ### ENDE TEST / EXPERIMENTELL ###

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

            // ############################################################
            // ### TEST / EXPERIMENTELL - Virtuelle Komponenten          ###
            // ### Boolean/Number/Enum/Text-Komponenten tauchen weder in ###
            // ### Shelly.GetStatus noch in Shelly.GetConfig auf,        ###
            // ### sondern nur in Shelly.GetComponents (wie BLU TRVs) -  ###
            // ### siehe getVirtualComponents() in ComponentDefinitionHelper.
            // ############################################################
            if ($this->ReadPropertyBoolean('EnableVirtualComponents') && $MQTTTopic != '') {
                $this->getComponents();
            }
            // ### ENDE TEST / EXPERIMENTELL ###############################
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
                //Ausnahme für BLUTRV - viel mehr Parameter beim RPC Aufruf
                if ($IdentKeyPath[0] == 'blutrv.target_C') {
                    $tmpComponents['action']['params']['params']['target_C'] = $Value;
                } elseif ($IdentKeyPath[0] == 'blutrv.pos') {
                    $tmpComponents['action']['params']['params']['pos'] = $Value;
                } elseif ($IdentKeyPath[0] == 'blutrv.current_C') {
                    $tmpComponents['action']['params']['params']['t_C'] = $Value;
                } else {
                    $tmpComponents['action']['params'][$keys[1]] = $Value;
                    //Ausnahme für RGB
                    if ($IdentKeyPath[0] == 'rgb.rgb.0') {
                        $rgb = json_decode($Value, true);
                        $tmpComponents['action']['params'][$keys[1]] = array_values($rgb);
                    }
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

                $componentsUpdated = false;
                $valuesToParse = null;

                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getComponentsViaStatus/rpc', $Buffer['Topic'])) {
                    if (array_key_exists('result', $Payload)) {
                        $this->SetBuffer('physicalComponentsList', json_encode($this->getArrayLeafKeyPaths($Payload['result'])));
                        $valuesToParse = $Payload['result'];
                    }
                    $componentsUpdated = true;
                }

                //Ausnahme für BLU TRVs, diese müssen über Shelly.GetComponents abgerufen werden.
                // ### TEST / EXPERIMENTELL - Virtuelle Komponenten ###
                // Bei aktiviertem EnableVirtualComponents werden hier zusätzlich auch
                // Boolean/Number/Enum/Text-Komponenten extrahiert, da diese (wie BLU TRVs)
                // ebenfalls nur über Shelly.GetComponents auffindbar sind.
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getComponents/rpc', $Buffer['Topic'])) {
                    if (array_key_exists('result', $Payload)) {
                        $blutrvs = $this->getBLUTRVs($Payload['result']);
                        $virtualComponents = $this->getVirtualComponents($Payload['result']);
                        $componentsFromGetComponents = array_merge($blutrvs, $virtualComponents['status']);

                        $this->SetBuffer('componentsFromGetComponents', json_encode($this->getArrayLeafKeyPaths($componentsFromGetComponents)));
                        $this->SetBuffer('virtualComponentsConfig', json_encode($virtualComponents['config']));
                        $valuesToParse = $componentsFromGetComponents;
                    }
                    $componentsUpdated = true;
                }
                // ### ENDE TEST / EXPERIMENTELL ###

                if ($componentsUpdated) {
                    //Physische Komponenten (aus Shelly.GetStatus) und Komponenten aus Shelly.GetComponents
                    //(BLU TRVs, ggf. virtuelle Komponenten) zusammenführen. Beide Antworten kommen
                    //unabhängig voneinander per MQTT an (unterschiedliche RPC-Aufrufe, keine feste
                    //Reihenfolge) - deshalb puffert jeder der beiden obigen if-Blöcke sein Ergebnis in
                    //einem eigenen Buffer, und hier wird bei JEDER der beiden Antworten neu aus dem
                    //jeweils letzten Stand BEIDER Buffer zusammengesetzt. So geht z.B. die Liste der
                    //physischen Komponenten nicht verloren, wenn danach noch die GetComponents-Antwort
                    //eintrifft (und umgekehrt).
                    $physicalComponents = json_decode($this->GetBuffer('physicalComponentsList'), true) ?: [];
                    $componentsFromGetComponents = json_decode($this->GetBuffer('componentsFromGetComponents'), true) ?: [];
                    // Duplikate entfernen
                    $allComponentsFromShelly = array_unique(array_merge($physicalComponents, $componentsFromGetComponents));

                    $propertyChannel = @$this->ReadPropertyInteger('Channel');
                    $propertyComponent = @$this->ReadPropertyString('Component');

                    $this->createVariableListForForm($allComponentsFromShelly, $propertyComponent, $propertyChannel);
                    $this->registerComponentVariables();

                    if ($valuesToParse != null) {
                        $this->parsePayloadIntoVariables($valuesToParse);
                    }

                    //Shelly muss online sein, da es sonst keine Antwort gegeben hatte, deswegen die Variable auf true setzen.
                    $this->SetValue('Reachable', true);
                }
            }

            if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/events/rpc', $Buffer['Topic'])) {
                if (array_key_exists('params', $Payload)) {
                    $this->parsePayloadIntoVariables($Payload['params']);
                }
            }
        }
        public function getComponents()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = $this->ReadPropertyString('MQTTTopic') . '/getComponents';
            $Payload['method'] = 'Shelly.GetComponents';
            $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
        }

        public function getComponentsViaStatus()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = $this->ReadPropertyString('MQTTTopic') . '/getComponentsViaStatus';
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

        protected function SetValue($Ident, $Value)
        {
            if (@$this->GetIDForIdent($Ident)) {
                $this->SendDebug('SetValue :: ' . $Ident, $Value, 0);

                if (is_array($Value)) {
                    $Value = implode(',', $Value);
                }
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
            //$Variables = json_decode($this->GetBuffer('variableList'), true);
            $Variables = json_decode($this->ReadPropertyString('VariableList'), true);

            foreach ($Variables as $key => $variable) {
                //Um Herauszufinden um welchen Variablentyp es sich hier handelt
                $Component = $this->getValueByKeyPath($variable['CleanKeyPath']);
                if ($variable['Zeroing']) {
                    switch ($Component['type']) {
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

        private function callRPCGetStatus()
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = 'Shelly.GetStatus';
            $Payload['params'] = '';

            $this->sendMQTT($Topic, json_encode($Payload));
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

                // ############################################################
                // ### TEST / EXPERIMENTELL - cover.current_pos spiegeln    ###
                // ### Damit die Shutter-Kachel (Position State /           ###
                // ### _ExtraAction) auch die Live-Position anzeigt, nicht  ###
                // ### nur die reine "Current Position"-Anzeige-Variable.   ###
                // ### Nur für cover.current_pos, da Basis- und Extra-      ###
                // ### Variable hier gleicher Typ (INTEGER) und gleiche     ###
                // ### Bedeutung (Position in %) haben - bei anderen        ###
                // ### actionWithExtraVariable-Komponenten (z.B. Brightness ###
                // ### Action, Cover Action State) wäre das NICHT korrekt!  ###
                // ### => Bei Problemen (z.B. Widget "zuckt" beim Ziehen    ###
                // ###    während der Rollladen fährt) diesen Block wieder  ###
                // ###    entfernen.                                       ###
                // ############################################################
                if ($componentsFromShellyResult['clean'] == 'cover.current_pos') {
                    $this->SetValue($componentsFromShellyResult['ident'] . '_ExtraAction', $value);
                }
                // ### ENDE TEST / EXPERIMENTELL ###############################
            }
        }

        // ############################################################
        // ### TEST / EXPERIMENTELL - Virtuelle Komponenten          ###
        // ### Liefert den vom Nutzer auf dem Gerät hinterlegten     ###
        // ### Konfigurations-Eintrag (u.a. "name", bei Enum         ###
        // ### "options") für z.B. component='boolean', channel=200 ###
        // ### -> sucht "boolean:200" im per Shelly.GetComponents    ###
        // ### gepufferten Ergebnis (siehe getVirtualComponents()).  ###
        // ### Liefert null, falls (noch) keine Konfiguration        ###
        // ### vorliegt oder der Eintrag nicht existiert.            ###
        // ############################################################
        // Nur diese Basis-Typen sind die generischen virtuellen Komponenten aus components.php.
        // WICHTIG: Ohne diese Einschränkung würde z.B. bei "pm1:0" (mehrere Unterwerte wie freq,
        // aenergy.total, ...) der vom Nutzer vergebene Kanalname fälschlich auf ALLE Unterwerte
        // dieses Kanals übertragen, da Shelly.GetComponents für jede Komponente einen "name" liefert.
        private static $virtualComponentTypes = ['boolean', 'number', 'enum', 'text'];

        private function getVirtualComponentOverride($component, $channel)
        {
            if (!in_array($component, self::$virtualComponentTypes, true)) {
                return null;
            }
            $config = json_decode($this->GetBuffer('virtualComponentsConfig'), true);
            if (!is_array($config)) {
                return null;
            }
            $key = $component . ':' . $channel;
            return $config[$key] ?? null;
        }
        // ### ENDE TEST / EXPERIMENTELL ###############################

        private function registerComponentVariables()
        {
            $allVariables = json_decode($this->GetBuffer('variableList'), true);

            foreach ($allVariables as $variable) {
                $tmpComponent = $this->getValueByKeyPath($variable['CleanKeyPath']);
                if (!$variable['actionWithExtraVariable']) {
                    if ($tmpComponent != null) {
                        $name = $this->Translate($tmpComponent['name']);
                        if ($variable['Channel'] > 0) {
                            $name = $this->Translate($tmpComponent['name'] . ' ' . $variable['Channel']);
                        }
                    }
                    $presentation = $tmpComponent['presentation'];

                    // ### TEST / EXPERIMENTELL - virtuelle Komponenten: Name/Optionen vom Gerät übernehmen ###
                    if ($this->ReadPropertyBoolean('EnableVirtualComponents')) {
                        $base = explode('.', $variable['CleanKeyPath'])[0];
                        $virtualOverride = $this->getVirtualComponentOverride($base, $variable['Channel']);
                        if ($virtualOverride != null) {
                            if (array_key_exists('name', $virtualOverride) && $virtualOverride['name'] != '') {
                                $name = $virtualOverride['name'];
                            }
                            if ($base == 'enum' && array_key_exists('options', $virtualOverride)) {
                                $options = [];
                                foreach ($virtualOverride['options'] as $optionValue) {
                                    $options[] = ['Value' => $optionValue, 'Caption' => $optionValue];
                                }
                                $presentation['OPTIONS'] = json_encode($options);
                            }
                        }
                    }
                    // ### ENDE TEST / EXPERIMENTELL ###

                    //Legt alle Variablen an, wenn diese in der Liste aktiv geschaltet wurden.
                    $this->MaintainVariable($variable['Ident'], $name, $tmpComponent['type'], $presentation, 0, $variable['Selected']);
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

        private function createVariableListForForm($allComponentsFromShelly, $component = '', $channel = '')
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

            //Immer die Event Komponenten hinzufügen!
            array_push($allComponentsFromShelly, 'events:0.component', 'events:0.event');

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

                    // ### TEST / EXPERIMENTELL - virtuelle Komponenten: Name vom Gerät übernehmen ###
                    if ($this->ReadPropertyBoolean('EnableVirtualComponents')) {
                        $virtualOverride = $this->getVirtualComponentOverride($componentsFromShellyResult['base'], $componentsFromShellyResult['number']);
                        if ($virtualOverride != null && array_key_exists('name', $virtualOverride) && $virtualOverride['name'] != '') {
                            $name = $virtualOverride['name'];
                        }
                    }
                    // ### ENDE TEST / EXPERIMENTELL ###

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

