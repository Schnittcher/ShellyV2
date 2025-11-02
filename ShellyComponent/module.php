<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

    class ShellyComponent extends ShellyModuleBase
    {
        public function Create()
        {
            parent::Create();
            $this->RegisterPropertyString('Component', '');
            $this->RegisterPropertyInteger('Channel', 0);
        }

        public function GetConfigurationForm()
        {
            $reflector = new ReflectionClass($this);
            $Form = json_decode(file_get_contents(dirname($reflector->getFileName()) . '/form.json'), true);

            $Form['elements'][4]['values'] = json_decode($this->GetBuffer('variableList'), true);

            return json_encode($Form);
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
        }
    }

