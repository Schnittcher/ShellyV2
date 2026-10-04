# ShellyBLUDevice
Mit dieser Instanz wird ein Shelly BLU-Gerät (z. B. Button, Door/Window, H&T, Motion) angelegt, das an einem **oder mehreren** Gateways angelernt ist (BLU Gateway, Shelly mit Bluetooth-Gateway). Das Gerät wird über seine MAC-Adresse erkannt, es genügt eine Instanz je BLU-Gerät.
BLU TRVs (Thermostate) sind davon ausgenommen, sie laufen an genau einem Gateway und werden über die Komponenten-Instanz angelegt.

Idealerweise wird die Instanz über den Konfigurator angelegt (Gruppe "BLU-Geräte"), dann wird die gesamte Konfiguration korrekt ausgefüllt.

## Inhaltverzeichnis
- [ShellyBLUDevice](#shellybludevice)
  - [Inhaltverzeichnis](#inhaltverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Variablen](#2-variablen)
  - [3. Funktionen](#3-funktionen)
  - [4. Spenden](#4-spenden)
  - [5. Lizenz](#5-lizenz)

## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
Haupt-Gateway (MQTT Topic) | Das Topic des Gateways, von dem die Nummerierung der Variablen stammt. Das Gerät muss an diesem Gateway angelernt sein.
MAC-Adresse des BLU-Geräts | MAC-Adresse des BLU-Geräts, z. B. `f8:44:77:43:0d:86` (Groß-/Kleinschreibung egal).
Weitere Gateways | Alle weiteren Gateways (MQTT Topics), an denen dasselbe Gerät angelernt ist. Alle Gateways müssen mit demselben MQTT Server/Client verbunden sein.
Stärkstes Gateway: maximales Alter | Ein Gateway zählt für "Stärkstes Gateway" nur, wenn es das Gerät in dieser Zeit (Sekunden) gehört hat.
Debug: Fehlende Idents | Zusätzliche Debug-Ausgaben, falls Variablen fehlen.
Variablen | Auswahl der Variablen, ebenfalls die Funktion "Zeroing" (Variable wird zurückgesetzt, wenn alle Gateways offline sind).

## 2. Variablen
Die Variablen des Geräts entsprechen denen einer `bthomedevice`-Komponenten-Instanz (Sensoren, Batterie, Empfang, Kopplung, Firmware und der letzte Tastendruck).

Beim Zusammenführen der Gateways gilt:
* Messwerte: der Wert mit dem neuesten Zeitstempel (`last_updated_ts`) aller Gateways.
* Tastendruck: von jedem Gateway; derselbe Druck, den zwei Gateways kurz nacheinander melden, wird nur einmal gezählt.
* Erreichbar: online, solange mindestens ein Gateway online ist.
* RSSI des Geräts: Signal des stärksten Gateways.

Zusätzlich je Gateway:
* `RSSI (<Gateway>)`: Signalstärke, mit der dieses Gateway das Gerät empfängt.
* `Letzte Aktualisierung (<Gateway>)`: wann dieses Gateway das Gerät zuletzt gehört hat.
* `Stärkstes Gateway`: das Gateway mit dem stärksten Signal (nur Gateways, die das Gerät innerhalb des maximalen Alters gehört haben) - z. B. für die Raumerkennung.

## 3. Funktionen

`void SHY_requestComponentsStatus(integer $InstanzID);`
Ruft die Komponenten aller Gateways ab und legt die Variablen an, wird automatisch beim Speichern der Instanz aufgerufen.

## 4. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 5. Lizenz

