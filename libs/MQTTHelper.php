<?php

declare(strict_types=1);
define('MQTT_GROUP_TOPIC', 'shellies');
trait MQTTHelper
{
    //Bei IPSModuleStrict ist der Datenfluss HEX-kodiert (siehe Symcon-Doku, Unterschiede IPSModule/IPSModuleStrict):
    //"Payload" wird gesendet und empfangen als bin2hex()/hex2bin() der Nutzdaten (UTF-8-Text). Das "Topic" bleibt
    //Klartext. Die Nutzdaten kommen dadurch ohne die frühere Doppelkodierung an (kein utf8_decode mehr nötig).
    protected function sendMQTT($Topic, $Payload)
    {
        $resultServer = true;
        //MQTT Server
        $Server['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
        $Server['PacketType'] = 3;
        $Server['QualityOfService'] = 0;
        $Server['Retain'] = false;
        $Server['Topic'] = $Topic;
        $Server['Payload'] = bin2hex((string) $Payload);
        $ServerJSON = json_encode($Server, JSON_UNESCAPED_SLASHES);
        $this->SendDebug(__FUNCTION__ . 'MQTT Server', $ServerJSON, 0);
        $resultServer = @$this->SendDataToParent($ServerJSON);

        if ($resultServer === false) {
            $last_error = error_get_last();
            echo $last_error['message'];
        }
    }

    //Liefert die Nutzdaten (Text) aus einem empfangenen MQTT-Paket (hex2bin des "Payload"-Felds).
    protected function decodeMQTTPayload(array $Buffer): string
    {
        $decoded = hex2bin((string) ($Buffer['Payload'] ?? ''));
        return $decoded === false ? '' : $decoded;
    }
}
