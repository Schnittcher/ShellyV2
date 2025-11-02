# Shelly Gen2+
Mit diesem Modul können alle Shellies ab Generation 2 in Symcon eingebunden werden.

## Inhaltverzeichnis
- [Shelly Gen2+](#shelly-gen2)
	- [Inhaltverzeichnis](#inhaltverzeichnis)
	- [1. Voraussetzungen](#1-voraussetzungen)
		- [1.1 Aktiviertes MQTT protokoll](#11-aktiviertes-mqtt-protokoll)
	- [2. Enthaltene Module](#2-enthaltene-module)
	- [3. Installation](#3-installation)
	- [4. Konfiguration in IP-Symcon](#4-konfiguration-in-ip-symcon)
	- [5. Spenden](#5-spenden)
	- [6. Lizenz](#6-lizenz)
   
## 1. Voraussetzungen

* mindestens IPS Version 8.1
* Aktiviertes MQTT Protokoll beim Shelly Gerät
* MQTT Server oder MQTT Client

### 1.1 Aktiviertes MQTT protokoll
Das MQTT Protokoll muss bei jedem Shelly aktiviert sein, damit das Gerät von IP-Symcon mit diesem Modul bedgefuncen und angelegt werden kann.
Die Einrichtung wird über das Shelly Webinterface vorgenommen:

Settings -> MQTT -> Enable

Mindestens "Enable RPC over MQTT" und "RPC status notifications over MQTT" müssen aktiviert sein.

Unter Server wird die IP von IP-Symcon und der MQTT Port eingetragen.
Der Standard Port für MQTT ist 1883, sollte dieser in IP-Symcon geändert worden sein ist er unter I/O Instanzen -> Server Socket (MQTT Server #InstanzID) zu finden.

Sollen Username und Passwort verwendet werden müssen diese Daten in IP-Symcon unter Splitter Instanzen -> MQTT Server hinterlegt werden.
Die selben Zugangsdaten müssen über das Shelly Webinterface unter Settings -> MQTT hinterlegt werden.

## 2. Enthaltene Module

* [ShellyComponent](ShellyComponent/README.md)
* [ShellyConfigurator](ShellyConfigurator/README.md)
* [ShellyDevice](ShellyDevice/README.md)

## 3. Installation
Installation über den IP-Symcon Module Store.

## 4. Konfiguration in IP-Symcon
Nach erfolgreicher Installation über den Module Store muss der Konfiguration angelegt werden, ggf. muss hier das Gateway angepasst werden.
Danach sollten die Shellies direkt im Konfigurator gefunden werden.

## 5. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 6. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)
