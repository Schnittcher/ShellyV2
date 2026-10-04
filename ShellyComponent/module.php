<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

class ShellyComponent extends ShellyModuleBase
{
    public function Create(): void
    {
        parent::Create();
        $this->RegisterPropertyString('Component', '');
        $this->RegisterPropertyInteger('Channel', 0);
    }

    public function GetConfigurationForm(): string
    {
        $reflector = new ReflectionClass($this);
        $Form = json_decode(file_get_contents(dirname($reflector->getFileName()) . '/form.json'), true);
        $Form['elements'][4]['values'] = json_decode($this->GetBuffer('variableList'), true);

        //Stream-Felder und -Buttons nur bei Kameras anzeigen (siehe libs/CameraStream.php)
        $Form = $this->applyCameraStreamFormVisibility($Form);

        return json_encode($Form);
    }

    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();
        $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
        if ($MQTTTopic != '') {
            if ($this->HasActiveParent()) {
                $this->requestComponentsStatus();
            }
        }
    }
}

