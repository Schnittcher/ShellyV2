# ShellyConfigurator
Dieses Modul findet alles Shelly Gen2+ Geräte per MQTT und kann diese als komplettes Gerät oder einzelne Komponenten anlegen.

## Inhaltsverzeichnis

- [ShellyConfigurator](#shellyconfigurator)
  - [Inhaltsverzeichnis](#inhaltsverzeichnis)
  - [1. Funktionsumfang](#1-funktionsumfang)
  - [2. Spenden](#2-spenden)
  - [3. Lizenz](#3-lizenz)

## 1. Funktionsumfang

Findet alle Shellies per MQTT im Netzwerk, welches auf das Topic /announce reagieren.
Die Shellies können als komplettes Gerät angelegt werden, oder aber die einzelnen Komponenten des Shellies.

**Shelly BLU-Geräte** stehen in einer eigenen Gruppe "BLU-Geräte", mit einer Zeile je MAC-Adresse. Die Zeile zeigt die MAC-Adresse (und die Gateways, an denen das Gerät angelernt ist), der Gerätetyp steht in der Spalte "Device Type". Beim Anlegen entsteht eine Instanz ShellyBLUDevice mit allen diesen Gateways; der Instanzname bekommt den Gerätetyp vor die MAC-Adresse (z. B. "Shelly BLU Button Tough 1 ZB - f8:44:77:43:0d:86"). BLU TRVs (Thermostate) hängen an genau einem Gateway und stehen deshalb bei diesem Gateway als einzelne Komponenten.
 
## 2. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 3. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)