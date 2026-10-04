<?php

declare(strict_types=1);

require_once __DIR__ . '/MQTTHelper.php';
require_once __DIR__ . '/DebugHelperStrict.php';
require_once __DIR__ . '/components.php';
require_once __DIR__ . '/ComponentDefinitionHelper.php';
require_once __DIR__ . '/CameraStream.php';
require_once __DIR__ . '/BTHomeObjects.php';
require_once __DIR__ . '/ShellyKVS.php';

    class ShellyModuleBase extends IPSModuleStrict
    {
        use MQTTHelper;
        use StrictDebugHelper;
        use Components;
        use ComponentDefinitionHelper;
        use CameraStream;
        use BTHomeObjects;
        use ShellyKVS;

        // IDEE / TODO - Presets-Zuordnungstabelle für ALLE
        // Komponenten (noch NICHT umgesetzt, kein akuter Bedarf,
        // nur damit die Idee nicht verloren geht):
        // getDynamicComponentMetadata() unten liefert pro
        // Instanz schon Name/Optionen/Min-Max/Access direkt vom
        // Gerät - aber nur für Felder, die der Shelly selbst
        // kennt UND nur für die dynamischen Typen. Für rein
        // Symcon-seitige Darstellung (z.B. ein Icon) oder Werte,
        // die der Shelly nicht/nicht konsistent mitliefert (z.B.
        // unterschiedliche Kelvin-Bereiche bei CCT-Lampen je nach
        // Modell), könnte man zusätzlich eine GLOBALE
        // Presets-Tabelle bauen, keyed auf ModelID + Komponenten-
        // Typ (Bevorzugte Variante, siehe Chat) - ähnlich wie
        // XMODServices.php es für LinkedGo/BLU-Geräte schon
        // macht, nur eben als Ergänzung zu components.php statt
        // Ersatz. components.php selbst eignet sich dafür NICHT
        // (global, kennt keine Geräte-/Instanz-Zugehörigkeit,
        // würde bei unterschiedlicher Nutzung z.B. von
        // boolean:200 auf verschiedenen Geräten kollidieren).
        // WICHTIG: Bewusst generisch für JEDEN Komponententyp
        // bauen (auch cover/light, nicht nur number/CCT) - auch
        // wenn z.B. cover.current_pos (0-100%) ein fester
        // Shelly-Protokollwert ist und aktuell KEIN konkreter
        // Bedarf für eine Override dort besteht, soll der
        // Mechanismus nicht künstlich auf bestimmte Typen
        // beschränkt sein, falls doch mal ein Sonderfall auftaucht.
        // Fallback für Fälle außerhalb der Presets-Tabelle:
        // manuelles Override-Feld in der VariableList-Property
        // (schon heute pro Instanz/pro Variable, siehe
        // Selected/Zeroing).

        // Dynamisch angelegte Komponenten
        // Liefert den vom Nutzer auf dem Gerät hinterlegten
        // Konfigurations-Eintrag (u.a. "name", bei Enum
        // "options") für z.B. component='boolean', channel=200
        // -> sucht "boolean:200" in 'componentConfigs' (volle
        // Config je Komponente aus Shelly.GetComponents, siehe
        // getComponentConfigs()). Liefert null, falls
        // (noch) keine Metadaten vorliegen oder der Eintrag
        // nicht existiert.
        // Nur diese Basis-Typen werden per Shelly.GetComponents auf Name/Optionen/Min-Max/Access
        // geprüft - Boolean/Number/Enum/Text (Shelly "User-defined components") und presencezone
        // (physischer Sensor, aber ebenfalls nur über Shelly.GetComponents mit "name" pro Zone
        // auffindbar, z.B. "Room"/"test"). WICHTIG: Ohne diese Einschränkung würde z.B. bei "pm1:0"
        // (mehrere Unterwerte wie freq, aenergy.total, ...) der vom Nutzer vergebene Kanalname
        // fälschlich auf ALLE Unterwerte dieses Kanals übertragen, da Shelly.GetComponents für jede
        // Komponente einen "name" liefert.
        private static $dynamicComponentTypes = ['boolean', 'number', 'enum', 'text', 'presencezone', 'camerazone'];

        public function Create(): void
        {
            //Never delete this line!
            parent::Create();
            //Der übergeordnete MQTT-Server/-Client wird bei IPSModuleStrict automatisch über die Kompatibilität
            //(parentRequirements in der module.json) von der Verwaltungskonsole verbunden - ConnectParent() gibt
            //es dort nicht mehr.
            $this->RegisterPropertyString('MQTTTopic', '');
            $this->RegisterPropertyBoolean('DebugMissingIdents', false);
            $this->RegisterPropertyString('VariableList', '{}');
            $this->registerCameraStreamProperties();

            $this->RegisterVariableBoolean('Reachable', $this->Translate('Reachable'), [
                'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                'OPTIONS'         => json_encode([
                    [
                        'Value'              => true,
                        'Caption'            => 'Online',
                        'IconActive'         => true,
                        'IconValue'          => 'Information',
                        'ColorActive'        => true,
                        'ColorValue'         => 65280,
                        'ContentColorActive' => false,
                        'ContentColorValue'  => -1
                    ],
                    [
                        'Value'              => false,
                        'Caption'            => 'Offline',
                        'IconActive'         => true,
                        'IconValue'          => 'Information',
                        'ColorActive'        => true,
                        'ColorValue'         => 16711680,
                        'ContentColorActive' => false,
                        'ContentColorValue'  => -1
                    ],
                ]
                    )
            ], 99);
        }

        public function Destroy(): void
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges(): void
        {
            parent::ApplyChanges();
            //Never delete this line!
            $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
            $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
            //Die Komponenten fragen die Instanz-Module (ShellyDevice/ShellyComponent) in ihrem
            //ApplyChanges() per requestComponentsStatus() ab - aber nur, wenn dann schon ein Parent
            //aktiv ist. Beim Anlegen einer Instanz (z.B. über den Configurator) ist das nicht der Fall:
            //der Parent wird erst NACH dem ersten ApplyChanges() verbunden. Deshalb zusätzlich auf das
            //Verbinden reagieren (siehe MessageSink()).
            $this->RegisterMessage($this->InstanceID, FM_CONNECT);
        }

        public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
        {
            parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
            if ($Message == FM_CONNECT && $SenderID == $this->InstanceID) {
                if ($this->ReadPropertyString('MQTTTopic') != '' && $this->HasActiveParent()) {
                    $this->requestComponentsStatus();
                }
            }
        }

        public function RequestAction(string $Ident, mixed $Value): void
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
            //Aktionen ohne Parameter (z.B. DALI.StartScan, 'params' => []) bekommen auch keine Kanalnummer.
            if (count($keys) > 0) {
                $tmpComponents['action']['params'][$keys[0]] = $IdentKeyPath[1];
            }

            //Werte aus der Geräte-Config (Definition mit 'configPath', z.B. Kamera rtsp.enable): per SetConfig mit
            //verschachtelter Config setzen und danach neu einlesen, damit die Variable den Gerätewert zeigt.
            if (isset($tmpComponents['configPath'])) {
                $config = [];
                $reference = &$config;
                foreach (explode('.', $tmpComponents['configPath']) as $part) {
                    $reference[$part] = [];
                    $reference = &$reference[$part];
                }
                $reference = $Value;
                unset($reference);
                $this->callRPCFunction($tmpComponents['action']['method'], ['id' => (int) $IdentKeyPath[1], 'config' => $config]);
                $this->requestComponentsStatus();
                return;
            }

            //Bei 'list'-Aktionen steckt der Wert schon im Methodennamen (z.B. "Cury.Boost") - weitere feste
            //Parameter (z.B. "slot") dürfen dann nicht mit dem Wert überschrieben werden.
            if (count($keys) > 1 && !array_key_exists('list', $tmpComponents['action'])) {
                //Ausnahme für BLUTRV - viel mehr Parameter beim RPC Aufruf
                if ($IdentKeyPath[0] == 'blutrv.target_C') {
                    $tmpComponents['action']['params']['params']['target_C'] = $Value;
                } elseif ($IdentKeyPath[0] == 'blutrv.pos') {
                    $tmpComponents['action']['params']['params']['pos'] = $Value;
                } elseif ($IdentKeyPath[0] == 'blutrv.current_C') {
                    $tmpComponents['action']['params']['params']['t_C'] = $Value;
                } else {
                    $tmpComponents['action']['params'][$keys[1]] = $Value;
                    //Farbwerte (rgb/rgbw/rgbcct): Die Variable enthält laut Symcon-Doku {"r":..,"g":..,"b":..}
                    //(Reihenfolge der Schlüssel nicht garantiert), Shelly erwartet [r,g,b].
                    if (($tmpComponents['presentation']['PRESENTATION'] ?? null) == VARIABLE_PRESENTATION_COLOR) {
                        $rgb = json_decode((string) $Value, true);
                        if (is_array($rgb)) {
                            $tmpComponents['action']['params'][$keys[1]] = [$rgb['r'], $rgb['g'], $rgb['b']];
                        }
                    }
                }
            }
            $this->callRPCFunction($tmpComponents['action']['method'], $tmpComponents['action']['params']);
        }

        public function ReceiveData(string $JSONString): string
        {
            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            //Antwort auf eine KVS-Anfrage (siehe libs/ShellyKVS.php): für die wartende Funktion ablegen.
            if (isset($Buffer['Topic']) && $this->storeKVSResponse((string) $Buffer['Topic'], $this->decodeMQTTPayload($Buffer))) {
                return '';
            }
            //IPSModuleStrict: "Payload" ist HEX-kodiert (siehe MQTTHelper::decodeMQTTPayload()).
            $Payload = json_decode($this->decodeMQTTPayload($Buffer), true);
            //Außer beim "online"-Topic (true/false) erwarten alle Zweige ein JSON-Objekt.
            if (!is_array($Payload) && !fnmatch('*/online', (string) ($Buffer['Topic'] ?? ''))) {
                return '';
            }
            if (array_key_exists('Topic', $Buffer)) {
                if (fnmatch('*/online', $Buffer['Topic'])) {
                    $this->SetValue('Reachable', $Payload);
                    if (!$Payload) {
                        $this->zeroingValues();
                    }
                }

                //Antwort auf requestComponentsStatus() (Shelly.GetComponents mit "include":["status","config"]).
                //Das Ergebnis enthält ALLE Komponenten (physische, BLU TRVs, dynamische Boolean/Number/Enum/
                //Text/presencezone, Add-on-Sensoren) mit Status UND Config - eine zweite Abfrage ist nicht
                //nötig. Alle Seiten werden gesammelt und erst danach in einem Rutsch verarbeitet. Folgeseiten
                //werden über RegisterOnceTimer() entkoppelt angefragt: SendDataToParent() direkt aus
                //ReceiveData() heraus hat live mehrfach den Produktiv-Broker/Symcon lahmgelegt (siehe
                //RunNextComponentsPageAsync()).
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getComponents/' . $this->InstanceID . '/rpc', $Buffer['Topic'])) {
                    //Seiten ohne laufenden Abruf (verspätete Reste eines früheren Abrufs) verwerfen.
                    if ((float) $this->GetBuffer('componentsRunStarted') <= 0) {
                        return '';
                    }
                    if (array_key_exists('result', $Payload) && array_key_exists('components', $Payload['result'])) {
                        $statusAccumulated = json_decode($this->GetBuffer('componentsPageAccumulator'), true) ?: [];
                        $statusAccumulated = array_merge($statusAccumulated, $Payload['result']['components']);
                        $this->SetBuffer('componentsPageAccumulator', json_encode($statusAccumulated));

                        $statusOffset = $Payload['result']['offset'] ?? 0;
                        $statusTotal = $Payload['result']['total'] ?? count($statusAccumulated);
                        $statusReceivedSoFar = $statusOffset + count($Payload['result']['components']);

                        $statusPageCount = (int) ($this->GetBuffer('componentsPageCount') ?: '0') + 1;
                        $this->SetBuffer('componentsPageCount', (string) $statusPageCount);
                        $statusMaxPages = 30;

                        if ($statusReceivedSoFar < $statusTotal && count($Payload['result']['components']) > 0 && $statusPageCount < $statusMaxPages) {
                            //WICHTIG: SendDataToParent() darf nicht direkt aus ReceiveData() heraus
                            //aufgerufen werden (siehe Kommentar bei requestComponentsPage()) - über
                            //RegisterOnceTimer() entkoppeln.
                            $this->SetBuffer('componentsNextPageOffset', (string) $statusReceivedSoFar);
                            $this->RegisterOnceTimer('GetComponentsNextPage', 'SHY_RunNextComponentsPageAsync($_IPS["TARGET"]);');
                        } else {
                            if ($statusPageCount >= $statusMaxPages) {
                                $this->SendDebug('getComponents', 'Abbruch: Sicherheitslimit von ' . $statusMaxPages . ' Seiten erreicht (offset/total vom Gerät evtl. inkonsistent).', 0);
                            }
                            $this->SetBuffer('componentsPageAccumulator', json_encode([]));
                            $this->SetBuffer('componentsPageCount', '0');
                            $this->SetBuffer('componentsRunStarted', '0');

                            $this->applyComponentsResult($statusAccumulated);
                        }
                    }
                }
                //Antwort auf die Status-Abfrage eines BLU-Geräts nach einem Tastendruck (siehe scheduleBTHomeRefresh()).
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getBTHomeStatus/' . $this->InstanceID . '/rpc', $Buffer['Topic'])) {
                    $statusParams = $this->bthomeStatusParams($Payload);
                    if (count($statusParams) > 0) {
                        $this->parsePayloadIntoVariables($statusParams);
                    }
                }
                //Antwort auf BTHome.GetObjectInfos (Name/Typ/Einheit der Sensor-Objekte, siehe RequestBTHomeObjectInfos()):
                //Variablen mit den echten Angaben neu anlegen bzw. aktualisieren.
                if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/getObjectInfos/' . $this->InstanceID . '/rpc', $Buffer['Topic'])) {
                    if ($this->storeBTHomeObjectInfos($Payload)) {
                        //Die Variablen sind meist schon mit Rückfall-Namen angelegt - umbenennen, sofern der Nutzer den
                        //Namen nicht selbst geändert hat (MaintainVariable() setzt den Namen nur beim Anlegen).
                        $this->syncBTHomeVariableNames();
                        $this->rebuildVariables();
                    }
                }
            }

            if (fnmatch($this->ReadPropertyString('MQTTTopic') . '/events/rpc', $Buffer['Topic'])) {
                if (array_key_exists('params', $Payload)) {
                    $this->parsePayloadIntoVariables($Payload['params']);
                    //BLU-Taster: Tastendrücke kommen als Ereignis (NotifyEvent), nicht als Status - siehe BTHomeObjects.
                    foreach (is_array($Payload['params']['events'] ?? null) ? $Payload['params']['events'] : [] as $event) {
                        if (is_array($event)) {
                            $this->setBTHomeButtonEvent($event);
                        }
                    }
                }
            }
            return '';
        }

        //HINWEIS: Das seitenweise Auslesen (Abruf starten, Seiten sammeln, Folgeseiten per Timer, Ergebnis an applyComponentsResult())
        //gibt es bewusst zweimal: hier für ein Gateway (Buffer componentsRun*/componentsPage*) und in ShellyBLUDevice je Gateway
        //(Buffer bluRun_*/bluAcc_*/bluPages_*/bluNext_*, Methoden requestComponentsStatus()/handleComponentsPage()). Der Abruf hier ist der
        //zentrale Pfad aller Geräte und wurde deshalb nicht zu einem gemeinsamen Helfer zusammengelegt (Aufwand und Risiko stehen in keinem
        //Verhältnis zum Nutzen). Änderungen an der Seitenlogik (z.B. Seitenlimit, Sperre gegen überlappende Abrufe) in beiden Klassen machen.
        //
        //Fragt ALLE Komponenten mit Status und Config per Shelly.GetComponents ab; die Antwort
        //(ReceiveData()) legt die Variablen an bzw. aktualisiert sie. Gemeinsamer Einstieg für das
        //ApplyChanges() der Instanz-Module und den "Read Componentes"-Button. Die Antwort ist paginiert
        //(die Seitengröße bestimmt das Gerät), Folgeseiten werden in ReceiveData() per
        //RegisterOnceTimer() angefordert.
        //
        //Verifiziert (Feldnamen/Ident-Stabilität, live): pm1, alle dynamischen Typen (boolean/number/
        //enum/text/presencezone), Shelly Presence G4, Shelly 1 Gen3, Pro RGBWW PM, Smart WaterValve (XT1),
        //BLU TRV (blutrv:201), per API-Doku für cover/em/temperature/humidity. Bei einem bisher
        //unbekannten Komponententyp vor breiterem Einsatz einmal live gegenprüfen.
        public function requestComponentsStatus(): void
        {
            //Läuft bereits ein Abruf (z.B. aus ApplyChanges() und gleichzeitig aus dem Verbinden), wird kein zweiter gestartet:
            //zwei parallele Abrufe teilen sich Accumulator und Seitenzähler, ein verspäteter Rest des einen überschrieb sonst das
            //fertige Ergebnis des anderen (z.B. mit nur 1 statt 25 Komponenten). Nach 15 s gilt ein Abruf als gescheitert.
            $started = (float) $this->GetBuffer('componentsRunStarted');
            if ($started > 0 && (microtime(true) - $started) < 15) {
                return;
            }
            $this->SetBuffer('componentsRunStarted', (string) microtime(true));
            $this->SetBuffer('componentsPageAccumulator', json_encode([]));
            $this->SetBuffer('componentsPageCount', '0');
            $this->requestComponentsPage(0);
        }

        //Öffentlicher Einstiegspunkt für den per RegisterOnceTimer() registrierten Timer - läuft
        //außerhalb des ReceiveData()-Aufruf-Stacks, liest den zu ladenden Offset aus dem Buffer.
        public function RunNextComponentsPageAsync(): void
        {
            $offset = (int) $this->GetBuffer('componentsNextPageOffset');
            $this->requestComponentsPage($offset);
        }

        public function callRPCFunction(string $method, array $params): void
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';

            $Payload['id'] = 1;
            $Payload['src'] = 'user_1';
            $Payload['method'] = $method;
            //Shelly erwartet für "params" ein Objekt - ein leeres Array würde als [] statt {} gesendet.
            $Payload['params'] = $params === [] ? new stdClass() : $params;

            $this->sendMQTT($Topic, json_encode($Payload));
        }

        //Verarbeitet das vollständige Ergebnis von Shelly.GetComponents ($statusAccumulated = Liste der Komponenteneinträge mit
        //key/status/config): Buffer setzen, Variablenliste und Variablen anlegen, Werte übernehmen. Wird aufgerufen, wenn die
        //letzte Seite eingetroffen ist. Die Instanz für BLU-Geräte an mehreren Gateways (ShellyBLUDevice) ruft es mit dem
        //zusammengeführten Ergebnis aller Gateways auf.
        protected function applyComponentsResult(array $statusAccumulated): void
        {
            $statusDict = $this->getAllComponentsAsStatusDict(['components' => $statusAccumulated]);
            //Volle Config für ALLE Komponenten (auch physische wie switch/cover/em/pm1,
            //nicht nur dynamische) - siehe getComponentConfigs()/getPhysicalComponentName()/
            //Fallback in getDynamicComponentMetadata().
            $componentConfigs = $this->getComponentConfigs(['components' => $statusAccumulated]);
            //IP-Adresse des Geräts (WLAN, sonst Ethernet) - z.B. für die RTSP-Adresse der Kamera.
            $deviceIP = $statusDict['wifi']['sta_ip'] ?? ($statusDict['eth']['ip'] ?? '');
            $this->SetBuffer('deviceIP', is_string($deviceIP) ? $deviceIP : '');
            //Werte aus der Geräte-Config (z.B. Kamera rtsp.enable) in den Status spiegeln.
            $statusDict = $this->mergeConfigBackedValues($statusDict, $componentConfigs);
            $this->SetBuffer('componentsList', json_encode($this->getArrayLeafKeyPaths($statusDict)));
            $this->SetBuffer('componentConfigs', json_encode($componentConfigs));
            //BTHome: Typ (Zahl/Boolean/Text) der Sensorwerte aus dem Status merken (siehe BTHomeObjects).
            $this->SetBuffer('bthomeValueTypes', json_encode($this->collectBTHomeValueTypes($statusDict)));
            $this->SetBuffer('bthomeDeviceModels', json_encode($this->collectBTHomeDeviceModels($statusAccumulated)));
            $valuesToParse = $statusDict;
            $allComponentsFromShelly = json_decode($this->GetBuffer('componentsList'), true) ?: [];

            $propertyChannel = $this->componentChannel();
            $propertyComponent = @$this->ReadPropertyString('Component');

            $this->createVariableListForForm($allComponentsFromShelly, $propertyComponent, $propertyChannel);
            $this->registerComponentVariables();

            //Kamera: Stream-Objekte (RTSP) automatisch anlegen, sobald die IP-Adresse bekannt ist.
            $this->autoMaintainCameraStreams();

            //BTHome: Name/Typ/Einheit der Messwerte beim Gerät erfragen - entkoppelt per Timer, weil
            //SendDataToParent() direkt aus ReceiveData() heraus den Datenfluss blockieren kann.
            if (count(json_decode($this->GetBuffer('bthomeValueTypes'), true) ?: []) > 0 || $this->bthomeHasSensorConfigs()) {
                $this->RegisterOnceTimer('BTHomeObjectInfos', 'SHY_RequestBTHomeObjectInfos($_IPS["TARGET"]);');
            }

            if ($valuesToParse != null) {
                $this->parsePayloadIntoVariables($valuesToParse);
            }
            $this->resetMissingErrorLists($allComponentsFromShelly);

            //Shelly muss online sein, da es sonst keine Antwort gegeben hatte, deswegen die Variable auf true setzen.
            $this->SetValue('Reachable', true);

            //Falls das Konfigurationsformular gerade offen ist: neu laden, damit die
            //aktualisierte Variablenliste sichtbar wird, ohne dass man das Formular manuell
            //schließen und wieder öffnen muss (die Antwort kommt asynchron per MQTT, ggf.
            //erst nachdem das Formular schon geöffnet wurde).
            $this->ReloadForm();
        }

        //Die Geräte melden "errors" nur, solange ein Fehler vorliegt. Fehlt das Feld im vollständigen Status, ist der Fehler behoben:
        //Fehlertext leeren und "Störung" auf false setzen. $leafPaths = Blattpfade des Status (z.B. "switch:0.output").
        protected function resetMissingErrorLists(array $leafPaths): void
        {
            foreach ($this->getResetWhenMissingLeafPaths($leafPaths) as $path) {
                if (!in_array($path, $leafPaths, true)) {
                    $ident = $this->cleanComponentPath($path)['ident'];
                    $this->SetValue($ident, '');
                    $this->SetValue(substr($ident, 0, -6) . 'fault', false);
                }
            }
        }

        //Fehlercode des Geräts (z.B. "overtemp", "out_of_range:voltage") übersetzen. Codes mit Zusatz (nach dem Doppelpunkt) werden
        //in Teilen übersetzt, unbekannte Codes bleiben unverändert (Geräte können neue Codes liefern).
        protected function translateErrorCode(string $code): string
        {
            $translated = $this->Translate($code);
            if ($translated !== $code || strpos($code, ':') === false) {
                return $translated;
            }
            [$base, $rest] = explode(':', $code, 2);
            return $this->Translate($base) . ' (' . $this->Translate($rest) . ')';
        }

        //Darstellung der Variable "Störung" (siehe 'fault' in components.php).
        protected function faultPresentation(): array
        {
            $options = [];
            foreach ([[false, 'No fault', 65280], [true, 'Fault', 16711680]] as [$value, $caption, $color]) {
                $options[] = ['Value' => $value, 'Caption' => $this->Translate($caption), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => $color, 'ContentColorActive' => false, 'ContentColorValue' => -1];
            }
            return ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode($options)];
        }

        //Kanal der Komponente, für die diese Instanz gilt (Property "Channel", bei ShellyDevice 0). ShellyBLUDevice überschreibt das:
        //dort ergibt sich der Kanal aus der MAC-Adresse des BLU-Geräts am Haupt-Gateway.
        protected function componentChannel()
        {
            return @$this->ReadPropertyInteger('Channel');
        }

        //Legt die Variablen aus den bereits gespeicherten Komponentendaten neu an (ohne neue Abfrage beim Gerät) -
        //z.B. wenn nachträglich Zusatzinformationen eingetroffen sind (BTHome.GetObjectInfos).
        protected function rebuildVariables(): void
        {
            $allComponentsFromShelly = json_decode($this->GetBuffer('componentsList'), true) ?: [];
            $propertyChannel = $this->componentChannel();
            $propertyComponent = @$this->ReadPropertyString('Component');
            $this->createVariableListForForm($allComponentsFromShelly, $propertyComponent, $propertyChannel);
            $this->registerComponentVariables();
            $this->ReloadForm();
        }

        protected function SetValue(string $Ident, mixed $Value): bool
        {
            if (@$this->GetIDForIdent($Ident)) {
                $this->SendDebug('SetValue :: ' . $Ident, $Value, 0);

                if (is_array($Value)) {
                    $Value = implode(',', $Value);
                }
                return parent::SetValue($Ident, $Value);
            }
            if ($this->ReadPropertyBoolean('DebugMissingIdents')) {
                if (is_array($Value)) {
                    $Value = json_encode($Value);
                }
                $this->SendDebug('Missing Ident :: Value', $Ident . ' :: ' . $Value, 0);
            }
            return false;
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

        protected function parsePayloadIntoVariables($Payload)
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
                //Shelly meldet "kein Wert" als null (z.B. temperature.tC bei cb/switch, wenn der Sensor nichts
                //liefert). SetValue() würde daraus 0 bzw. "" machen und einen echten Messwert vortäuschen - die
                //Variable behält dann lieber ihren letzten Wert.
                if ($value === null) {
                    continue;
                }
                //BLU-Taster ohne bisherigen Tastendruck melden als Zeitstempel einen Platzhalter weit in der Vergangenheit.
                if ($componentsFromShellyResult['clean'] == 'bthomesensor.last_updated_ts' && is_numeric($value) && $value < 1000000000) {
                    continue;
                }
                //Fehlerlisten ('resetWhenMissing', z.B. switch.errors): Codes übersetzen und zusammenfassen; die Variable "Störung"
                //(Ident <...>_fault) ist true, sobald mindestens ein Fehler vorliegt.
                if (($tmpComponent['resetWhenMissing'] ?? false) && is_array($value)) {
                    $this->SetValue($componentsFromShellyResult['ident'], implode(', ', array_map(function ($code)
                    {
                        return $this->translateErrorCode((string) $code);
                    }, $value)));
                    $this->SetValue(substr($componentsFromShellyResult['ident'], 0, -6) . 'fault', count($value) > 0);
                    continue;
                }
                //BTHome: Aufzählungs-Sensor (z.B. Helligkeitsstufe) als Anzeigetext der Stufe.
                if ($componentsFromShellyResult['clean'] == 'bthomesensor.value') {
                    $caption = $this->bthomeSensorEnumCaption('bthomesensor:' . $componentsFromShellyResult['number'], $value);
                    if ($caption !== null) {
                        $value = $caption;
                    }
                }
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

                //Farbwerte (rgb/rgbw/rgbcct): Shelly liefert [r,g,b] als Array, eine String-Variable mit der
                //Farb-Darstellung (ENCODING RGB) erwartet laut Symcon-Doku {"r":..,"g":..,"b":..}. Ohne
                //Umwandlung würde SetValue() das Array zu "r,g,b" verketten und die Darstellung bliebe leer.
                if (($tmpComponent['presentation']['PRESENTATION'] ?? null) == VARIABLE_PRESENTATION_COLOR && is_array($value) && count($value) == 3) {
                    $value = json_encode(['r' => $value[0], 'g' => $value[1], 'b' => $value[2]]);
                }

                $this->SetValue($componentsFromShellyResult['ident'], $value);

                // cover.current_pos spiegeln
                // Damit die Shutter-Kachel (Position State /
                // _ExtraAction) auch die Live-Position anzeigt, nicht
                // nur die reine "Current Position"-Anzeige-Variable.
                // Nur für cover.current_pos, da Basis- und Extra-
                // Variable hier gleicher Typ (INTEGER) und gleiche
                // Bedeutung (Position in %) haben - bei anderen
                // actionWithExtraVariable-Komponenten (z.B. Brightness
                // Action, Cover Action State) wäre das NICHT korrekt!
                // => Bei Problemen (z.B. Widget "zuckt" beim Ziehen
                // während der Rollladen fährt) diesen Block wieder
                // entfernen.
                if ($componentsFromShellyResult['clean'] == 'cover.current_pos') {
                    $this->SetValue($componentsFromShellyResult['ident'] . '_ExtraAction', $value);
                }
            }
        }

        private function requestComponentsPage($offset)
        {
            $Topic = $this->ReadPropertyString('MQTTTopic') . '/rpc';
            $Payload['id'] = 1;
            //Die Instanz-ID in der Quelle macht das Antwort-Topic pro Instanz eindeutig: mehrere Instanzen desselben Geräts
            //(z.B. eine je Komponente) fragen sonst gleichzeitig dieselbe Quelle ab und vermischen die Seiten.
            $Payload['src'] = $this->ReadPropertyString('MQTTTopic') . '/getComponents/' . $this->InstanceID;
            $Payload['method'] = 'Shelly.GetComponents';
            //"config" zusätzlich zu "status": liefert u.a. den vom Nutzer auf dem Gerät vergebenen
            //Namen pro Kanal (z.B. "Waschmaschine" bei switch:0), bei dynamischen Komponenten Optionen/
            //Min/Max - siehe getComponentConfigs()/getPhysicalComponentName()/getDynamicComponentMetadata().
            $Payload['params'] = ['include' => ['status', 'config'], 'offset' => $offset];
            $this->sendMQTT($Topic, json_encode($Payload, JSON_UNESCAPED_SLASHES));
        }

        private function getDynamicComponentMetadata($component, $channel)
        {
            if (!in_array($component, self::$dynamicComponentTypes, true)) {
                return null;
            }
            $configs = json_decode($this->GetBuffer('componentConfigs'), true);
            $entry = is_array($configs) ? ($configs[$component . ':' . $channel] ?? null) : null;
            return is_array($entry) ? $entry : null;
        }

        // Gerätename als Präfix für physische Komponenten
        // Anders als getDynamicComponentMetadata() (Name ERSETZT den generischen Namen komplett, nur
        // für boolean/number/enum/text/presencezone) wird der Gerätename hier nur als PRÄFIX vor den
        // generischen Feldnamen gesetzt (z.B. "Waschmaschine - Active power" statt nur "Active power")
        // - für "normale" physische Komponenten wie switch/cover/em/pm1/light/rgb, die MEHRERE
        // Unterfelder pro Kanal haben. Ein kompletter Ersatz würde dort wie bei den dynamischen
        // Komponenten alle Unterfelder gleich benennen (der pm1-Namenskollisions-Bug von früher in der
        // Session) - das Präfix behält die Unterscheidung (Active power/Voltage/...) bei gleichzeitiger
        // Zuordnung zum richtigen Gerät/Kanal. 'object' und die dynamischen Typen sind ausgenommen (die
        // haben ihre eigene, passendere Namenslogik). Der Name stammt aus 'componentConfigs'.
        private function getPhysicalComponentName($component, $channel)
        {
            if (in_array($component, self::$dynamicComponentTypes, true) || $component == 'object' || $component == 'bthomesensor') {
                return null;
            }
            $configs = json_decode($this->GetBuffer('componentConfigs'), true);
            if (!is_array($configs)) {
                return null;
            }
            $key = $component . ':' . $channel;
            $name = $configs[$key]['name'] ?? null;
            //BLU-Geräte ohne Namen: MAC-Adresse als Präfix (wie bei deren Sensoren).
            if (($name === null || $name === '') && ($component == 'bthomedevice' || $component == 'blutrv')) {
                return $this->bthomeUnnamedDeviceName($key);
            }
            return ($name !== null && $name !== '') ? $name : null;
        }

        //Generischer Zugriff auf ein einzelnes Feld aus der Geräte-Config einer Komponente (z.B.
        //"ct_range" bei cct) - liefert null, falls (noch) keine Config vorliegt oder das Feld nicht
        //existiert. Nutzt dieselbe componentConfigs-Quelle wie getPhysicalComponentName().
        private function getComponentConfigField($component, $channel, $configKey)
        {
            $configs = json_decode($this->GetBuffer('componentConfigs'), true);
            $key = $component . ':' . $channel;
            return $configs[$key][$configKey] ?? null;
        }

        private function registerComponentVariables()
        {
            $allVariables = json_decode($this->GetBuffer('variableList'), true);

            //Bei "object" ist die Zahl (z.B. "200") die interne Shelly-Komponenten-ID, kein echter
            //Kanal wie bei switch:0/switch:1 - deshalb nur anhängen, wenn tatsächlich MEHRERE
            //object-Komponenten auf demselben Gerät existieren (sonst z.B. "Phase A voltage 200"
            //bei nur einer einzigen Instanz unnötig).
            $objectChannels = [];
            foreach ($allVariables as $variable) {
                if (explode('.', $variable['CleanKeyPath'])[0] == 'object') {
                    $objectChannels[$variable['Channel']] = true;
                }
            }
            $multipleObjectChannels = count($objectChannels) > 1;

            foreach ($allVariables as $variable) {
                $tmpComponent = $this->getValueByKeyPath($variable['CleanKeyPath']);
                if (!$variable['actionWithExtraVariable']) {
                    $base = explode('.', $variable['CleanKeyPath'])[0];
                    if ($tmpComponent != null) {
                        //Erst übersetzen, DANN die Kanalnummer anhängen - Translate() macht exakte
                        //String-Treffer, eine kombinierte Zeichenkette wie "Temperature 100" bräuchte
                        //sonst einen eigenen Locale-Eintrag pro Kanalnummer (siehe Kommentar bei
                        //createVariableListForForm() für ein Beispiel, wo das gefehlt hat).
                        $name = $this->Translate($tmpComponent['name']);
                        if ($variable['Channel'] > 0 && ($base != 'object' || $multipleObjectChannels)) {
                            $name = $this->Translate($tmpComponent['name']) . ' ' . $variable['Channel'];
                        }
                    }
                    $presentation = $tmpComponent['presentation'];
                    $variableType = $tmpComponent['type'];
                    if (substr($variable['CleanKeyPath'], -6) == '.fault') {
                        $presentation = $this->faultPresentation();
                    }
                    //BTHome-Sensorwert: Typ, Name und Darstellung je nach Objekt (siehe libs/BTHomeObjects.php).
                    if ($variable['CleanKeyPath'] == 'bthomesensor.value') {
                        $sensorVariable = $this->getBTHomeSensorVariable('bthomesensor:' . $variable['Channel']);
                        $variableType = $sensorVariable['type'];
                        $presentation = $sensorVariable['presentation'];
                        $name = $sensorVariable['name'];
                    } elseif (in_array($variable['CleanKeyPath'], ['bthomedevice.button', 'bthomedevice.button2', 'bthomedevice.button3', 'bthomedevice.button4'], true)) {
                        $presentation = $this->bthomeButtonPresentation();
                    } elseif ($variable['CleanKeyPath'] == 'bthomedevice.dial') {
                        $presentation = $this->bthomeDialPresentation();
                    } elseif ($variable['CleanKeyPath'] == 'bthomesensor.last_updated_ts') {
                        //Zeitstempel eines Sensors: Sensorname voranstellen statt der Kanalnummer
                        $name = $this->getBTHomeSensorVariable('bthomesensor:' . $variable['Channel'])['name'] . ' - ' . $this->Translate($tmpComponent['name']);
                    }
                    $isWritable = true;
                    $dynamicEnumOptions = null;

                    // dynamisch angelegte Komponenten: Name/Optionen/Min-Max-Einheit/Schreibschutz vom Gerät übernehmen
                    $componentMetadata = $this->getDynamicComponentMetadata($base, $variable['Channel']);
                    if ($componentMetadata != null) {
                        if (array_key_exists('name', $componentMetadata) && $componentMetadata['name'] != '') {
                            //presencezone hat pro Zone mehrere Felder (value/num_objects), aber nur
                            //EINEN Zonennamen - Namen kombinieren statt ersetzen, sonst heißen
                            //"Zone Presence" und "Objects in Zone" beide nur noch z.B. "Room".
                            if (in_array($base, ['presencezone', 'camerazone'], true)) {
                                $name = $componentMetadata['name'] . ' (' . $this->Translate($tmpComponent['name']) . ')';
                            } else {
                                //Translate() ist ein No-Op für Strings ohne passenden Locale-Eintrag
                                //(z.B. ein vom Nutzer frei vergebener Name wie "Trockner" bleibt
                                //unverändert) - für Shelly-Werksnamen wie "Power supply"/"Position"
                                //greift die Übersetzung dann aber korrekt.
                                $name = $this->Translate($componentMetadata['name']);
                            }
                        }
                        if ($base == 'enum' && array_key_exists('options', $componentMetadata)) {
                            //config.meta.ui.titles liefert schönere Anzeigetexte pro Optionswert
                            //(z.B. "charger_free" -> "Free") - falls nicht vorhanden, Rohwert als
                            //Fallback nutzen.
                            $titles = $componentMetadata['meta']['ui']['titles'] ?? [];
                            //Die Optionen werden erst NACH der Entscheidung ENUMERATION/VALUE_PRESENTATION
                            //(siehe unten) aufgebaut, weil die beiden Darstellungen unterschiedliche
                            //Felder pro Option kennen.
                            $dynamicEnumOptions = [];
                            foreach ($componentMetadata['options'] as $optionValue) {
                                $dynamicEnumOptions[] = [
                                    'Value'   => $optionValue,
                                    'Caption' => $this->Translate($titles[$optionValue] ?? $optionValue),
                                ];
                            }
                        }

                        //Number: Min/Max/Einheit/Step vom Gerät übernehmen, falls vorhanden (z.B.
                        //"Current limit" 6-16 A). Step liefert nicht jedes Gerät (z.B. WaterValve
                        //schon, EV-Charger nicht) - Fallback auf 1, wenn nicht vorhanden, statt
                        //Symcons eigenen (unbekannten) Default zu nutzen.
                        if ($base == 'number') {
                            if (array_key_exists('min', $componentMetadata)) {
                                $presentation['MIN'] = $componentMetadata['min'];
                            }
                            if (array_key_exists('max', $componentMetadata)) {
                                $presentation['MAX'] = $componentMetadata['max'];
                            }
                            $unit = $componentMetadata['meta']['ui']['unit'] ?? '';
                            if ($unit != '') {
                                $presentation['SUFFIX'] = ' ' . $unit;
                            }
                            $presentation['STEP_SIZE'] = $componentMetadata['meta']['ui']['step'] ?? 1;
                        }

                        //Manche dynamisch angelegten Komponenten sind schreibgeschützt (z.B. "Session energy" oder
                        //"Charger state" bei einem Shelly EV-Charger, access "cr" statt "crw") - dort darf
                        //keine Aktion angeboten werden, auch wenn der generische Typ (number/enum/...)
                        //normalerweise eine action hat.
                        if (array_key_exists('access', $componentMetadata) && strpos($componentMetadata['access'], 'w') === false) {
                            $isWritable = false;
                        }
                    }

                    // Gerätename als Präfix für physische Komponenten
                    $physicalName = $this->getPhysicalComponentName($base, $variable['Channel']);
                    if ($physicalName != null) {
                        $name = $physicalName . ' - ' . $this->Translate($tmpComponent['name']);
                    }
                    //Gerät mit mehreren Tasten: die erste heißt "Taste 1".
                    if ($variable['CleanKeyPath'] == 'bthomedevice.button' && $this->bthomeDeviceButtonCount('bthomedevice:' . $variable['Channel']) > 1) {
                        $name = ($physicalName != null ? $physicalName . ' - ' : '') . $this->Translate('Button') . ' 1';
                    }

                    // CCT-Farbtemperaturbereich vom Gerät übernehmen
                    // Shelly meldet den unterstützten Kelvin-Bereich direkt in der Komponenten-Config
                    // ("ct_range": [min, max]) - anders als bei Cover/Light/RGB-Brightness (immer fest
                    // 0-100%, laut API-Doku geprüft) ist das bei CCT tatsächlich geräteabhängig. Nur
                    // das "ct"-Feld braucht das, nicht "output"/"brightness" desselben cct-Kanals -
                    // deshalb exakter CleanKeyPath-Match statt nur $base == 'cct'.
                    if (in_array($variable['CleanKeyPath'], ['cct.ct', 'rgbcct.ct'], true)) {
                        $ctRange = $this->getComponentConfigField($base, $variable['Channel'], 'ct_range');
                        if (is_array($ctRange) && count($ctRange) == 2) {
                            $presentation['MIN'] = $ctRange[0];
                            $presentation['MAX'] = $ctRange[1];
                        }
                    }

                    // BLU TRV Zieltemperaturbereich vom Gerät übernehmen
                    // Shelly meldet min/max Zieltemperatur direkt in der Komponenten-Config
                    // ("min_target_C"/"max_target_C", Doku-Default 5-35°C) - der bisher hartkodierte
                    // Bereich in components.php (5-30) war laut Doku ungenau (30 statt 35). Nur das
                    // "target_C"-Feld braucht das, nicht "current_C"/"pos" desselben blutrv-Kanals.
                    if ($variable['CleanKeyPath'] == 'blutrv.target_C') {
                        $minTargetC = $this->getComponentConfigField('blutrv', $variable['Channel'], 'min_target_C');
                        $maxTargetC = $this->getComponentConfigField('blutrv', $variable['Channel'], 'max_target_C');
                        if ($minTargetC !== null) {
                            $presentation['MIN'] = $minTargetC;
                        }
                        if ($maxTargetC !== null) {
                            $presentation['MAX'] = $maxTargetC;
                        }
                    }

                    //Schreibfähige Präsentationen (Slider/Switch/Enumeration/Value Input) verlangen
                    //laut Symcon zwingend eine konfigurierte Variablenaktion - ohne EnableAction()
                    //(z.B. bei schreibgeschützten Komponenten wie "Session energy") würde
                    //MaintainVariable() sonst eine inkompatible Darstellung anlegen ("Diese Darstellung
                    //ist nur für Variablen [mit/ohne] eine Variablenaktion verfügbar"). Fällt in diesem
                    //Fall auf die nicht-schreibfähige VALUE_PRESENTATION zurück, die bereits gesetzten
                    //OPTIONS/SUFFIX/MIN/MAX bleiben dabei erhalten (VALUE_PRESENTATION unterstützt
                    //OPTIONS genauso, siehe z.B. die "Reachable"-Variable in Create()).
                    $writeOnlyPresentations = [VARIABLE_PRESENTATION_SLIDER, VARIABLE_PRESENTATION_SWITCH, VARIABLE_PRESENTATION_ENUMERATION, VARIABLE_PRESENTATION_VALUE_INPUT];
                    if (!$isWritable && in_array($presentation['PRESENTATION'], $writeOnlyPresentations, true)) {
                        $presentation['PRESENTATION'] = VARIABLE_PRESENTATION_VALUE_PRESENTATION;
                    }

                    //Optionen der dynamischen Enum-Komponente (siehe oben) je nach endgültiger Darstellung:
                    //- ENUMERATION kennt pro Option laut Symcon-Doku nur Value/Caption/IconActive/IconValue/
                    //  Color - alles andere meldet Symcon als "unknown sub-parameters".
                    //- VALUE_PRESENTATION (schreibgeschützt) kennt ColorActive/ColorValue, und sein
                    //  Formular verlangt zusätzlich bei JEDEM Eintrag auch ContentColorActive/
                    //  ContentColorValue - fehlen die, gibt's "Undefined array key"-Warnungen und ein
                    //  ungültiges Formular (live beobachtet bei der EV-Charger-Wallbox).
                    if ($dynamicEnumOptions !== null) {
                        $options = [];
                        foreach ($dynamicEnumOptions as $option) {
                            if ($presentation['PRESENTATION'] == VARIABLE_PRESENTATION_ENUMERATION) {
                                $options[] = $option + [
                                    'IconActive' => false,
                                    'IconValue'  => '',
                                    'Color'      => -1,
                                ];
                            } else {
                                $options[] = $option + [
                                    'IconActive'         => false,
                                    'IconValue'          => '',
                                    'ColorActive'        => false,
                                    'ColorValue'         => -1,
                                    'ContentColorActive' => false,
                                    'ContentColorValue'  => -1,
                                ];
                            }
                        }
                        $presentation['OPTIONS'] = json_encode($options);
                    }

                    //Legt alle Variablen an, wenn diese in der Liste aktiv geschaltet wurden.
                    $this->MaintainVariable($variable['Ident'], $name, $variableType, $presentation, 0, $variable['Selected']);
                    //BTHome-Sensorvariablen: automatisch vergebenen Namen merken, damit er später (wenn die Objektinfos
                    //eintreffen) nur umbenannt wird, solange der Nutzer ihn nicht geändert hat.
                    if (strpos($variable['Ident'], 'bthomesensor_') === 0) {
                        $this->rememberBTHomeAutoName($variable['Ident'], $name);
                    }
                    //Wenn die Komponetene eine Aktion besitzt, wird EnableAction aufgerufen
                    if (array_key_exists('action', $tmpComponent) && $isWritable) {
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

            //BTHome: Tasten- und Rad-Variablen (Taste 1 bis 4, Rad) nur für BLU-Geräte, bei denen sie bekannt sind (siehe bthomeButtonLeafPaths()).
            foreach ($allComponentsFromShelly as $entry) {
                if (preg_match('/^(bthomedevice:\d+)\./', $entry, $deviceMatch)) {
                    foreach ($this->bthomeButtonLeafPaths($deviceMatch[1]) as $buttonPath) {
                        if (!in_array($buttonPath, $allComponentsFromShelly, true)) {
                            $allComponentsFromShelly[] = $buttonPath;
                        }
                    }
                }
            }

            //Mit 'alwaysCreate' markierte Definitionen auch anlegen, wenn die Antwort das Feld nicht enthält
            //(z.B. Cury mit leerem Fach: "left": null) - siehe getAlwaysCreatedLeafPaths().
            foreach ($this->getAlwaysCreatedLeafPaths($allComponentsFromShelly) as $path) {
                if (!in_array($path, $allComponentsFromShelly, true)) {
                    $allComponentsFromShelly[] = $path;
                }
            }

            //Bei "object" ist die Zahl (z.B. "200") die interne Shelly-Komponenten-ID, kein echter
            //Kanal wie bei switch:0/switch:1 - deshalb nur anhängen, wenn tatsächlich MEHRERE
            //object-Komponenten auf demselben Gerät existieren.
            $objectChannels = [];
            foreach ($allComponentsFromShelly as $entry) {
                $entryCleaned = $this->cleanComponentPath($entry);
                if ($entryCleaned['base'] == 'object') {
                    $objectChannels[$entryCleaned['number']] = true;
                }
            }
            $multipleObjectChannels = count($objectChannels) > 1;

            foreach ($allComponentsFromShelly as $entry) {
                $componentsFromShellyResult = $this->cleanComponentPath($entry);

                // Überprüfen, ob der Ident in der alten Liste vorhanden ist
                if (!empty($componentsFromShellyResult['ident']) && isset($oldMap[$componentsFromShellyResult['ident']])) {
                    // Falls der Ident in der alten Liste existiert, den "Selected"-Wert übernehmen
                    $selected = $oldMap[$componentsFromShellyResult['ident']]['selected'];
                    $zeroing = $oldMap[$componentsFromShellyResult['ident']]['zeroing'];
                }

                $tmpComponent = $this->getValueByKeyPath($componentsFromShellyResult['clean']);
                //Nur echte Variablendefinitionen (mit 'type'): Zeigt der Pfad auf ein Zwischenelement der
                //Definition (z.B. cury.slots.left bei leerem Fach, Wert null), gibt es keine Variable dazu.
                if (is_array($tmpComponent) && array_key_exists('type', $tmpComponent)) {
                    $name = $tmpComponent['name'];
                    //Bei "object" ist die Zahl (z.B. "200") die interne Shelly-Komponenten-ID, kein
                    //echter Kanal wie bei switch:0/switch:1 - deshalb hier ausgenommen (sonst z.B.
                    //"Phase A voltage 200" statt "Phase A voltage"), außer es gibt tatsächlich
                    //mehrere object-Komponenten auf diesem Gerät.
                    if ($componentsFromShellyResult['number'] > 0 && ($componentsFromShellyResult['base'] != 'object' || $multipleObjectChannels)) {
                        //Erst übersetzen, DANN die Kanalnummer anhängen - Translate() macht exakte
                        //String-Treffer, eine kombinierte Zeichenkette wie "Temperature 100" bräuchte
                        //sonst einen eigenen Locale-Eintrag pro Kanalnummer (z.B. bei einer
                        //Smart WaterValve mit temperature:100 gefehlt - "Temperature 100" blieb
                        //unübersetzt, weil nur "Temperature" allein einen Locale-Eintrag hatte).
                        $name = $this->Translate($tmpComponent['name']) . ' ' . $componentsFromShellyResult['number'];
                    }

                    // dynamisch angelegte Komponenten: Name vom Gerät übernehmen
                    $componentMetadata = $this->getDynamicComponentMetadata($componentsFromShellyResult['base'], $componentsFromShellyResult['number']);
                    if ($componentMetadata != null && array_key_exists('name', $componentMetadata) && $componentMetadata['name'] != '') {
                        //presencezone hat pro Zone mehrere Felder (value/num_objects), aber nur
                        //EINEN Zonennamen - Namen kombinieren statt ersetzen, sonst heißen
                        //"Zone Presence" und "Objects in Zone" beide nur noch z.B. "Room".
                        if (in_array($componentsFromShellyResult['base'], ['presencezone', 'camerazone'], true)) {
                            $name = $componentMetadata['name'] . ' (' . $this->Translate($tmpComponent['name']) . ')';
                        } else {
                            $name = $componentMetadata['name'];
                        }
                    }

                    //BTHome-Sensorwert: Name wie bei der Variable (Gerät/MAC - Objektname)
                    if ($componentsFromShellyResult['clean'] == 'bthomesensor.value') {
                        $name = $this->getBTHomeSensorVariable('bthomesensor:' . $componentsFromShellyResult['number'])['name'];
                    } elseif ($componentsFromShellyResult['clean'] == 'bthomesensor.last_updated_ts') {
                        $name = $this->getBTHomeSensorVariable('bthomesensor:' . $componentsFromShellyResult['number'])['name'] . ' - ' . $this->Translate($tmpComponent['name']);
                    }

                    // Gerätename als Präfix für physische Komponenten
                    $physicalName = $this->getPhysicalComponentName($componentsFromShellyResult['base'], $componentsFromShellyResult['number']);
                    if ($physicalName != null) {
                        $name = $physicalName . ' - ' . $this->Translate($tmpComponent['name']);
                    }

                    //BTHome: Taster-Sensoren (ohne Status-Wert) bekommen keine Variablen - der Tastendruck steht in der Variable
                    //des BLU-Geräts (bthomedevice.button).
                    if ($componentsFromShellyResult['base'] == 'bthomesensor' && $this->isBTHomeButtonSensorKey('bthomesensor:' . $componentsFromShellyResult['number'])) {
                        continue;
                    }

                    //BTHome: Eine Instanz für ein BLU-Gerät (bthomedevice:N) enthält auch alle Sensoren (bthomesensor:M) mit
                    //derselben MAC-Adresse und - bei einem Thermostat - die Variablen des blutrv (mit den Aktionen).
                    $isSensorOfSelectedBTHomeDevice = $component == 'bthomedevice' && in_array($componentsFromShellyResult['base'], ['bthomesensor', 'blutrv'], true) && $this->bthomeComponentBelongsToDevice($componentsFromShellyResult['base'] . ':' . $componentsFromShellyResult['number'], 'bthomedevice:' . $channel);
                    //RSSI und Batterie hat der Thermostat (blutrv) genauso wie das BLU-Gerät - nur einmal anlegen.
                    if ($isSensorOfSelectedBTHomeDevice && in_array($componentsFromShellyResult['clean'], ['blutrv.rssi', 'blutrv.battery'], true)) {
                        continue;
                    }
                    //Sensoren, die eine Variable des Geräts bzw. des Thermostats doppeln (Batterie, Ziel-/externe Temperatur).
                    if ($isSensorOfSelectedBTHomeDevice && $componentsFromShellyResult['base'] == 'bthomesensor' && $this->bthomeSensorDuplicatesDeviceVariable('bthomesensor:' . $componentsFromShellyResult['number'], 'bthomedevice:' . $channel)) {
                        continue;
                    }

                    if (($componentsFromShellyResult['base'] == $component && $componentsFromShellyResult['number'] == $channel) || $componentsFromShellyResult['base'] == $component && $componentsFromShellyResult['number'] == '' || $component == '' && $channel == '' || $isSensorOfSelectedBTHomeDevice) {
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
                                //Erst übersetzen, DANN die Kanalnummer anhängen - siehe Kommentar oben.
                                $name = $this->Translate($tmpComponent['actionWithExtraVariable']['name']) . ' ' . $componentsFromShellyResult['number'];
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

