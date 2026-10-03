<?php

declare(strict_types=1);

//Stream-Objekte (Medien, Typ Stream) für den RTSP-Stream einer Shelly Camera. Gemeinsam für ShellyDevice (ganzes
//Gerät) und ShellyComponent (einzelne Komponente), eingebunden über ShellyModuleBase. Die Objekte werden nach dem
//Einlesen der Komponenten automatisch angelegt bzw. aktualisiert (abschaltbar über "AutoCreateStreams"), sobald das
//Gerät eine Kamera-Komponente (camera:N) hat und seine IP-Adresse bekannt ist. Die Formularfelder
//(AutoCreateStreams, StreamUser, StreamPassword, CreateStreams) stehen in den form.json beider Module und werden
//nur bei Kameras angezeigt.
trait CameraStream
{
    //Stream 0 = Hauptstream, Stream 1 = zweiter (kleinerer) Stream der Kamera.
    private static $cameraStreamNumbers = [0, 1];

    //AutoCreateStreams: Stream-Objekte automatisch anlegen. StreamUser/StreamPassword: optional, nur nötig, wenn am
    //Gerät die Authentifizierung aktiv ist (Benutzer ist laut Weboberfläche der Kamera "admin").
    protected function registerCameraStreamProperties()
    {
        $this->RegisterPropertyBoolean('AutoCreateStreams', true);
        $this->RegisterPropertyString('StreamUser', '');
        $this->RegisterPropertyString('StreamPassword', '');
    }

    //Blendet die Stream-Felder und den Button im Konfigurationsformular nur bei Kameras ein.
    protected function applyCameraStreamFormVisibility(array $Form)
    {
        $visible = $this->hasCameraComponent();
        $streamItems = ['AutoCreateStreams', 'StreamUser', 'StreamPassword', 'CreateStreams'];
        foreach (['elements', 'actions'] as $section) {
            foreach ($Form[$section] ?? [] as $index => $item) {
                if (isset($item['name']) && in_array($item['name'], $streamItems, true)) {
                    $Form[$section][$index]['visible'] = $visible;
                }
            }
        }
        return $Form;
    }

    //Wird nach dem Einlesen der Komponenten aufgerufen (ReceiveData): legt die Stream-Objekte an, wenn die
    //Automatik aktiv ist, eine Kamera vorhanden ist und die IP-Adresse des Geräts bekannt ist.
    protected function autoMaintainCameraStreams()
    {
        if (@$this->ReadPropertyBoolean('AutoCreateStreams') && $this->hasCameraComponent() && $this->GetBuffer('deviceIP') != '') {
            $this->maintainCameraStreams();
        }
    }

    //Button im Formular ("Stream-Objekte aktualisieren"): z.B. nach geänderten Zugangsdaten. Liefert den
    //Hinweistext zurück (der Button gibt ihn per echo als Meldung aus).
    public function CreateCameraStreams(): string
    {
        return $this->maintainCameraStreams();
    }

    //Legt die Stream-Objekte an bzw. aktualisiert ihre Adresse. Die IP-Adresse stammt aus dem Status des Geräts
    //(wifi.sta_ip bzw. eth.ip), die Adresse ist laut Weboberfläche der Kamera rtsp://<IP>/stream/0 (Hauptstream)
    //bzw. /stream/1. Mit Authentifizierung am Gerät werden Benutzer/Passwort aus den Eigenschaften in die Adresse
    //übernommen (so verlangt es Symcon). Gibt den Hinweistext zurück.
    private function maintainCameraStreams()
    {
        if (!$this->hasCameraComponent()) {
            return $this->Translate('This device has no camera component.');
        }
        $ip = $this->GetBuffer('deviceIP');
        if ($ip == '') {
            return $this->Translate('The IP address of the device is not known yet. Please read the components first.');
        }

        $user = $this->ReadPropertyString('StreamUser');
        $credentials = '';
        if ($user != '') {
            $credentials = rawurlencode($user) . ':' . rawurlencode($this->ReadPropertyString('StreamPassword')) . '@';
        }

        foreach (self::$cameraStreamNumbers as $number) {
            $url = 'rtsp://' . $credentials . $ip . '/stream/' . $number;
            $ident = 'CameraStream' . $number;
            $mediaID = @IPS_GetObjectIDByIdent($ident, $this->InstanceID);
            if (!$mediaID) {
                $mediaID = IPS_CreateMedia(MEDIATYPE_STREAM);
                IPS_SetParent($mediaID, $this->InstanceID);
                IPS_SetIdent($mediaID, $ident);
                IPS_SetName($mediaID, $this->Translate('Stream') . ' ' . $number);
            }
            //Nur schreiben, wenn sich die Adresse geändert hat (z.B. neue IP oder neue Zugangsdaten).
            if (IPS_GetMedia($mediaID)['MediaFile'] != $url) {
                IPS_SetMediaFile($mediaID, $url, false);
            }
        }

        //RTSP kann am Gerät ausgeschaltet sein (Config rtsp.enable) - dann gibt es noch kein Bild.
        $configs = json_decode($this->GetBuffer('componentConfigs'), true);
        if (is_array($configs)) {
            foreach ($configs as $key => $config) {
                if (strpos((string) $key, 'camera:') === 0 && ($config['rtsp']['enable'] ?? null) === false) {
                    return $this->Translate('The stream objects were created, but RTSP is switched off on the device. Switch on the variable "Camera RTSP" to get a picture.');
                }
            }
        }
        return $this->Translate('The stream objects were created / updated.');
    }

    //Hat das Gerät bzw. die Instanz eine Kamera-Komponente (camera:N)? Geprüft wird die konfigurierte Komponente
    //(ShellyComponent) und die Variablenliste (Buffer und gespeicherte Eigenschaft) - "camera." mit Punkt, damit
    //"camerazone" nicht zählt.
    private function hasCameraComponent()
    {
        if (@$this->ReadPropertyString('Component') == 'camera') {
            return true;
        }
        $lists = [json_decode($this->GetBuffer('variableList'), true), json_decode($this->ReadPropertyString('VariableList'), true)];
        foreach ($lists as $list) {
            foreach (is_array($list) ? $list : [] as $item) {
                if (strpos((string) ($item['CleanKeyPath'] ?? ''), 'camera.') === 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
