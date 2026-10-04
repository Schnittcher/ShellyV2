<?php

declare(strict_types=1);

//Key-Value-Store (KVS) der Shellies: persistenter Speicher für Schlüssel/Wert-Paare auf dem Gerät (z.B. zum Austausch mit einem
//Shelly-Skript). Das KVS hat keinen Status und keine Konfiguration, es gibt nur RPC-Methoden (KVS.Set, KVS.Get, KVS.GetMany, KVS.List,
//KVS.Delete) - deshalb gibt es dafür keine Variablen, sondern öffentliche Funktionen der Instanz (SHY_KVSSet(), SHY_KVSGet(), ...).
//Grenzen laut Shelly-Doku: Schlüssel höchstens 42 Zeichen, Wert (als JSON) höchstens 253 Zeichen, höchstens 50 Schlüssel.
//
//Die Antwort kommt asynchron per MQTT: Die Anfrage bekommt eine eigene Antwort-Adresse (<topic>/kvs/<InstanzID>/<Anfrage-ID>), ReceiveData()
//legt die Antwort in einen Buffer, und die Funktion wartet darauf (höchstens 5 Sekunden, Muster wie beim Konfigurator).
trait ShellyKVS
{
    //Schlüssel hinzufügen oder ändern. $Value ist ein beliebiger JSON-Wert (Text, Zahl, Boolean, Array). Gibt true zurück, wenn das Gerät den
    //Wert übernommen hat. (Die Funktionen der Instanz kennen keine optionalen Parameter - deshalb gibt es KVSSetIfUnchanged() separat.)
    public function KVSSet(string $Key, mixed $Value): bool
    {
        return $this->kvsCall('KVS.Set', ['key' => $Key, 'value' => $Value]) !== null;
    }

    //Wie KVSSet(), ändert aber nur, wenn der Eintrag seit dem Lesen nicht anderweitig geändert wurde: $Etag stammt aus KVSGet(). Ist der Wert
    //inzwischen ein anderer, lehnt das Gerät ab (false).
    public function KVSSetIfUnchanged(string $Key, mixed $Value, string $Etag): bool
    {
        return $this->kvsCall('KVS.Set', ['key' => $Key, 'value' => $Value, 'etag' => $Etag]) !== null;
    }

    //Wert eines Schlüssels als JSON: {"etag": "...", "value": ...}. Leer, wenn der Schlüssel nicht existiert oder das Gerät nicht antwortet.
    public function KVSGet(string $Key): string
    {
        $result = $this->kvsCall('KVS.Get', ['key' => $Key]);
        return $result === null ? '' : json_encode($result);
    }

    //Schlüssel löschen. Gibt true zurück, wenn das Gerät das Löschen bestätigt hat.
    public function KVSDelete(string $Key): bool
    {
        return $this->kvsCall('KVS.Delete', ['key' => $Key]) !== null;
    }

    //Die Schlüssel, die zum Muster passen ("*" = alle; "*" steht für beliebig viele Zeichen, "?" für ein Zeichen, "," trennt Muster), als JSON:
    //{"keys": {"schluessel": {"etag": "..."}}, "rev": 12}. Leer bei Fehler.
    public function KVSList(string $Match): string
    {
        return $this->kvsCollect('KVS.List', 'keys', $Match);
    }

    //Wie KVSList(), aber mit den Werten: {"items": {"schluessel": {"etag": "...", "value": ...}}, "rev": 12}. Leer bei Fehler.
    public function KVSGetMany(string $Match): string
    {
        return $this->kvsCollect('KVS.GetMany', 'items', $Match);
    }

    //Die Antwort einer KVS-Anfrage (siehe ReceiveData()) für die wartende Funktion ablegen.
    protected function storeKVSResponse(string $Topic, string $Payload)
    {
        if (preg_match('#/kvs/' . $this->InstanceID . '/([0-9a-f]{8})/rpc$#', $Topic, $matches)) {
            $this->SetBuffer('kvsResponse_' . $matches[1], $Payload);
            return true;
        }
        return false;
    }

    //Eine Anfrage senden und auf die Antwort warten. Rückgabe: das "result" der Antwort, null bei Fehlermeldung des Geräts oder Zeitüberschreitung.
    private function kvsCall(string $method, array $params, float $timeout = 5.0)
    {
        $requestID = substr(md5(uniqid('', true)), 0, 8);
        $topic = $this->ReadPropertyString('MQTTTopic');
        if ($topic == '') {
            return null;
        }
        $this->SetBuffer('kvsResponse_' . $requestID, '');
        $payload = [
            'id'     => 1,
            'src'    => $topic . '/kvs/' . $this->InstanceID . '/' . $requestID,
            'method' => $method,
            'params' => count($params) > 0 ? $params : new stdClass(),
        ];
        $this->sendMQTT($topic . '/rpc', json_encode($payload, JSON_UNESCAPED_SLASHES));

        $raw = '';
        $end = microtime(true) + $timeout;
        while (microtime(true) < $end) {
            $raw = $this->GetBuffer('kvsResponse_' . $requestID);
            if ($raw != '') {
                break;
            }
            IPS_Sleep(50);
        }
        $this->SetBuffer('kvsResponse_' . $requestID, '');
        if ($raw == '') {
            $this->SendDebug($method, 'Keine Antwort des Geräts innerhalb von ' . $timeout . ' Sekunden.', 0);
            return null;
        }
        $response = json_decode($raw, true);
        if (!is_array($response) || isset($response['error']) || !array_key_exists('result', $response)) {
            $this->SendDebug($method, 'Fehler: ' . $raw, 0);
            return null;
        }
        return is_array($response['result']) ? $response['result'] : [];
    }

    //KVS.List / KVS.GetMany: Die Antwort ist seitenweise (offset/total), alle Seiten werden zusammengefügt.
    private function kvsCollect(string $method, string $field, string $match)
    {
        $collected = [];
        $revision = null;
        $offset = 0;
        for ($page = 0; $page < 30; $page++) {
            $result = $this->kvsCall($method, ['match' => $match, 'offset' => $offset]);
            if ($result === null) {
                return '';
            }
            $revision = $result['rev'] ?? $revision;
            $part = is_array($result[$field] ?? null) ? $result[$field] : [];
            $collected = array_merge($collected, $part);
            $offset += count($part);
            if (count($part) == 0 || $offset >= (int) ($result['total'] ?? $offset)) {
                break;
            }
        }
        return json_encode([$field => $collected === [] ? new stdClass() : $collected, 'rev' => $revision]);
    }
}
