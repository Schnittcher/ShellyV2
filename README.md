[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Symcon%20Version-8.1%20%3E-blue.svg)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Check Style](https://github.com/Schnittcher/ShellyV2/actions/workflows/style.yml/badge.svg)](https://github.com/Schnittcher/ShellyV2/actions/workflows/style.yml)

# Shelly Gen2+
Mit diesem Modul können alle Shellies ab Generation 2 in Symcon eingebunden werden.

## Inhaltsverzeichnis
- [Shelly Gen2+](#shelly-gen2)
	- [Inhaltsverzeichnis](#inhaltsverzeichnis)
	- [1. Voraussetzungen](#1-voraussetzungen)
		- [1.1 Aktiviertes MQTT-Protokoll](#11-aktiviertes-mqtt-protokoll)
		- [1.2 Shelly BLU-Geräte](#12-shelly-blu-geräte)
	- [2. Funktionsumfang](#2-funktionsumfang)
	- [3. Enthaltene Module](#3-enthaltene-module)
	- [4. Installation](#4-installation)
	- [5. Konfiguration in IP-Symcon](#5-konfiguration-in-ip-symcon)
	- [6. Spenden](#6-spenden)
	- [7. Lizenz](#7-lizenz)

## 1. Voraussetzungen

* mindestens IPS Version 8.1
* Aktiviertes MQTT Protokoll beim Shelly Gerät
* MQTT Server oder MQTT Client

### 1.1 Aktiviertes MQTT-Protokoll
Das MQTT Protokoll muss bei jedem Shelly aktiviert sein, damit das Gerät von IP-Symcon mit diesem Modul bedient und angelegt werden kann.
Die Einrichtung wird über das Shelly Webinterface vorgenommen:

Settings -> MQTT -> Enable

Mindestens "Enable RPC over MQTT" und "RPC status notifications over MQTT" müssen aktiviert sein.

Unter Server wird die IP von IP-Symcon und der MQTT Port eingetragen.
Der Standard Port für MQTT ist 1883, sollte dieser in IP-Symcon geändert worden sein ist er unter I/O Instanzen -> Server Socket (MQTT Server #InstanzID) zu finden.

Sollen Username und Passwort verwendet werden müssen diese Daten in IP-Symcon unter Splitter Instanzen -> MQTT Server hinterlegt werden.
Die selben Zugangsdaten müssen über das Shelly Webinterface unter Settings -> MQTT hinterlegt werden.

### 1.2 Shelly BLU-Geräte
Shelly BLU-Geräte (Bluetooth) sprechen nicht selbst mit IP-Symcon, sondern über ein Gateway: ein Shelly BLU Gateway oder ein anderer Shelly mit Bluetooth-Gateway-Funktion. Das BLU-Gerät muss am Gateway angelernt sein, das Gateway selbst muss wie oben beschrieben per MQTT mit IP-Symcon verbunden sein. Ein BLU-Gerät kann an mehreren Gateways angelernt sein.

## 2. Funktionsumfang
* Alle Shellies ab Generation 2: Schalten (Switch, Cover, Light/Dimmer, RGB, RGBW, CCT, RGBCCT), Messwerte (Energie, Spannung, Strom, Temperatur, Luftfeuchtigkeit, Helligkeit), Eingänge, Rauch- und Flutsensor, Shelly Pill, Shelly Cury, Shelly Camera, DALI, Leitungsschutzschalter (CB), virtuelle Komponenten (Boolean, Number, Enum, Text) und weitere. Die Komponenten werden vom Gerät selbst ermittelt, welche Variablen es je Komponente gibt steht in [Komponenten.md](Komponenten.md).
* "Powered by Shelly" Geräte.
* Welche Variablen es gibt, wird je Instanz in einer Liste ausgewählt. Komponenten, die Fehler melden, haben zusätzlich die Variablen "Fehler" (übersetzte Fehlercodes) und "Störung".
* Shelly BLU-Geräte über ein oder mehrere Gateways: Sensoren (z. B. Kontakt, Bewegung, Temperatur, Luftfeuchtigkeit), Tasten, Drehrad, Thermostat (BLU TRV), Empfang je Gateway und das stärkste Gateway, z. B. für die Raumerkennung.
* Funktionen für Skripte: Aktionen über die Variablen, beliebige RPC-Aufrufe und der Key-Value-Store der Geräte (KVS).
* Der Konfigurator findet die Shellies im Netzwerk und legt die Instanzen mit der richtigen Konfiguration an.

## 3. Enthaltene Module

* [ShellyBLUDevice](ShellyBLUDevice/README.md)
  * Ein Shelly BLU-Gerät (z. B. Button, Door/Window, Motion), das an einem oder mehreren Gateways angelernt ist, mit Messwerten, Tasten und dem Empfang je Gateway.
* [ShellyComponent](ShellyComponent/README.md)
  * Eine einzelne Komponente eines Shellys (z. B. switch:0) als eigene Instanz.
* [ShellyConfigurator](ShellyConfigurator/README.md)
  * Findet die Shellies per MQTT im Netzwerk und legt sie als komplettes Gerät oder als einzelne Komponenten an.
* [ShellyDevice](ShellyDevice/README.md)
  * Legt alle passenden Variablen eines Shellys an.
* [ShellyXT1Device](ShellyXT1Device/README.md)
  * Bindet "Powered by Shelly" Geräte ein.

## 4. Installation
Installation über den IP-Symcon Module Store.

## 5. Konfiguration in IP-Symcon
Nach erfolgreicher Installation über den Module Store muss der Konfigurator angelegt werden, ggf. muss hier das Gateway (MQTT Server oder MQTT Client) angepasst werden.
Danach sollten die Shellies direkt im Konfigurator gefunden werden.

## 6. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 7. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)
