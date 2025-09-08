<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/ShellyModuleBase.php';

//Die Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve, werden hier in dieser Datei fest hinterlegt


    class ShellyDevice extends ShellyModuleBase
    {
        use notDefinedComponents;

        public function Create()
        {
            parent::Create();
            $this->RegisterPropertyString('ModelID', '');
        }

        public function GetConfigurationForm()
        {
            $reflector = new ReflectionClass($this);
            $Form = json_decode(file_get_contents(dirname($reflector->getFileName()) . '/form.json'), true);

            $Form['elements'][2]['items'][0]['values'] = json_decode($this->GetBuffer('variableList'), true);

            return json_encode($Form);
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();

            //Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve
            $modelID = $this->ReadPropertyString('ModelID');

            switch ($modelID) {
                case 'S3XT-0S':
                    $presentation = [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                        'SUFFIX'       => ' %',
                        'USAGE_TYPE'   => 3,
                        'STEP_SIZE'    => 10,
                    ];
                    $this->MaintainVariable('S3XT_0S_Position', $this->Translate('Postion'), VARIABLETYPE_INTEGER, $presentation, 0, true);
                    $this->EnableAction('S3XT_0S_Position');
                    break;

                default:
                    # code...
                    break;
            }
        }

        //Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve
        public function RequestAction($Ident, $Value)
        {
            $modelID = $this->ReadPropertyString('ModelID');
            switch ($modelID) {
                case 'S3XT-0S':
                    $this->callRPCFunction('number.set', ['id' => '200', 'value' => $Value]);
                    return;
            }

            parent::RequestAction($Ident, $Value);
        }

        //Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve
        public function ReceiveData($JSONString)
        {
            $modelID = $this->ReadPropertyString('ModelID');

            $Buffer = json_decode($JSONString, true);
            $this->SendDebug('JSON', $Buffer, 0);

            $Payload = json_decode($Buffer['Payload'], true);

            if (fnmatch('*/events/rpc', $Buffer['Topic'])) {
                switch ($modelID) {
                case 'S3XT-0S':
                    if (array_key_exists('params', $Payload)) {
                        $this->SendDebug('Test', $JSONString, 0);
                        if (array_key_exists('number:200', $Payload['params'])) {
                            if (array_key_exists('value', $Payload['params']['number:200'])) {
                                $this->SetValue('S3XT_0S_Position', intval($Payload['params']['number:200']['value']));
                            }
                        }
                    }
                    break;

                default:
                    # code...
                    break;

            }
            }
            parent::ReceiveData($JSONString);
        }
    }
