# ShellyXT1Device
Mit dieser Instanz können alle "Powered by Shelly" Geräte eingebunden werden.

## Inhaltsverzeichnis
- [ShellyXT1Device](#shellyxt1device)
  - [Inhaltsverzeichnis](#inhaltsverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Variablen](#2-variablen)
    - [2.1 ST802](#21-st802)
    - [2.2 ST1820](#22-st1820)
    - [2.3 Smart Water Valve](#23-smart-water-valve)
    - [2.4 Neo Smart Water Valve](#24-neo-smart-water-valve)
    - [2.5 EV Charger](#25-ev-charger)
  - [3. Funktionen](#3-funktionen)
  - [4. Spenden](#4-spenden)
  - [5. Lizenz](#5-lizenz)

## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
MQTT Topic | Hier wird das Topic des Geräts hinterlegt.
XMOD Service Type | Hier wird das Gerät ausgewählt, für welches diese Instanz dienen soll. 

## 2. Variablen

Welche Variablen die Instanz anlegt, hängt vom gewählten "XMOD Service Type" ab. Der Ident einer Variable ist der Name des Felds (z. B. `enable`). Dazu kommt immer die Variable "Erreichbar" (`Reachable`, Ja/Nein).

### 2.1 ST802

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Status | `enable` | Ja/Nein | Ein/Aus, steuerbar
Aktuelle Temperatur | `current_temperature` | Zahl (°C) | Gemessene Temperatur
Zieltemperatur | `target_temperature` | Zahl (°C) | Steuerbar
Lüftergeschwindigkeit | `fan_speed` | Text | Steuerbar
Modus | `working_mode` | Text | Steuerbar
Frostschutz | `anti_freeze` | Ja/Nein | Steuerbar
Luftfeuchtigkeit | `current_humidity` | Zahl (%) | Gemessene Luftfeuchtigkeit
Ziel Luftfeuchtigkeit | `target_humidity` | Zahl (%) | Steuerbar

### 2.2 ST1820

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Status | `enable` | Ja/Nein | Ein/Aus, steuerbar
Aktuelle Temperatur | `current_temperature` | Zahl (°C) | Gemessene Temperatur
Zieltemperatur | `target_temperature` | Zahl (°C) | Steuerbar
Frostschutz | `anti_freeze` | Ja/Nein | Steuerbar
Luftfeuchtigkeit | `current_humidity` | Zahl (%) | Gemessene Luftfeuchtigkeit
Kindersicherung | `child_lock` | Ja/Nein | Steuerbar

### 2.3 Smart Water Valve

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Position | `position` | Ganzzahl (%) | Öffnung des Ventils, steuerbar
Stromversorgung | `has_Power` | Ja/Nein | Ist das Ventil an der Stromversorgung
Aktuelle Temperatur | `temperature` | Zahl (°C) | Gemessene Temperatur

### 2.4 Neo Smart Water Valve

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Status | `state` | Ja/Nein | Ventil offen/geschlossen, steuerbar
Durchflussrate | `flow_rate` | Zahl (m3/min) | Aktueller Durchfluss
Wasserdruck | `water_pressure` | Zahl (kPa) | Aktueller Wasserdruck
Wassertemperatur | `water_temperature` | Zahl (°C) | Gemessene Wassertemperatur
Wasserverbrauch | `water_consumption` | Zahl (m3) | Verbrauch

### 2.5 EV Charger

Variable | Ident | Typ | Beschreibung
------------ | ------------ | ------------ | ----------------
Ladevorgang starten | `start_charging` | Ja/Nein | Startet bzw. beendet das Laden, steuerbar
Ladestatus | `work_state` | Text | Zustand der Wallbox
Stromlimit | `current_limit` | Zahl (A) | Steuerbar
Energie (Sitzung) | `energy_charge` | Zahl (kWh) | Geladene Energie der Sitzung
Dauer (Sitzung) | `time_charge` | Ganzzahl (min) | Dauer der Sitzung
Gesamtverbrauch | `total_consumption` | Zahl (kWh) | Gesamter Verbrauch
Gesamtstrom | `total_current` | Zahl (A) | Summe aller Phasen
Gesamtleistung | `total_power` | Zahl (W) | Summe aller Phasen
Gesamtwirkenergie | `total_active_energy` | Zahl (kWh) | Gesamte Wirkenergie
Phase A/B/C Spannung | `phase_a_voltage`, `phase_b_voltage`, `phase_c_voltage` | Zahl (V) | Spannung je Phase
Phase A/B/C Stromstärke | `phase_a_current`, `phase_b_current`, `phase_c_current` | Zahl (A) | Strom je Phase
Phase A/B/C Leistung | `phase_a_power`, `phase_b_power`, `phase_c_power` | Zahl (W) | Leistung je Phase

## 3. Funktionen

`RequestAction($VariablenID, $Value);`
Mit dieser Funktion können alle Aktionen einer Variable ausgelöst werden.

**Beispiel:**

Variable ID Status 1 = 12345

```php
RequestAction(12345, true);  //Status 1 Einschalten;
RequestAction(12345, false); //Status 1 Ausschalten;
```

## 4. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:

[![PayPal](https://img.shields.io/badge/PayPal-Spenden-00457C?logo=paypal&logoColor=white&style=for-the-badge)](https://www.paypal.com/donate?hosted_button_id=EK4JRP87XLSHW) [![Amazon Wunschzettel](https://img.shields.io/badge/Amazon-Wunschzettel-FF9900?logo=amazon&logoColor=white&style=for-the-badge)](https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share)

## 5. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)