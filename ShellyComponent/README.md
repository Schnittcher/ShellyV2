# ShellyDevice
Diese Instanz legt alle passenden Variablen für das Shelly an.

### Inhaltsverzeichnis

- [ShellyDevice](#shellydevice)
    - [Inhaltsverzeichnis](#inhaltsverzeichnis)
    - [1. Funktionsumfang](#1-funktionsumfang)
    - [2. Voraussetzungen](#2-voraussetzungen)
    - [3. Software-Installation](#3-software-installation)
    - [4. Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
    - [5. Statusvariablen und Profile](#5-statusvariablen-und-profile)
    - [6. Visualisierung](#6-visualisierung)
    - [7. PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Erstellt alle Variablen in Symcon und bietet die Funktionalität des Shellies.

### 2. Voraussetzungen

- IP-Symcon ab Version 8.1

### 3. Software-Installation

* Über den Module Store (Testing Version)

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'ShellyDevice'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
MQTT Topic         | Hier wird das MQTT Topic des Shellies eingetragen.
Debug: Fehlende Idents         | Wenn diese Checkbox aktiviert ist, werden mehr Debug Informationen im Debug Fenster angezeigt.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

### 6. Visualisierung

Die Funktionalität, die das Modul in der Visualisierung bietet.

### 7. PHP-Befehlsreferenz

`boolean SHY_getComponents(integer $InstanzID);`
Ruft alle Components / Services ab und legt dazu die Variablen an, diese Funktion wird automatisch beim Speichern der Instanz aufgerufen.

Beispiel:
`SHY_getComponents(12345);`

`boolean SHY_callRPCFunction(integer $InstanzID, string $method, array $params);`
Mit diesr Funktion können als RPC Funktionen von den Shellies ausgeführt werden.
Die RPC Funktionen können in der API Beschreibung der Shellies gefunden werden: https://shelly-api-docs.shelly.cloud/gen2/

Beispiel:
`SHY_callRPCFunction(12345, 'Switch.Set, ['id' => 0, 'on'=> true]);`