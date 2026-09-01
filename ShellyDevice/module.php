<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

    class ShellyDevice extends ShellyModuleBase
    {
        public function Create()
        {
            parent::Create();
            $this->RegisterPropertyString('ModelID', '');
        }

        public function GetConfigurationForm()
        {
            $reflector = new ReflectionClass($this);
            $Form = json_decode(file_get_contents(dirname($reflector->getFileName()) . '/form.json'), true);

            $Form['elements'][2]['values'] = json_decode($this->GetBuffer('variableList'), true);

            return json_encode($Form);
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
            $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
            if ($MQTTTopic != '') {
                if ($this->HasActiveParent()) {
                    //TEST/EXPERIMENTELL: requestComponentsStatus() entscheidet anhand der Property
                    //'UseGetComponentsForStatus' zwischen getComponentsViaStatus() (Standard) und
                    //getComponentsViaGetComponents() (Opt-in, Schritt 4 der Migration) - siehe
                    //ShellyModuleBase.php.
                    $this->requestComponentsStatus();
                }
            }
        }
    }
