<?php

declare(strict_types=1);

trait ShellyRPCHelper
{
    protected function ShellyRPCviaHTTP(string $ip, string $method, array $params = [], int $timeout = 5)
    {
        $url = "http://{$ip}/rpc";

        $payload = json_encode([
            'id'     => 1,
            'method' => $method,
            'params' => $params
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => $timeout
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Shelly RPC Fehler: ' . $error);
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}