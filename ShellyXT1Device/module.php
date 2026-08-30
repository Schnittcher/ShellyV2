<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/MQTTHelper.php';
require_once __DIR__ . '/../libs//vendor/SymconModulHelper/DebugHelper.php';
require_once __DIR__ . '/../libs/XMODServices.php';

class ShellyXT1Device extends IPSModule
{
    use MQTTHelper;
    use DebugHelper;
    use XMODServices;

    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyString('MQTTTopic', '');
        $this->RegisterPropertyString('XMODServiceType', '');

        $this->RegisterVariableBoolean('Reachable', $this->Translate('Reachable'), [
            'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
            'OPTIONS'         => json_encode(
                [
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

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
        $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
        $XMODServiceType = $this->ReadPropertyString('XMODServiceType');

        if (array_key_exists($XMODServiceType, self::$services)) {
            $position = 0;
            foreach (self::$services[$XMODServiceType] as $key => $variable) {
                $this->MaintainVariable($key, $this->Translate($variable['name']), $variable['type'], $this->TranslatePresentation($variable['presentation']), $position, true);
                if ($variable['action']) {
                    $this->EnableAction($key);
                }
                $position++;
            }
        } else {
            $this->LogMessage('This service is not available', KL_ERROR);
        }
    }

    //Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve
    public function RequestAction($Ident, $Value)
    {
        $XMODServiceType = $this->ReadPropertyString('XMODServiceType');

        if (array_key_exists($XMODServiceType, self::$services)) {
            if (array_key_exists($Ident, self::$services[$XMODServiceType])) {
                switch (self::$services[$XMODServiceType][$Ident]['type']) {
                    case VARIABLETYPE_BOOLEAN:
                        $this->callRPCFunction('Boolean.Set', ['owner' => 'service:0', 'role' => $Ident, 'value' => $Value]);
                        break;
                    case VARIABLETYPE_FLOAT:
                    case VARIABLETYPE_INTEGER:
                        $this->callRPCFunction('number.set', ['owner' => 'service:0', 'role' => $Ident, 'value' => $Value]);
                        break;
                    case VARIABLETYPE_STRING:
                        $this->callRPCFunction('enum.set', ['owner' => 'service:0', 'role' => $Ident, 'value' => $Value]);
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                $this->LogMessage('Request Action :: Ident didn´t found in service.', KL_ERROR);
            }
        } else {
            $this->LogMessage('Request Action :: This service is not available.', KL_ERROR);
        }
        parent::RequestAction($Ident, $Value);
    }

    //Ausnahme für Shellys, welche nicht korrekt definiert sind, wie zum Beispiel WaterValve
    public function ReceiveData($JSONString)
    {
        $Buffer = json_decode($JSONString, true);
        $this->SendDebug('JSON', $Buffer, 0);

        $Payload = json_decode($Buffer['Payload'], true);

        if (fnmatch('*/online', $Buffer['Topic'])) {
            $this->SetValue('Reachable', $Payload);
        }

        if (fnmatch('*/events/rpc', $Buffer['Topic'])) {
            if (array_key_exists('params', $Payload)) {
                $this->SendDebug('Test', $JSONString, 0);

                //Absicherung: Ohne diese Prüfung würde self::$services[''] (falls XMODServiceType
                //leer/ungültig ist) $XMODService zu null machen, und findKeysByReceive() bekäme
                //null statt eines Arrays übergeben - das bricht mit einem fatalen TypeError ab und
                //verhindert jede weitere Datenverarbeitung für diese Instanz.
                $XMODServiceType = $this->ReadPropertyString('XMODServiceType');
                if (!array_key_exists($XMODServiceType, self::$services)) {
                    $this->LogMessage('ReceiveData :: This service is not available.', KL_ERROR);
                    parent::ReceiveData($JSONString);
                    return;
                }
                $XMODService = self::$services[$XMODServiceType];

                foreach ($Payload['params'] as $key => $value) {
                    //Mehrere Variablen können denselben "receive"-Key haben (z.B. ein "object:200"
                    //mit mehreren Unterwerten wie counter.total, total_current, phase_a.voltage, ...)
                    //- deshalb ALLE passenden Idents holen, nicht nur den ersten Treffer.
                    $idents = $this->findKeysByReceive($XMODService, $key);

                    foreach ($idents as $ident) {
                        if (array_key_exists('receivePayloadKey', $XMODService[$ident])) {
                            //IPS_LogMessage('test', print_r($Payload['params'][$key], true));
                            if (array_key_exists($XMODService[$ident]['receivePayloadKey'], $Payload['params'][$key])) {
                                $this->SetValue($ident, $Payload['params'][$key][$XMODService[$ident]['receivePayloadKey']]);
                            }
                        }

                        if (array_key_exists('value', $Payload['params'][$key])) {
                            if (is_array($Payload['params'][$key]['value']) && array_key_exists('objectValue', $XMODService[$ident])) {
                                $keyPath = $XMODService[$ident]['objectValue'];
                                $this->SetValue($ident, $this->getValueToKeyPath($Payload['params'][$key]['value'], $keyPath));
                            } else {
                                $this->SetValue($ident, $Payload['params'][$key]['value']);
                            }
                        }
                    }
                }
            }
        }

        parent::ReceiveData($JSONString);
    }

private function getValueToKeyPath($array, $keyPath)
{
    $value = $array;
    if (is_array($value) && $keyPath) {
        $keys = explode(':', $keyPath);
        $result = $value;
        foreach ($keys as $k) {
            $result = $result[$k] ?? null;
            if ($result === null) {
                break;
            }
        }
    } else {
        $result = $value;
    }
    return $result;
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

    //Liefert ALLE Idents, deren 'receive' zum gesuchten Event-Key passt (nicht nur den ersten
    //Treffer) - mehrere XMODServices-Einträge können denselben 'receive'-Wert nutzen (z.B. mehrere
    //Unterwerte, die alle aus demselben "object:200"-Event stammen).
    private function findKeysByReceive(array $array, string $searchReceive)
    {
        $idents = [];
        foreach ($array as $key => $config) {
            if (isset($config['receive']) && $config['receive'] === $searchReceive) {
                $idents[] = $key;
            }
        }
        return $idents;
    }

    private function TranslatePresentation($Presentation)
    {
        if (isset($Presentation['PREFIX'])) {
            $Presentation['PREFIX'] = $this->Translate($Presentation['PREFIX']);
        }
        if (isset($Presentation['SUFFIX'])) {
            $Presentation['SUFFIX'] = $this->Translate($Presentation['SUFFIX']);
        }
        if (isset($Presentation['OPTIONS'])) {
            $Options = $Presentation['OPTIONS'];
            foreach ($Options as &$Option) {
                $Option['Caption'] = $this->Translate($Option['Caption']);
            }
            $Presentation['OPTIONS'] = json_encode($Options);
        }
        if (isset($Presentation['INTERVALS'])) {
            $Intervals = $Presentation['INTERVALS'];
            foreach ($Intervals as &$Interval) {
                if (isset($Interval['ConstantValue'])) {
                    $Interval['ConstantValue'] = $this->Translate($Interval['ConstantValue']);
                }
                if (isset($Interval['PrefixValue'])) {
                    $Interval['PrefixValue'] = $this->Translate($Interval['PrefixValue']);
                }
                if (isset($Interval['SuffixValue'])) {
                    $Interval['SuffixValue'] = $this->Translate($Interval['SuffixValue']);
                }
            }
            $Presentation['INTERVALS'] = json_encode($Intervals);
        }
        return $Presentation;
    }
}
