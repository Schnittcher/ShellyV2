# Changelog

## 02.11.2026 - Version 1.1.7 (Beta Version)

### Neu
- BLU-Geräte mit Temperatur, Luftfeuchtigkeit und Wind (z. B. die Wetterstation) haben zwei berechnete Variablen: "Gefühlte Temperatur" (Windchill bei Kälte, Hitzeindex bei Wärme) und "Windrichtung (Text)" (Himmelsrichtung). Sie entstehen beim nächsten Auslesen der Komponenten und lassen sich in der Liste Variablen abwählen.

### Fixes
- Die Adresse der Module (Dokumentation) verweist jetzt auf die Seite der jeweiligen Instanz auf deinsmarthome.info.

## 30.10.2026 - Version 1.1.6 (Beta Version)

### Fixes
- Die Liste "Variablen" in der Instanz stimmt wieder mit den Variablen überein: Bisher konnte beim Abwählen einer Variable ein Haken bei einer anderen Variable landen, und Einträge erschienen ohne Namen. Die gespeicherte Auswahl wird jetzt auch nach einem Neustart von Symcon richtig angezeigt.

## 24.10.2026 - Version 1.1.5 (Beta Version)

### Fixes
- BLU TRV: Die Variable "Externe Temperatur" zeigte bisher die aktuelle Temperatur des Thermostats. Jetzt heißt diese Variable "Aktuelle Temperatur" (nur lesbar), und die neue Variable "Externe Temperatur" zeigt den Wert des Sensors der externen Temperatur und ist steuerbar. Achtung: Skripte, die die bisherige Variable zum Setzen der externen Temperatur genutzt haben, müssen die neue Variable verwenden; die bisherige wird beim nächsten Auslesen umbenannt.
- BLU TRV: Zieltemperatur, aktuelle und externe Temperatur werden aus den BTHome-Sensoren des Geräts übernommen, die es alle 8 Sekunden sendet. Der Status des Thermostats selbst kommt nur selten, deshalb waren diese Variablen bisher oft veraltet. Ein später eintreffender, älterer Status überschreibt sie nicht mehr.
- BLU-Geräte: Fällt ein Gateway aus, wechselt "Stärkstes Gateway" sofort zum nächsten, sobald der MQTT-Server dessen Status "offline" meldet.
- Der Konfigurator zeigt die Shelly RGBCCT Bulb Gen3 mit Namen an statt "Unbekannt".
- Der Konfigurator zeigt ein Gerät jetzt schon nach seiner ersten Meldung an. Bisher erschien es erst nach einer zweiten, ein Gerät, das nicht auf die Suche antwortet und sich nur einmal meldet (z. B. das Shelly Wall Display), fehlte in der Liste. Das Shelly Wall Display wird mit Namen angezeigt.
- Instanzen mit ähnlichen Topics (z. B. "shelly_1og" und "shelly_1og_wohnzimmer") beeinflussen sich nicht mehr: Bisher setzte die Meldung "online" des einen Geräts auch "Erreichbar" des anderen, beim Shelly XT1 galt das auch für Statusmeldungen.
- Verbindet sich ein Shelly (wieder) mit dem MQTT-Server, z. B. nach einem Neustart oder Stromausfall, rufen die Instanzen dieses Geräts zeitlich verteilt den aktuellen Status ihrer Komponenten ab. Bisher fehlten die Änderungen, die in der Zeit ohne Verbindung geschehen waren, bis zur nächsten Meldung des Geräts.

## 23.10.2026 - Version 1.1.4 (Beta Version)

### Fixes
- Beim Start von IP-Symcon fragen die Instanzen den Shelly deutlich weniger ab: BLU-Instanzen gar nicht mehr, die übrigen nur noch den Status ihrer eigenen Komponenten. Das vermeidet Überlast bei Geräten mit vielen BLU-Geräten (z. B. Shelly Strip), die dabei neu starteten.
- Nach einem Update liest jede Instanz einmal vollständig, zeitlich verteilt statt gleichzeitig. Änderungen an der Konfiguration einer Instanz lesen sofort.

## 22.10.2026 - Version 1.1.3 (Beta Version)

### Fixes
- BLU-Geräte: Nach einem Neustart von Symcon reagieren die Instanzen wieder von selbst auf Meldungen der Gateways. Ein manuelles "Komponenten auslesen" ist nicht mehr nötig.
- Fehler und Störung nennen jetzt die Komponente im Namen (z. B. "Schalter Fehler 1", "Eingang Störung"), damit man sie unterscheiden kann. Bei bestehenden Instanzen werden die bisherigen Standardnamen "Fehler" und "Störung" beim nächsten Auslesen umbenannt, selbst vergebene Namen bleiben.
- Beim kompletten Gerät (z. B. einem BLU Gateway) entstehen keine Variablen der angelernten BLU-Geräte mehr, die gibt es nur in der Instanz ShellyBLUDevice. Der Thermostat (BLU TRV) bleibt.
- Achtung: Bei bestehenden Instanzen bleiben die bisherigen BLU-Variablen des Gateways stehen, werden aber nicht mehr aktualisiert und können gelöscht werden.
- Wählt man in der Liste "Variablen" einzelne Variablen ab, verschwinden dadurch keine weiteren Variablen mehr.
- Fehlende deutsche Übersetzungen ergänzt, z. B. Aktuelle Position, Batterie, Impulse gesamt, Dimmen, Aufnahme und die Räume des Shelly Cury.

## 05.10.2026 - Version 1.1.2 (Beta Version)

### Neu
- Eingänge vom Typ Taster (z. B. an einem Rollladenaktor wie dem Shelly 2PM Gen4) haben die Variable "Taste" mit dem letzten Tastendruck (einfach, doppelt, dreifach oder lang), je Eingang eine eigene.
- Die Variable "Eingangsstatus" gibt es nur noch bei Eingängen vom Typ Schalter, weil das Gerät für Taster keinen Status meldet.
- Achtung: Bei bestehenden Instanzen bleibt die nicht mehr genutzte Variable "Eingangsstatus" der Taster stehen und kann gelöscht werden.
- Der Konfigurator weist darauf hin, dass batteriebetriebene Geräte oft nicht gefunden werden und dann von Hand angelegt werden müssen.

### Fixes
- Fehler und Störung nennen jetzt die Komponente im Namen (z. B. "Schalter Fehler 1", "Eingang Störung"), damit man sie unterscheiden kann. Bei bestehenden Instanzen werden die bisherigen Standardnamen "Fehler" und "Störung" beim nächsten Auslesen umbenannt, selbst vergebene Namen bleiben.
- Beim kompletten Gerät (z. B. einem BLU Gateway) entstehen keine Variablen der angelernten BLU-Geräte mehr, die gibt es nur in der Instanz ShellyBLUDevice. Der Thermostat (BLU TRV) bleibt.
- Achtung: Bei bestehenden Instanzen bleiben die bisherigen BLU-Variablen des Gateways stehen, werden aber nicht mehr aktualisiert und können gelöscht werden.
- Wählt man in der Liste "Variablen" einzelne Variablen ab, verschwinden dadurch keine weiteren Variablen mehr.
- Fehlende deutsche Übersetzungen ergänzt, z. B. Aktuelle Position, Batterie, Impulse gesamt, Dimmen, Aufnahme und die Räume des Shelly Cury.

## 04.10.2026 - Version 1.1.1 (Beta Version)

### Neu
- BLU-Geräte (BTHome): Eine Instanz je Gerät legt alle Messwerte (Name und Einheit kommen vom Gerät) sowie Batterie, Empfang, Kopplung, Verschlüsselungs-Status, Firmware, Fehler und Störung als Variablen an.
- Die neue Instanz "ShellyBLUDevice" erkennt das Gerät über die MAC-Adresse und kann an mehreren Gateways angelernt sein.
- Bei mehreren Gateways kommen die Messwerte vom Gateway mit dem neuesten Zeitstempel, und ein Tastendruck zählt nur einmal.
- Das Gerät gilt als erreichbar, solange ein Gateway online ist.
- Je Gateway gibt es RSSI und "Letzte Aktualisierung", dazu "Stärkstes Gateway" (z. B. für die Raumerkennung).
- Ein BLU TRV (Thermostat) hat zusätzlich Zieltemperatur, Position und externe Temperatur.
- Geräte ohne eigenen Namen erscheinen mit ihrer MAC-Adresse.
- Der Konfigurator zeigt bei BLU-Geräten den Gerätetyp (Spalte "Device Type") und fasst sie (außer BLU TRVs) in der Gruppe "BLU-Geräte" mit einer Zeile je Gerät zusammen.
- Der Instanzname bekommt bei BLU-Geräten den Typ vorangestellt (z. B. "Shelly BLU Button Tough 1 ZB - <MAC>").
- Die Instanz legt alle Sensoren des Modells direkt an (z. B. Fenster, Luftfeuchtigkeit, Temperatur, Beleuchtungsstärke), auch wenn das Gateway sie noch nicht gemeldet hat.
- Der letzte Tastendruck von BLU-Geräten steht in der Variable "Taste", bei Geräten mit Taste von Anfang an, sonst ab dem ersten Druck.
- Geräte mit mehreren Tasten (z. B. BLU RC Button 4, Wall Switch 4) haben "Taste 1" bis "Taste 4".
- Die BLU Remote Control ZB hat für das Drehrad "Rad" (Hoch gedreht / Runter gedreht) und "Rad Schritte" (runter negativ), ihre drei Drehwinkel heißen "Drehung 1" bis "Drehung 3".
- Bei einem Tastendruck werden die mitgelieferten Sensorwerte übernommen und der Gerätestatus (Empfang, letzte Aktualisierung, Batterie) neu abgefragt.
- Alle Komponenten, die Fehler melden (u. a. Switch, Cover, Light, RGB/RGBW/CCT, PM1, EM, EM1, Input, Temperatur, Feuchte, Flood, CB, Cury, Kamera, DALI, BTHome), haben die Variablen "Fehler" (Fehlercodes übersetzt, z. B. "Übertemperatur, Überlast") und "Störung" (Ja/Nein).
- Melden die Geräte keinen Fehler mehr, werden "Fehler" und "Störung" beim nächsten Auslesen zurückgesetzt, unbekannte Fehlercodes werden unverändert angezeigt.
- KVS (Key-Value-Store der Shellies): Neue Funktionen zum Lesen und Schreiben des persistenten Speichers auf dem Gerät: SHY_KVSSet, SHY_KVSSetIfUnchanged, SHY_KVSGet, SHY_KVSDelete, SHY_KVSList und SHY_KVSGetMany (mit Muster und seitenweiser Abfrage).

### Fixes
- Mehrere Instanzen desselben Geräts stören sich beim Auslesen der Komponenten nicht mehr gegenseitig, und ein zweiter, gleichzeitiger Abruf überschreibt das fertige Ergebnis nicht mehr (dadurch fehlten bei Geräten mit vielen Komponenten teils Variablen).
- BLU-Geräte: Ereignisse, die keine Tastendrücke sind (z. B. "config_changed"), erscheinen nicht mehr in der Variable "Taste".
- Flood: "Stummschalten" ist jetzt schreibgeschützt, weil die Shelly-API dafür keine Methode kennt.
- BLU TRV: Die Position wird mit der in der Doku genannten Methode TRV.SetPosition gesetzt.
- CCT: Die Variable für den Gesamtverbrauch entfällt, weil der CCT-Status laut Doku keinen Energiezähler enthält.
- DALI: Die Fehler stehen in der Variable "Fehler" der Komponente, die bisherige Variable "DALI scan errors" entfällt.

## 04.10.2026 - Version 1.1 (Beta Version)

### Neu
- The Pill by Shelly wird im Konfigurator erkannt und kann samt angeschlossener Add-on-Sensoren (z. B. Temperatur) angelegt werden.
- Alle Komponenten und Variablen werden über Shelly.GetComponents ermittelt, dadurch werden auch Add-on-Sensoren, BLU TRVs und virtuelle Komponenten zuverlässig gefunden.
- Die Checkbox "Status über Shelly.GetComponents beziehen" entfällt.
- Das generische Shelly Device kann auch Powered-by-Shelly-Geräte (XT1) anlegen, der Konfigurator bietet diese zusätzlich als "ShellyDevice (generisch)" an.
- Der auf dem Gerät vergebene Name steht bei normalen Komponenten vor dem Variablennamen (z. B. "Waschmaschine - Wirkleistung").
- Min-, Max- und Schrittweite-Werte werden vom Gerät übernommen (z. B. Stromlimit der Wallbox, Farbtemperatur bei CCT, BLU TRV), fehlt die Schrittweite, gilt 1.
- Weitere Übersetzungen für die vom Gerät gelieferten Namen (z. B. Smart WaterValve, LinkedGo Thermostate).
- RGBCCT-Komponente (z. B. Lampen mit RGB und Weißtemperatur): Schalter, Modus RGB/CCT, Farbe, Farbtemperatur, Helligkeit mit Dim up/down/stop, Wirkleistung und Gesamtverbrauch.
- CB-Komponente (Shelly Pro 3CB, Leitungsschutzschalter): Schutzschalter-Status (Auslösen per Fernsteuerung), Sicherheitsschalter, Anzahl Auslösungen, Temperatur und Fehler.
- Cury-Komponente (Shelly Cury, Duftspender): Raummodus, Abwesenheitsmodus sowie je Fach Status, Boost, Intensität, Füllstand, Name und Seriennummer des Fläschchens und Fehler, auch bei leerem Fach.
- DALI-Komponente (Shelly DALI Dimmer Gen3): Anzahl der gefundenen Vorschaltgeräte, Scan-Ergebnis und Scan-Fehler sowie die Aktionen "Scan starten" und "Geräte prüfen".
- Shelly Camera (S1CM-0DXW00): scharf, Privatsphäre-Modus, Bewegung, Bewegungszonen (mit Zonennamen), Aktionen (Schnappschuss, Aufnahme starten/stoppen, Ton abspielen) und Geräteeinstellungen (RTSP, LED, Töne, Aufnahme bei Bewegung, Bewegungsempfindlichkeit, Nachtsicht).
- Bei einer Kamera legt die Geräte- bzw. Komponenten-Instanz die Stream-Objekte für den RTSP-Stream (Hauptstream und zweiter Stream) automatisch an, sobald die IP-Adresse bekannt ist.
- Das automatische Anlegen der Stream-Objekte lässt sich abschalten, es gibt den Button "Stream-Objekte aktualisieren" und optional Benutzer und Passwort, falls am Gerät die Authentifizierung aktiv ist.
- Das Modul nutzt die Basisklasse IPSModuleStrict und braucht damit Symcon 8.1 oder neuer.
- Der Datenfluss zum MQTT-Server/-Client ist HEX-kodiert, Sonderzeichen kommen ohne Umwege korrekt an.
- Bei neuen Instanzen wird der MQTT-Server/-Client automatisch von der Verwaltungskonsole verbunden.

### Fixes
- Variablen werden beim Anlegen einer Instanz automatisch erzeugt, bisher existierte nur "Erreichbar", bis "Komponenten auslesen" gedrückt wurde.
- Warnung "Entry in parameter OPTIONS includes unknown sub-parameters" und ungültiges Formular bei Variablen mit Aufzählung (z. B. Wallbox, Helligkeit Dim up/down/stop) behoben.
- Sonderzeichen wie "°C" werden korrekt dargestellt.
- Variablennamen bei Geräten mit mehreren Kanälen und virtuellen Komponenten zeigen den vom Gerät gelieferten Namen statt z. B. "Boolean 200".
- Der Konfigurator findet alle Komponenten eines Geräts (paginierte Abfrage) und lädt dabei alle Geräte gleichzeitig.
- Im Konfigurator werden die Komponenten wieder dem richtigen Gerät zugeordnet (vorher hatten alle Geräte dieselbe ID).
- Darstellungen entsprechen der Symcon-Dokumentation (Reachable-Variable des XT1 Device, Rollladen-Position, Wertanzeigen von Alarm, Rauchmelder und Water Valve), dadurch entfallen Warnungen in der Konsole.
- Die Farbe bei RGB-, RGBW- und RGBCCT-Komponenten wird korrekt angezeigt und gesetzt (vorher blieb die Farb-Variable leer, beim Setzen konnten Rot und Blau vertauscht werden).
- Meldet ein Gerät für einen Wert "kein Wert" (null, z. B. Temperatur ohne Messwert), behält die Variable ihren letzten Wert statt auf 0 zu springen.
- Beim Laden des Moduls erscheinen keine Warnungen zu fehlenden Typangaben mehr.

## 30.08.2026 - Version 1.0.13 (Beta Version)

### Neu
- Shelly Presence.
- Virtuelle / Dynamische Komponenten werden immer angelegt, die Checkbox dafür entfällt.
- Top AC Portable EV Charger ist als XT1 Device anlegbar.
- Objekte der Geräte werden ausgewertet, z. B. für den Wasserverbrauch beim Smart Neo Water Valve.

### Fixes
- Das Konfigurationsformular wird neu geladen, wenn die Variablen abgerufen werden.

## 10.08.2026 - Version 1.0.12 (Beta Version)

### Neu
- Virtuelle Komponenten: Es wird geprüft, ob die Variable schreibbar sein muss, und Min- und Max-Werte werden beachtet, sofern vorhanden.

### Fixes
- Übersetzungen.

## 03.08.2026 - Version 1.0.11 (Beta Version)

### Neu
- Virtuelle Komponenten können angelegt werden.
- Button zum Auslesen der Variablen in der Instanz.

## 26.07.2026 - Version 1.0.10 (Beta Version)

### Fixes
- Der Konfigurator öffnet sich nun innerhalb von ca. 5 Sekunden.

## 22.04.2026 - Version 1.0.9 (Beta Version)

### Neu
- TRV Batterie-Variable.
- Neo Smart Water Valve.

### Fixes
- BLU TRVs werden im Konfigurator wieder gefunden.

## 17.02.2026 - Version 1.0.8 (Beta Version)

### Neu
- Shelly MQTT-Einstellungen können über den Konfigurator gesetzt werden.

## 31.01.2026 - Version 1.0.7 (Beta Version)

### Neu
- ShellyXT1 Device (Powered-by-Shelly-Geräte).

## 07.12.2025 - Version 1.0.6

### Neu
- Voltmeter.
- Input: Counts und Frequenz.

### Fixes
- Darstellungen.

## 29.11.2025 - Version 1.0.5 (Beta Version)

### Fixes
- Konfigurator und Events.

## 24.11.2025 - Version 1.0.4 (Beta Version)

### Neu
- Shelly BLU TRV.
- Shelly Flood: Variable "Fehler".

## 20.11.2025 - Version 1.0.3 (Beta Version)

### Neu
- RGBW-Komponente.

### Fixes
- Beim Start von Symcon wird geprüft, ob das Parent aktiv ist, das behebt den Fehler "Instanz wird erstellt".

## 14.11.2025 - Version 1.0.2

### Fixes
- Variablen können wieder deaktiviert werden.
- Übersetzungen angepasst.

## 11.11.2025 - Version 1.0.1 (Beta Version)

### Neu
- Events.

### Fixes
- Übersetzungen angepasst.

## 02.11.2025 - Version 1.0.0 (Beta Version)

### Neu
- Erste Beta Version.