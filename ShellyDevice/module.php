<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

class ShellyDevice extends ShellyModuleBase
{
    public function Create(): void
    {
        parent::Create();
        $this->RegisterPropertyString('ModelID', '');
    }

    public function GetConfigurationForm(): string
    {
        $reflector = new ReflectionClass($this);
        $Form = json_decode(file_get_contents(dirname($reflector->getFileName()) . '/form.json'), true);

        $Form['elements'][2]['values'] = $this->variableListValues();

        //Stream-Felder und -Buttons nur bei Kameras anzeigen (siehe libs/CameraStream.php)
        $Form = $this->applyCameraStreamFormVisibility($Form);

        return json_encode($Form);
    }

    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();
        //Beim Anlegen, nach Änderungen und nach einem Update vollständig lesen, sonst nur der Status (siehe planStartRead()).
        $this->planStartRead();
    }
}
