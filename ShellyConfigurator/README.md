# ShellyConfigurator
Dieses Modul findet alle Shelly Gen2+ Geräte per MQTT und kann diese als komplettes Gerät oder einzelne Komponenten anlegen.

## Inhaltsverzeichnis
- [ShellyConfigurator](#shellyconfigurator)
  - [Inhaltsverzeichnis](#inhaltsverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Hinweise](#2-hinweise)
  - [3. Spenden](#3-spenden)
  - [4. Lizenz](#4-lizenz)

## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
Shelly Konfigurator (Tabelle) | Die gefundenen Shellies mit MQTT Topic, Instanzname, Gerätetyp, App und Firmware. Hier werden die Instanzen angelegt, als komplettes Gerät oder als einzelne Komponenten. Die Spalten "Name" und "IP-Adresse" lassen sich einblenden.
MQTT-Einstellungen setzen (Liste) | Experimentell: Die im Netzwerk per mDNS gefundenen Shellies. Hier wird das Gerät ausgewählt, dessen MQTT-Einstellungen geändert werden sollen.
Broker | Adresse des MQTT Servers, die in das Gerät geschrieben wird.
Port | Port des MQTT Servers.
MQTT Benutzername | Benutzername für den MQTT Server.
MQTT Passwort | Passwort für den MQTT Server.
Speichern | Schreibt die MQTT-Einstellungen in das ausgewählte Gerät und aktiviert MQTT. Das Gerät startet dabei bei Bedarf neu.

## 2. Hinweise

Der Konfigurator findet alle Shellies im Netzwerk, die per MQTT auf das Topic `/announce` reagieren. Sie können als komplettes Gerät oder als einzelne Komponenten angelegt werden.

**Batteriebetriebene Geräte** schlafen die meiste Zeit und werden deshalb oft nicht gefunden. Sie müssen dann von Hand angelegt werden: eine Instanz "ShellyDevice" hinzufügen und das MQTT-Topic des Geräts sowie das Modell eintragen. Dieser Hinweis steht auch im Konfigurator unter der Tabelle.

**Shelly BLU-Geräte** stehen in einer eigenen Gruppe "BLU-Geräte", mit einer Zeile je MAC-Adresse. Die Zeile zeigt die MAC-Adresse (und die Gateways, an denen das Gerät angelernt ist), der Gerätetyp steht in der Spalte "Device Type". Beim Anlegen entsteht eine Instanz ShellyBLUDevice mit allen diesen Gateways; der Instanzname bekommt den Gerätetyp vor die MAC-Adresse (z. B. "Shelly BLU Button Tough 1 ZB - aa:bb:cc:dd:ee:ff"). BLU TRVs (Thermostate) hängen an genau einem Gateway und stehen deshalb bei diesem Gateway als einzelne Komponenten.

## 3. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:

[![PayPal](https://img.shields.io/badge/PayPal-Spenden-00457C?logo=paypal&logoColor=white&style=for-the-badge)](https://www.paypal.com/donate?hosted_button_id=EK4JRP87XLSHW) [![Amazon Wunschzettel](https://img.shields.io/badge/Amazon-Wunschzettel-FF9900?logo=amazon&logoColor=white&style=for-the-badge)](https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share)

## 4. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)