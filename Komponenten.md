# Komponenten und Variablen

Welche Variablen eine Instanz (ShellyDevice oder ShellyComponent) anlegt, hängt von den Komponenten des Geräts ab. Das Modul liest sie vom Gerät selbst; hier steht, was es je Komponente gibt. Diese Übersicht wird aus den Definitionen im Modul (`libs/components.php`) erzeugt.

**Allgemein:**
* Jede Instanz hat die Variable **Erreichbar** (online/offline).
* Der **Ident** einer Variable ist `<Komponente>_<Kanal>_<Feld>`, z. B. `switch_0_output` (Kanal 0 des Switch, Feld `output`). Felder mit Unterebenen werden mit `_` verbunden (`switch_0_aenergy_total`). Bei Komponenten ohne Kanal fehlt die Zahl (`wifi_ssid`).
* Der **Name** ist der Name der Komponente, bei Kanal größer als 0 mit der Kanalnummer (z. B. "Status 1"). Hat man dem Kanal am Gerät einen Namen gegeben, steht er davor (z. B. "Waschmaschine - Wirkleistung").
* In der Liste "Variablen" der Instanz kann man einzelne Variablen abwählen.
* Fast alle Komponenten, die Fehler melden, haben zusätzlich die Variablen **Fehler** (Fehlercodes als Text) und **Störung** (Ja/Nein); sie stehen in den Tabellen unten.
* **Aktion** bedeutet, dass die Variable bedienbar ist (Schalten, Einstellen). Bei manchen Komponenten gibt es dafür eine zweite Variable ("Zusatzvariable"), z. B. die Helligkeit als Prozentwert und dazu eine Aktion.

## Inhaltsverzeichnis
* [Ereignisse](#ereignisse)
* [Cloud](#cloud)
* [Ethernet](#ethernet)
* [WLAN](#wlan)
* [Eingang](#eingang)
* [Spannungsmesser](#spannungsmesser)
* [Flutsensor](#flutsensor)
* [Energiedaten (EM)](#energiedaten-em)
* [Energiedaten (EM1)](#energiedaten-em1)
* [Energiemessung 3 Phasen (EM)](#energiemessung-3-phasen-em)
* [Temperatur](#temperatur)
* [Luftfeuchtigkeit](#luftfeuchtigkeit)
* [Licht / Dimmer (Light)](#licht--dimmer-light)
* [RGBW](#rgbw)
* [RGB](#rgb)
* [CCT](#cct)
* [RGBCCT](#rgbcct)
* [BLU TRV (Thermostat)](#blu-trv-thermostat)
* [Schalter (Switch)](#schalter-switch)
* [Leitungsschutzschalter (CB)](#leitungsschutzschalter-cb)
* [Shelly Cury](#shelly-cury)
* [Shelly Camera](#shelly-camera)
* [Kamera-Bewegungszone](#kamera-bewegungszone)
* [DALI](#dali)
* [BLU-Gerät (BTHome)](#blu-gerät-bthome)
* [BLU-Sensor (BTHome)](#blu-sensor-bthome)
* [Energiemessung (PM1)](#energiemessung-pm1)
* [Energiemessung 1 Phase (EM1)](#energiemessung-1-phase-em1)
* [Rollladen / Abdeckung (Cover)](#rollladen--abdeckung-cover)
* [Rauchmelder](#rauchmelder)
* [Stromversorgung](#stromversorgung)
* [Beleuchtungsstärke](#beleuchtungsstärke)
* [Präsenzzone](#präsenzzone)
* [Virtuelle Komponente: Boolean](#virtuelle-komponente-boolean)
* [Virtuelle Komponente: Number](#virtuelle-komponente-number)
* [Virtuelle Komponente: Enum](#virtuelle-komponente-enum)
* [Virtuelle Komponente: Text](#virtuelle-komponente-text)
* [Objekt-Komponente](#objekt-komponente)

## Ereignisse
Jedes Gerät: Name der Komponente und des Ereignisses, das zuletzt gemeldet wurde.

Komponente `events`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Ereigniskomponente | `events_0_component` | Text |  | 
Ereignis | `events_0_event` | Text |  | 

## Cloud
Verbindung des Geräts zur Shelly-Cloud.

Komponente `cloud`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Cloud Status | `cloud_connected` | Ja/Nein |  | 

## Ethernet
Netzwerkanschluss per Kabel.

Komponente `eth`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Ethernet IP-Adresse | `eth_ip` | Text |  | 

## WLAN
Verbindung des Geräts zum WLAN.

Komponente `wifi`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Wifi IP-Adresse | `wifi_sta_ip` | Text |  | 
Wifi SSID | `wifi_ssid` | Text |  | 

## Eingang
Digitaler Eingang (Taster/Schalter am Gerät).

Komponente `input`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Eingangsstatus | `input_<Kanal>_state` | Ja/Nein |  | 
Eingang (Porzent) | `input_<Kanal>_percent` | Ganzzahl | % | 
Total Counts | `input_<Kanal>_counts_total` | Ganzzahl |  | 
Frequency | `input_<Kanal>_freq` | Zahl | Hz | 
Fehler | `input_<Kanal>_errors` | Text |  | 
Störung | `input_<Kanal>_fault` | Ja/Nein |  | 

## Spannungsmesser
Messung einer externen Spannung (z. B. Add-on).

Komponente `voltmeter`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Spannung | `voltmeter_<Kanal>_voltage` | Zahl | V | 
Xvoltage | `voltmeter_<Kanal>_xvoltage` | Zahl | V | 
Fehler | `voltmeter_<Kanal>_errors` | Text |  | 
Störung | `voltmeter_<Kanal>_fault` | Ja/Nein |  | 

## Flutsensor
Wassersensor (Alarm, Stummschaltung, Fehler).

Komponente `flood`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Alarm | `flood_<Kanal>_alarm` | Ja/Nein |  | 
Stumm | `flood_<Kanal>_mute` | Ja/Nein |  | 
Fehler | `flood_<Kanal>_errors` | Text |  | 
Störung | `flood_<Kanal>_fault` | Ja/Nein |  | 

## Energiedaten (EM)
Energiezähler der 3-Phasen-Messung (Wirk- und Rückspeiseenergie, je Phase und gesamt).

Komponente `emdata`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Phase A Gesamtwirkenergie | `emdata_<Kanal>_a_total_act_energy` | Zahl | kWh | 
Phase A Gesamtwirkenergie Einspeisung | `emdata_<Kanal>_a_total_act_ret_energy` | Zahl | kWh | 
Phase B Gesamtwirkenergie | `emdata_<Kanal>_b_total_act_energy` | Zahl | kWh | 
Phase B Gesamtwirkenergie Einspeisung | `emdata_<Kanal>_b_total_act_ret_energy` | Zahl | kWh | 
Phase C Gesamtwirkenergie | `emdata_<Kanal>_c_total_act_energy` | Zahl | kWh | 
Phase C Gesamtwirkenergie Einspeisung | `emdata_<Kanal>_c_total_act_ret_energy` | Zahl | kWh | 
Gesamtwirkenergie | `emdata_<Kanal>_total_act` | Zahl | kWh | 
Gesamtwirkenergie Einspeisung | `emdata_<Kanal>_total_act_ret` | Zahl | kWh | 
Fehler | `emdata_<Kanal>_errors` | Text |  | 
Störung | `emdata_<Kanal>_fault` | Ja/Nein |  | 

## Energiedaten (EM1)
Energiezähler der einphasigen Messung.

Komponente `em1data`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Gesamtwirkenergie | `em1data_<Kanal>_total_act_energy` | Zahl | kWh | 
Gesamtwirkenergie Einspeisung | `em1data_<Kanal>_total_act_ret_energy` | Zahl | kWh | 
Fehler | `em1data_<Kanal>_errors` | Text |  | 
Störung | `em1data_<Kanal>_fault` | Ja/Nein |  | 

## Energiemessung 3 Phasen (EM)
Momentanwerte einer 3-Phasen-Messung (z. B. Shelly Pro 3EM): Spannung, Strom, Leistung, Frequenz, je Phase und gesamt.

Komponente `em`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Phase A Stromstärke | `em_<Kanal>_a_current` | Zahl | A | 
Phase A Spannung | `em_<Kanal>_a_voltage` | Zahl | V | 
Phase A Wirkleistung | `em_<Kanal>_a_act_power` | Zahl | W | 
Phase A Scheinleistung | `em_<Kanal>_a_aprt_power` | Zahl | VA | 
Phase A Leistungsfaktor | `em_<Kanal>_a_pf` | Zahl |  | 
Phase A Netzfrequenz | `em_<Kanal>_a_freq` | Zahl |  | 
Phase B Stromstärke | `em_<Kanal>_b_current` | Zahl | A | 
Phase B Spannung | `em_<Kanal>_b_voltage` | Zahl | V | 
Phase B Wirkleistung | `em_<Kanal>_b_act_power` | Zahl | W | 
Phase B Scheinleistung | `em_<Kanal>_b_aprt_power` | Zahl | VA | 
Phase B Leistungsfaktor | `em_<Kanal>_b_pf` | Zahl |  | 
Phase B Netzfrequenz | `em_<Kanal>_b_freq` | Zahl |  | 
Phase C Stromstärke | `em_<Kanal>_c_current` | Zahl | A | 
Phase C Spannung | `em_<Kanal>_c_voltage` | Zahl | V | 
Phase C Wirkleistung | `em_<Kanal>_c_act_power` | Zahl | W | 
Phase C Scheinleistung | `em_<Kanal>_c_aprt_power` | Zahl | VA | 
Phase C Leistungsfaktor | `em_<Kanal>_c_pf` | Zahl |  | 
Phase C Netzfrequenz | `em_<Kanal>_c_freq` | Zahl |  | 
Neutral Stromstärke | `em_<Kanal>_n_current` | Zahl | A | 
Stromstärke auf allen Phasen | `em_<Kanal>_total_current` | Zahl | A | 
Wirkleistung auf allen Phasen | `em_<Kanal>_total_act_power` | Zahl | W | 
Scheinleistung auf allen Phasen | `em_<Kanal>_total_aprt_power` | Zahl | VA | 
Fehler | `em_<Kanal>_errors` | Text |  | 
Störung | `em_<Kanal>_fault` | Ja/Nein |  | 

## Temperatur
Temperatursensor (Add-on, Pill, Geräte-Temperatur).

Komponente `temperature`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Temperatur | `temperature_<Kanal>_tC` | Zahl | °C | 
Fehler | `temperature_<Kanal>_errors` | Text |  | 
Störung | `temperature_<Kanal>_fault` | Ja/Nein |  | 

## Luftfeuchtigkeit
Luftfeuchtigkeitssensor.

Komponente `humidity`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Luftfeuchte | `humidity_<Kanal>_rh` | Zahl | % | 
Fehler | `humidity_<Kanal>_errors` | Text |  | 
Störung | `humidity_<Kanal>_fault` | Ja/Nein |  | 

## Licht / Dimmer (Light)
Ein/Aus und Helligkeit, dazu Messwerte.

Komponente `light`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Status | `light_<Kanal>_output` | Ja/Nein |  | ja
Helligkeit | `light_<Kanal>_brightness` | Ganzzahl | % | ja (Zusatzvariable "Helligkeit Aktion")
Wirkleistung | `light_<Kanal>_apower` | Zahl | W | 
Spannung | `light_<Kanal>_voltage` | Zahl | V | 
Stromstärke | `light_<Kanal>_current` | Zahl | A | 
Gesamtverbrauch | `light_<Kanal>_aenergy_total` | Zahl | kWh | 
Fehler | `light_<Kanal>_errors` | Text |  | 
Störung | `light_<Kanal>_fault` | Ja/Nein |  | 

## RGBW
RGB-Leuchte mit zusätzlichem Weißkanal.

Komponente `rgbw`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
RGBW Status | `rgbw_<Kanal>_output` | Ja/Nein |  | ja
RGB | `rgbw_<Kanal>_rgb` | Text |  | ja
RGBW Helligkeit | `rgbw_<Kanal>_brightness` | Ganzzahl | % | ja (Zusatzvariable "RGBW Helligkeit Aktion")
RGBW White | `rgbw_<Kanal>_white` | Ganzzahl | % | ja
RGBW Wirkleistung | `rgbw_<Kanal>_apower` | Zahl | W | 
RGBW Spannung | `rgbw_<Kanal>_voltage` | Zahl | V | 
RGBW Stromstärke | `rgbw_<Kanal>_current` | Zahl | A | 
RGBW Gesamtverbrauch | `rgbw_<Kanal>_aenergy_total` | Zahl | kWh | 
Temperatur | `rgbw_<Kanal>_temperature_tC` | Zahl | °C | 
Fehler | `rgbw_<Kanal>_errors` | Text |  | 
Störung | `rgbw_<Kanal>_fault` | Ja/Nein |  | 

## RGB
RGB-Leuchte mit Farbe und Helligkeit.

Komponente `rgb`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
RGB Status | `rgb_<Kanal>_output` | Ja/Nein |  | ja
RGB | `rgb_<Kanal>_rgb` | Text |  | ja
RGB Helligkeit | `rgb_<Kanal>_brightness` | Ganzzahl | % | ja (Zusatzvariable "RGB Helligkeit Aktion")
RGB Temperatur | `rgb_<Kanal>_temperature_tC` | Zahl | °C | 
RGB Wirkleistung | `rgb_<Kanal>_apower` | Zahl | W | 
RGB Spannung | `rgb_<Kanal>_voltage` | Zahl | V | 
RGB Stromstärke | `rgb_<Kanal>_current` | Zahl | A | 
RGB Gesamtverbrauch | `rgb_<Kanal>_aenergy_total` | Zahl | kWh | 
Fehler | `rgb_<Kanal>_errors` | Text |  | 
Störung | `rgb_<Kanal>_fault` | Ja/Nein |  | 

## CCT
Leuchte mit einstellbarer Farbtemperatur.

Komponente `cct`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
CCT Status | `cct_<Kanal>_output` | Ja/Nein |  | ja
CCT Farbtemperatur | `cct_<Kanal>_ct` | Ganzzahl |  | ja
CCT Helligkeit | `cct_<Kanal>_brightness` | Ganzzahl | % | ja (Zusatzvariable "CCT Helligkeit Aktion")
CCT Wirkleistung | `cct_<Kanal>_apower` | Zahl | W | 
CCT Spannung | `cct_<Kanal>_voltage` | Zahl | V | 
CCT Stromstärke | `cct_<Kanal>_current` | Zahl | A | 
CCT Temperatur | `cct_<Kanal>_temperature_tC` | Zahl | °C | 
Fehler | `cct_<Kanal>_errors` | Text |  | 
Störung | `cct_<Kanal>_fault` | Ja/Nein |  | 

## RGBCCT
Leuchte mit Farbe, Farbtemperatur und Helligkeit.

Komponente `rgbcct`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
RGBCCT Status | `rgbcct_<Kanal>_output` | Ja/Nein |  | ja
RGBCCT Modus | `rgbcct_<Kanal>_mode` | Text |  | ja
RGB | `rgbcct_<Kanal>_rgb` | Text |  | ja
RGBCCT Farbtemperatur | `rgbcct_<Kanal>_ct` | Ganzzahl | K | ja
RGBCCT Helligkeit | `rgbcct_<Kanal>_brightness` | Ganzzahl | % | ja (Zusatzvariable "RGBCCT Helligkeit Aktion")
RGBCCT Wirkleistung | `rgbcct_<Kanal>_apower` | Zahl | W | 
RGBCCT Gesamtverbrauch | `rgbcct_<Kanal>_aenergy_total` | Zahl | kWh | 

## BLU TRV (Thermostat)
Thermostat am BLU Gateway: Zieltemperatur, Position, externe Temperatur, Empfang, Batterie.

Komponente `blutrv`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Externe Temperatur | `blutrv_<Kanal>_current_C` | Zahl | °C | ja
Zieltemperatur | `blutrv_<Kanal>_target_C` | Zahl | °C | ja
Position | `blutrv_<Kanal>_pos` | Ganzzahl | % | ja
RSSI | `blutrv_<Kanal>_rssi` | Ganzzahl |  | 
Battery | `blutrv_<Kanal>_battery` | Ganzzahl | % | 

## Schalter (Switch)
Relais mit Schaltzustand und (je nach Gerät) Leistungsmessung und Temperatur.

Komponente `switch`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Status | `switch_<Kanal>_output` | Ja/Nein |  | ja
Wirkleistung | `switch_<Kanal>_apower` | Zahl | W | 
Spannung | `switch_<Kanal>_voltage` | Zahl | V | 
Stromstärke | `switch_<Kanal>_current` | Zahl | A | 
Leistungsfaktor | `switch_<Kanal>_pf` | Zahl |  | 
Netzfrequenz | `switch_<Kanal>_freq` | Zahl | Hz | 
Gesamtverbrauch | `switch_<Kanal>_aenergy_total` | Zahl | kWh | 
Gesamteinspeisung | `switch_<Kanal>_ret_aenergy_total` | Zahl | kWh | 
Temperatur | `switch_<Kanal>_temperature_tC` | Zahl | °C | 
Fehler | `switch_<Kanal>_errors` | Text |  | 
Störung | `switch_<Kanal>_fault` | Ja/Nein |  | 

## Leitungsschutzschalter (CB)
Shelly Pro 3CB: Schutzschalter, Messwerte und Fehler.

Komponente `cb`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Schutzschalter Status | `cb_<Kanal>_output` | Ja/Nein |  | ja
Sicherheitsschalter | `cb_<Kanal>_safety` | Ja/Nein |  | 
Auslösungen gesamt | `cb_<Kanal>_total_cycles` | Ganzzahl |  | 
Temperatur | `cb_<Kanal>_temperature_tC` | Zahl | °C | 
Fehler | `cb_<Kanal>_errors` | Text |  | 
Störung | `cb_<Kanal>_fault` | Ja/Nein |  | 

## Shelly Cury
Duftspender mit Raummodus, Abwesenheitsmodus und zwei Fächern.

Komponente `cury`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Raummodus | `cury_<Kanal>_mode` | Text |  | ja
Abwesenheitsmodus | `cury_<Kanal>_away_mode` | Ja/Nein |  | ja
Linkes Fach Status | `cury_<Kanal>_slots_left_on` | Ja/Nein |  | ja (Zusatzvariable "Linkes Fach Boost")
Linkes Fach Intensität | `cury_<Kanal>_slots_left_intensity` | Ganzzahl | % | ja
Linkes Fläschchen Füllstand | `cury_<Kanal>_slots_left_vial_level` | Ganzzahl | % | 
Linkes Fläschchen Name | `cury_<Kanal>_slots_left_vial_name` | Text |  | 
Linkes Fläschchen Seriennummer | `cury_<Kanal>_slots_left_vial_serial` | Text |  | 
Rechtes Fach Status | `cury_<Kanal>_slots_right_on` | Ja/Nein |  | ja (Zusatzvariable "Rechtes Fach Boost")
Rechtes Fach Intensität | `cury_<Kanal>_slots_right_intensity` | Ganzzahl | % | ja
Rechtes Fläschchen Füllstand | `cury_<Kanal>_slots_right_vial_level` | Ganzzahl | % | 
Rechtes Fläschchen Name | `cury_<Kanal>_slots_right_vial_name` | Text |  | 
Rechtes Fläschchen Seriennummer | `cury_<Kanal>_slots_right_vial_serial` | Text |  | 
Fehler | `cury_<Kanal>_errors` | Text |  | 
Störung | `cury_<Kanal>_fault` | Ja/Nein |  | 

## Shelly Camera
Kamera: scharf, Privatsphäre, Bewegung, Aktionen und Geräteeinstellungen.

Komponente `camera`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Kamera scharf | `camera_<Kanal>_arm` | Ja/Nein |  | ja (Zusatzvariable "Kamera Aktion")
Kamera Privatsphäre | `camera_<Kanal>_privacy` | Ja/Nein |  | ja
Kamera Bewegung | `camera_<Kanal>_motion` | Ja/Nein |  | 
Kamera Streamer | `camera_<Kanal>_streamer` | Text |  | 
Kamera Streams | `camera_<Kanal>_streams` | Ganzzahl |  | 
Kamera Ton abspielen | `camera_<Kanal>_playsound` | Text |  | ja
Kamera RTSP | `camera_<Kanal>_rtsp_enable` | Ja/Nein |  | ja, Einstellung am Gerät
Kamera LED | `camera_<Kanal>_led_enable` | Ja/Nein |  | ja, Einstellung am Gerät
Kamera Töne | `camera_<Kanal>_sounds_enable` | Ja/Nein |  | ja, Einstellung am Gerät
Kamera Aufnahme bei Bewegung | `camera_<Kanal>_motionrecording_enable` | Ja/Nein |  | ja, Einstellung am Gerät
Kamera Bewegungsempfindlichkeit | `camera_<Kanal>_sensitivity_level` | Text |  | ja, Einstellung am Gerät
Kamera Nachtsicht | `camera_<Kanal>_nightvision_mode` | Text |  | ja, Einstellung am Gerät
Fehler | `camera_<Kanal>_errors` | Text |  | 
Störung | `camera_<Kanal>_fault` | Ja/Nein |  | 

## Kamera-Bewegungszone
Zone der Kamera mit Bewegungsstatus.

Komponente `camerazone`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Zone Bewegung | `camerazone_<Kanal>_motion` | Ja/Nein |  | 

## DALI
Shelly DALI Dimmer: Anzahl der Vorschaltgeräte, Scan und Prüfung.

Komponente `dali`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
DALI Vorschaltgeräte | `dali_cg_count` | Ganzzahl |  | ja (Zusatzvariable "DALI Aktion")
DALI Scan gefundene Vorschaltgeräte | `dali_scan_cg_count` | Ganzzahl |  | 
Fehler | `dali_errors` | Text |  | 
Störung | `dali_fault` | Ja/Nein |  | 

## BLU-Gerät (BTHome)
Allgemeine Variablen eines BLU-Geräts am Gateway (siehe auch ShellyBLUDevice).

Komponente `bthomedevice`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
RSSI | `bthomedevice_<Kanal>_rssi` | Ganzzahl | dBm | 
Battery | `bthomedevice_<Kanal>_battery` | Ganzzahl | % | 
Letzte Aktualisierung | `bthomedevice_<Kanal>_last_updated_ts` | Ganzzahl | Datum/Uhrzeit | 
Gekoppelt | `bthomedevice_<Kanal>_paired` | Ja/Nein |  | 
Schlüssel hinterlegt | `bthomedevice_<Kanal>_key` | Ja/Nein |  | 
Taste | `bthomedevice_<Kanal>_button` | Text |  | 
Taste 2 | `bthomedevice_<Kanal>_button2` | Text |  | 
Taste 3 | `bthomedevice_<Kanal>_button3` | Text |  | 
Taste 4 | `bthomedevice_<Kanal>_button4` | Text |  | 
Rad | `bthomedevice_<Kanal>_dial` | Text |  | 
Rad Schritte | `bthomedevice_<Kanal>_dialsteps` | Ganzzahl |  | 
Firmware-Version | `bthomedevice_<Kanal>_fw_ver` | Text |  | 
Fehler | `bthomedevice_<Kanal>_errors` | Text |  | 
Störung | `bthomedevice_<Kanal>_fault` | Ja/Nein |  | 

## BLU-Sensor (BTHome)
Messwert eines Sensors eines BLU-Geräts (Typ, Name und Einheit je Sensor).

Komponente `bthomesensor`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
BTHome Wert | `bthomesensor_<Kanal>_value` | Zahl |  | 
Letzte Aktualisierung | `bthomesensor_<Kanal>_last_updated_ts` | Ganzzahl | Datum/Uhrzeit | 

## Energiemessung (PM1)
Messwerte eines Geräts mit Leistungsmessung (z. B. Plug).

Komponente `pm1`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Spannung | `pm1_<Kanal>_voltage` | Zahl | V | 
Stromstärke | `pm1_<Kanal>_current` | Zahl | A | 
Wirkleistung | `pm1_<Kanal>_apower` | Zahl | W | 
Scheinleistung | `pm1_<Kanal>_aprtpower` | Zahl | VA | 
Leistungsfaktor | `pm1_<Kanal>_pf` | Zahl |  | 
Netzfrequenz | `pm1_<Kanal>_freq` | Zahl | Hz | 
Gesamtverbrauch | `pm1_<Kanal>_aenergy_total` | Zahl | kWh | 
Gesamteinspeisung | `pm1_<Kanal>_ret_aenergy_total` | Zahl | kWh | 
Fehler | `pm1_<Kanal>_errors` | Text |  | 
Störung | `pm1_<Kanal>_fault` | Ja/Nein |  | 

## Energiemessung 1 Phase (EM1)
Momentanwerte einer einphasigen Messung.

Komponente `em1`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Stromstärke | `em1_<Kanal>_current` | Zahl | A | 
Spannung | `em1_<Kanal>_voltage` | Zahl | V | 
Wirkleistung | `em1_<Kanal>_act_power` | Zahl | W | 
Scheinleistung | `em1_<Kanal>_aprt_power` | Zahl | VA | 
Leistungsfaktor | `em1_<Kanal>_pf` | Zahl |  | 
Netzfrequenz | `em1_<Kanal>_freq` | Zahl | Hz | 
Fehler | `em1_<Kanal>_errors` | Text |  | 
Störung | `em1_<Kanal>_fault` | Ja/Nein |  | 

## Rollladen / Abdeckung (Cover)
Auf/Zu/Stopp und Position, dazu Messwerte.

Komponente `cover`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Status | `cover_<Kanal>_state` | Text |  | ja (Zusatzvariable "Aktion Status")
Current Position | `cover_<Kanal>_current_pos` | Ganzzahl | % | ja (Zusatzvariable "Position State")
Wirkleistung | `cover_<Kanal>_apower` | Zahl | W | 
Spannung | `cover_<Kanal>_voltage` | Zahl | V | 
Stromstärke | `cover_<Kanal>_current` | Zahl | A | 
Leistungsfaktor | `cover_<Kanal>_pf` | Zahl |  | 
Netzfrequenz | `cover_<Kanal>_freq` | Zahl | Hz | 
Gesamtverbrauch | `cover_<Kanal>_aenergy_total` | Zahl | kWh | 
Temperatur | `cover_<Kanal>_temperature_tC` | Zahl | °C | 
Fehler | `cover_<Kanal>_errors` | Text |  | 
Störung | `cover_<Kanal>_fault` | Ja/Nein |  | 

## Rauchmelder
Rauchalarm.

Komponente `smoke`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Alarm | `smoke_<Kanal>_alarm` | Ja/Nein |  | 
Stumm | `smoke_<Kanal>_mute` | Ja/Nein |  | ja

## Stromversorgung
Batteriestand und externe Stromversorgung.

Komponente `devicepower`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Batteriespannung | `devicepower_<Kanal>_battery_V` | Zahl | V | 
Batteriesstatus | `devicepower_<Kanal>_battery_percent` | Ganzzahl | % | 
Externe Stromquelle | `devicepower_<Kanal>_external_present` | Ja/Nein |  | 
Fehler | `devicepower_<Kanal>_errors` | Text |  | 
Störung | `devicepower_<Kanal>_fault` | Ja/Nein |  | 

## Beleuchtungsstärke
Helligkeitssensor.

Komponente `illuminance`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Beleuchtungsstärke | `illuminance_<Kanal>_lux` | Zahl | lx | 
Beleuchtungsstufe | `illuminance_<Kanal>_illumination` | Text |  | 
Fehler | `illuminance_<Kanal>_errors` | Text |  | 
Störung | `illuminance_<Kanal>_fault` | Ja/Nein |  | 

## Präsenzzone
Zone eines Shelly Presence (Anwesenheit, Anzahl Objekte).

Komponente `presencezone`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Zonenanwesenheit | `presencezone_<Kanal>_value` | Ja/Nein |  | 
Objekte in Zone | `presencezone_<Kanal>_num_objects` | Ganzzahl |  | 

## Virtuelle Komponente: Boolean
Ja/Nein-Wert, am Gerät angelegt.

Komponente `boolean`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Boolean | `boolean_<Kanal>_value` | Ja/Nein |  | ja

## Virtuelle Komponente: Number
Zahlenwert, am Gerät angelegt.

Komponente `number`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Zahl | `number_<Kanal>_value` | Zahl |  | ja

## Virtuelle Komponente: Enum
Auswahl aus mehreren Werten, am Gerät angelegt.

Komponente `enum`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Aufzählung | `enum_<Kanal>_value` | Text |  | ja

## Virtuelle Komponente: Text
Textwert, am Gerät angelegt.

Komponente `text`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Text | `text_<Kanal>_value` | Text |  | ja

## Objekt-Komponente
Werte von Geräten, die ihre Messwerte als Objekt melden.

Komponente `object`

Variable | Ident | Typ | Einheit | Aktion
------------ | ------------ | ------------ | ------------ | ------------
Gesamtverbrauch | `object_<Kanal>_value_counter_total` | Zahl | kWh | 
Gesamtstrom | `object_<Kanal>_value_total_current` | Zahl | A | 
Gesamtleistung | `object_<Kanal>_value_total_power` | Zahl | W | 
Gesamtwirkenergie | `object_<Kanal>_value_total_act_energy` | Zahl | kWh | 
Phase A Spannung | `object_<Kanal>_value_phase_a_voltage` | Zahl | V | 
Phase A Stromstärke | `object_<Kanal>_value_phase_a_current` | Zahl | A | 
Phase A Wirkleistung | `object_<Kanal>_value_phase_a_power` | Zahl | W | 
Phase B Spannung | `object_<Kanal>_value_phase_b_voltage` | Zahl | V | 
Phase B Stromstärke | `object_<Kanal>_value_phase_b_current` | Zahl | A | 
Phase B Wirkleistung | `object_<Kanal>_value_phase_b_power` | Zahl | W | 
Phase C Spannung | `object_<Kanal>_value_phase_c_voltage` | Zahl | V | 
Phase C Stromstärke | `object_<Kanal>_value_phase_c_current` | Zahl | A | 
Phase C Wirkleistung | `object_<Kanal>_value_phase_c_power` | Zahl | W | 

