<?php

declare(strict_types=1);

//Ergänzt SendDebug um die Möglichkeit, Arrays, Objekte und Booleans auszugeben (Variante für IPSModuleStrict;
//dort ist SendDebug(string $Message, string $Data, int $Format): bool, das vendorte DebugHelper ohne Typen ist
//damit nicht kompatibel). Nach dem Vorbild von DebugHelper (Michael Tröger, nall-chan.net).
trait StrictDebugHelper
{
    protected function SendDebug(string $Message, mixed $Data, int $Format): bool
    {
        if (is_array($Data)) {
            if (count($Data) == 0) {
                return $this->SendDebug($Message, '[EMPTY]', 0);
            }
            if (count($Data) > 25) {
                $this->SendDebug($Message, array_slice($Data, 0, 20), 0);
                $this->SendDebug($Message . ':CUT', '-------------CUT-----------------', 0);
                return $this->SendDebug($Message, array_slice($Data, -5, null, true), 0);
            }
            foreach ($Data as $Key => $DebugData) {
                $this->SendDebug($Message . ':' . $Key, $DebugData, 0);
            }
            return true;
        }
        if (is_object($Data)) {
            if (count(get_object_vars($Data)) == 0) {
                return $this->SendDebug($Message, '[EMPTY]', 0);
            }
            foreach ($Data as $Key => $DebugData) {
                $this->SendDebug($Message . '->' . $Key, $DebugData, 0);
            }
            return true;
        }
        if (is_bool($Data)) {
            return parent::SendDebug($Message, $Data ? 'TRUE' : 'FALSE', 0);
        }
        if (IPS_GetKernelRunlevel() == KR_READY) {
            return parent::SendDebug($Message, (string) $Data, $Format);
        }
        return $this->LogMessage($Message . ':' . (string) $Data, KL_DEBUG);
    }
}
