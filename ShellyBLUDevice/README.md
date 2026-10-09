# ShellyBLUDevice
Mit dieser Instanz wird ein Shelly BLU-Gerät (z. B. Button, Door/Window, H&T, Motion) angelegt, das an einem **oder mehreren** Gateways angelernt ist (BLU Gateway, Shelly mit Bluetooth-Gateway). Das Gerät wird über seine MAC-Adresse erkannt, es genügt eine Instanz je BLU-Gerät.
BLU TRVs (Thermostate) sind davon ausgenommen, sie laufen an genau einem Gateway und werden über die Komponenten-Instanz angelegt.

Idealerweise wird die Instanz über den Konfigurator angelegt (Gruppe "BLU-Geräte"), dann wird die gesamte Konfiguration korrekt ausgefüllt.

## Inhaltsverzeichnis
- [ShellyBLUDevice](#shellybludevice)
  - [Inhaltsverzeichnis](#inhaltsverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Variablen](#2-variablen)
    - [2.1 Variablen des Geräts](#21-variablen-des-geräts)
    - [2.2 Tasten und Rad](#22-tasten-und-rad)
    - [2.3 Sensoren (Messwerte)](#23-sensoren-messwerte)
    - [2.4 Variablen der Instanz (mehrere Gateways)](#24-variablen-der-instanz-mehrere-gateways)
  - [3. Modelle](#3-modelle)
  - [4. Funktionen](#4-funktionen)
  - [5. Spenden](#5-spenden)
  - [6. Lizenz](#6-lizenz)

## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
Haupt-Gateway (MQTT Topic) | Das Topic des Gateways, von dem die Nummerierung der Variablen stammt. Das Gerät muss an diesem Gateway angelernt sein.
MAC-Adresse des BLU-Geräts | MAC-Adresse des BLU-Geräts, z. B. `aa:bb:cc:dd:ee:ff` (Groß-/Kleinschreibung egal).
Weitere Gateways | Alle weiteren Gateways (MQTT Topics), an denen dasselbe Gerät angelernt ist. Alle Gateways müssen mit demselben MQTT Server/Client verbunden sein.
Stärkstes Gateway: maximales Alter | Ein Gateway zählt für "Stärkstes Gateway" nur, wenn es das Gerät in dieser Zeit (Sekunden) gehört hat.
Debug: Fehlende Idents | Zusätzliche Debug-Ausgaben, falls Variablen fehlen.
Variablen | Auswahl der Variablen, ebenfalls die Funktion "Zeroing" (Variable wird zurückgesetzt, wenn alle Gateways offline sind).

## 2. Variablen
Alle Variablen einer Instanz. Die Namen der Variablen des Geräts beginnen mit dem Namen des Geräts, den man am Gateway vergeben hat, sonst mit der MAC-Adresse (z. B. `aa:bb:cc:dd:ee:ff - Battery`). In der Liste "Variablen" der Instanz kann man einzelne abwählen.

### 2.1 Variablen des Geräts
Der Ident beginnt mit `bthomedevice_<N>_`, `<N>` ist die Nummer des Geräts am Haupt-Gateway.

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
RSSI | `rssi` | Ganzzahl (dBm) | Signalstärke, mit der das Gerät empfangen wird. Bei mehreren Gateways der Wert des stärksten Gateways.
Battery | `battery` | Ganzzahl (%) | Batteriestand.
Letzte Aktualisierung | `last_updated_ts` | Datum/Uhrzeit | Wann das letzte Paket des Geräts empfangen wurde (der neueste Zeitpunkt aller Gateways).
Gekoppelt | `paired` | Ja/Nein | Ob das Gerät am Gateway angelernt ist.
Schlüssel hinterlegt | `key` | Ja/Nein | Ob am Gateway ein Verschlüsselungsschlüssel für das Gerät hinterlegt ist (nur bei verschlüsselten Geräten nötig).
Firmware-Version | `fw_ver` | Text | Firmware des BLU-Geräts.
Fehler | `errors` | Text | Fehler des Geräts als Text (z. B. "Entschlüsselung fehlgeschlagen" bei einem fehlenden oder falschen Schlüssel), leer wenn keiner vorliegt.
Störung | `fault` | Ja/Nein | `true`, sobald ein Fehler vorliegt, praktisch für Ereignisse und Benachrichtigungen.

### 2.2 Tasten und Rad
Sie kommen als Ereignis des Geräts (beim Druck bzw. Drehen), nicht beim Auslesen. Welche es gibt, steht in der Modellliste (siehe 3.).

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Taste | `button` | Text | Letzter Tastendruck: Einfach gedrückt, Doppelt gedrückt, Dreifach gedrückt, Lang gedrückt oder Gehalten. Bei Geräten mit mehreren Tasten heißt sie `Taste 1`.
Taste 2 bis Taste 4 | `button2` bis `button4` | Text | Die weiteren Tasten (BLU RC Button 4, Wall Switch 4). Die Tastennummer liefert das Gerät im Ereignis.
Rad | `dial` | Text | Letzte Drehrichtung des Drehrads (BLU Remote Control ZB): Hoch gedreht oder Runter gedreht.
Rad Schritte | `dialsteps` | Ganzzahl | Schritte der letzten Drehung, runter negativ.

Geräte mit einer Taste als Hauptfunktion (z. B. Button, Wall Switch) haben die Variable von Anfang an. Bei den übrigen (z. B. Tür-/Fenstersensor, H&T, Motion) entsteht sie beim ersten Tastendruck.

### 2.3 Sensoren (Messwerte)
Je Sensor des Geräts gibt es eine Variable mit dem Messwert und eine mit dem Zeitpunkt der letzten Meldung ("<Sensor> - Letzte Aktualisierung"). Der Ident ist `bthomesensor_<Objekt * 100 + Index>_value` bzw. `..._last_updated_ts` (z. B. `bthomesensor_4500_value` für das Fenster), an allen Gateways gleich. Name, Typ und Einheit kommen vom Gerät bzw. aus der Liste unten. Die Batterie hat keine eigene Sensor-Variable, sie steht schon bei den Variablen des Geräts.

Die Sensoren, die ein Modell sendet, legt die Instanz **sofort** an (Liste siehe 3.), sie füllen sich mit dem ersten Paket des Geräts. Bis dahin zeigen sie den Standardwert (0, "Keine Bewegung") und bei der Zeit "-". Sendet ein Gerät einen Sensor nicht (mehr), bleibt die Variable bei dem letzten bekannten Wert.

Sensor | BTHome-Objekt | Typ | Werte | Geräte (Beispiele)
------------ | ------------ | ------------ | ------------ | ------------
Fenster | 45 | Ja/Nein | Offen / Geschlossen | Tür-/Fenstersensor
Bewegung | 33 | Ja/Nein | Bewegung / Keine Bewegung | Motion, Motion ZB
Temperatur | 69 | Zahl | °C | HT, H&T Display, Wetterstation
Luftfeuchtigkeit | 46 | Zahl | % | HT, H&T Display, Wetterstation
Beleuchtungsstärke | 5 | Zahl | lx | Tür-/Fenstersensor, Motion, Wetterstation
Drehung | 63 | Zahl | ° | Tür-/Fenstersensor: Öffnungswinkel. Remote Control ZB: drei Werte "Drehung 1" bis "Drehung 3" (Bedeutung vom Hersteller nicht beschrieben, vermutlich Lage des Geräts, ändern sich beim Drehen des Rads nicht)
Helligkeitsstufe | 100 | Aufzählung | Dunkel / Dämmerung / Hell | Motion ZB, Tür-/Fenstersensor ZB, H&T Display
Licht | 30 | Ja/Nein | Hell / Dunkel | H&T Display
Batterie schwach | 21 | Ja/Nein | Schwach / OK | H&T Display
Abstand | 64 | Zahl | mm | Distance
Kanal | 96 | Zahl | aktiver Kanal | Remote Control ZB
Luftdruck, Taupunkt, Spannung, Niederschlag, Feuchtigkeit (Regen), Wind (Geschwindigkeit und Böen), UV-Index, Richtung | 4, 8, 12, 95, 32, 68, 70, 94 | Zahl bzw. Ja/Nein | hPa, °C, V, mm, Nass/Trocken, m/s, -, ° | Wetterstation (nach der Doku, nicht an einem echten Gerät geprüft)

### 2.4 Variablen der Instanz (mehrere Gateways)
Beim Zusammenführen der Gateways gilt:
* Messwerte: der Wert mit dem neuesten Zeitstempel (`last_updated_ts`) aller Gateways.
* Tastendruck: von jedem Gateway; derselbe Druck, den zwei Gateways kurz nacheinander melden, wird nur einmal gezählt.

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Erreichbar | `Reachable` | Online/Offline | Online, solange mindestens ein Gateway online ist.
RSSI (<Gateway>) | `GwRssi_...` | Ganzzahl (dBm) | Signalstärke, mit der dieses Gateway das Gerät empfängt (eine Variable je Gateway).
Letzte Aktualisierung (<Gateway>) | `GwSeen_...` | Datum/Uhrzeit | Wann dieses Gateway das Gerät zuletzt gehört hat (eine Variable je Gateway).
Stärkstes Gateway | `NearestGateway` | Text | Das Gateway mit dem stärksten Signal (nur Gateways, die das Gerät innerhalb des maximalen Alters gehört haben), z. B. für die Raumerkennung.

## 3. Modelle
Welches BLU-Gerät es ist, steht in der Modell-ID (attrs.model_id der Komponente am Gateway). Die Liste der bekannten Modelle steht in libs/BTHomeModels.php (Name, Taste, Thermostat und die BTHome-Objekte, die das Modell sendet). Für diese Modelle gibt es die Sensor-Variablen (Fenster, Luftfeuchtigkeit, Temperatur, ...) sofort beim Anlegen der Instanz, auch wenn das Gateway sie noch nicht gemeldet hat; die Variablen füllen sich beim ersten Paket des Geräts. Unbekannte Modelle funktionieren weiter, aber nur mit den Sensoren, die das Gateway meldet. Ein neues Modell trägt man in der Liste ein.

## 4. Funktionen

`void SHY_requestComponentsStatus(integer $InstanzID);`
Ruft die Komponenten aller Gateways ab und legt die Variablen an, wird automatisch beim Speichern der Instanz aufgerufen.

## 5. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 6. Lizenz

