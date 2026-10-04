04.10.2026 - Version 1.1.1 (Beta Version)
Neu: BTHome (Shelly BLU-Geräte an einem Gateway): Eine Instanz für ein BLU-Gerät (bthomedevice) legt auch alle seine Sensoren an, jeder Messwert als eigene Variable (Zahl, Ja/Nein oder Text) mit Name und Einheit vom Gerät (BTHome.GetObjectInfos) und dem Gerätenamen als Präfix. Dazu Batterie, Empfang, Kopplung, Verschlüsselungs-Status, Firmware und der letzte Tastendruck (single_push, double_push, triple_push, ...), der bei Geräten mit erkennbarer Taste von Anfang an und sonst ab dem ersten Tastendruck angelegt wird. Bei einem BLU TRV (Thermostat) enthält die Instanz zusätzlich die bedienbaren Variablen Zieltemperatur, Position und externe Temperatur. Im Konfigurator erscheinen die Sensoren nicht mehr einzeln.
Neu: BLU-Geräte und BLU TRVs ohne eigenen Namen werden im Konfigurator und als Präfix der Variablennamen mit ihrer MAC-Adresse angezeigt.
Neu: Instanz "ShellyBLUDevice" für ein BLU-Gerät, das an einem oder mehreren Gateways angelernt ist (Erkennung über die MAC-Adresse, eine Instanz je Gerät). Messwerte kommen vom Gateway mit dem neuesten Zeitstempel, ein Tastendruck von jedem Gateway (derselbe Druck auf zwei Gateways zählt einmal), das Gerät ist erreichbar, solange ein Gateway online ist. Je Gateway gibt es RSSI und "Letzte Aktualisierung", dazu die Variable "Stärkstes Gateway" (z. B. für die Raumerkennung). Der Konfigurator zeigt die BLU-Geräte (außer BLU TRVs) in der Gruppe "BLU-Geräte" mit einer Zeile je MAC-Adresse.
Neu: Bei einem Tastendruck am BLU-Gerät werden die mitgelieferten Sensorwerte übernommen und der Gerätestatus (Empfang, letzte Aktualisierung, Batterie) neu abgefragt, so dass alle Variablen des Geräts aktuell sind.
Neu: Alle Komponenten, die Fehler melden (u. a. Switch, Cover, Light, RGB/RGBW/CCT, PM1, EM, EM1, Input, Temperatur, Feuchte, Flood, CB, Cury, Kamera, DALI, BTHome), haben die Variablen "Fehler" (die Fehlercodes übersetzt, z. B. "Übertemperatur, Überlast") und "Störung" (Ja/Nein, wird true, sobald ein Fehler vorliegt). Melden die Geräte keinen Fehler mehr, werden beide beim nächsten Auslesen zurückgesetzt. Unbekannte Fehlercodes werden unverändert angezeigt.
Fix: Mehrere Instanzen desselben Geräts (z.B. eine je Komponente) stören sich beim Auslesen der Komponenten nicht mehr gegenseitig, und ein zweiter, gleichzeitig angestoßener Abruf überschreibt das fertige Ergebnis nicht mehr (dadurch fehlten bei Geräten mit vielen Komponenten teils Variablen).
Fix: Flood: "Stummschalten" ist jetzt schreibgeschützt (die Shelly-API kennt dafür keine Methode, die Variable zeigt nur den Zustand).
Fix: BLU TRV: Die Position wird mit der in der Doku genannten Methode TRV.SetPosition gesetzt.
Fix: CCT: Die Variable für den Gesamtverbrauch entfällt, der CCT-Status enthält laut Doku keinen Energiezähler.
Fix: DALI: Die Fehler stehen in der Variable "Fehler" der Komponente (laut Doku nicht unter dem Scan, die bisherige Variable "DALI scan errors" entfällt).

04.10.2026 - Version 1.1 (Beta Version)
Neu: The Pill by Shelly wird im Configurator erkannt und kann angelegt werden, inklusive der angeschlossenen Add-on-Sensoren (z. B. Temperatur).
Neu: Alle Komponenten und Variablen werden jetzt ausschließlich über Shelly.GetComponents ermittelt. Dadurch werden auch Add-on-Sensoren, BLU TRVs und virtuelle Komponenten zuverlässig gefunden. Die Checkbox "Status über Shelly.GetComponents beziehen" entfällt.
Neu: Das generische Shelly Device kann auch Powered by Shelly Geräte (XT1) anlegen, der Configurator bietet diese zusätzlich als "ShellyDevice (generisch)" an.
Neu: Der vom Nutzer auf dem Gerät vergebene Name wird bei normalen Komponenten als Präfix vor die Variablennamen geschrieben (z. B. "Waschmaschine - Wirkleistung").
Neu: Min-, Max- und Schrittweite-Werte werden vom Gerät übernommen, sofern es sie liefert (z. B. Stromlimit der Wallbox, Farbtemperatur bei CCT, BLU TRV). Fehlt die Schrittweite, wird 1 verwendet.
Neu: Weitere Übersetzungen für die von den Geräten gelieferten Namen (z. B. Smart WaterValve, LinkedGo Thermostate).
Neu: RGBCCT-Komponente (z. B. Lampen mit RGB und Weißtemperatur): Schalter, Modus RGB/CCT, Farbe, Farbtemperatur, Helligkeit mit Dim up/down/stop, Wirkleistung und Gesamtverbrauch.
Neu: CB-Komponente (Shelly Pro 3CB, Leitungsschutzschalter): Schutzschalter-Status (Auslösen per Fernsteuerung), Sicherheitsschalter, Anzahl Auslösungen, Temperatur und Fehler.
Neu: Cury-Komponente (Shelly Cury, Duftspender): Raummodus, Abwesenheitsmodus sowie je Fach Status, Boost, Intensität, Füllstand, Name und Seriennummer des Fläschchens und Fehler. Die Variablen der Fächer werden auch bei leerem Fach angelegt.
Neu: DALI-Komponente (Shelly DALI Dimmer Gen3): Anzahl der gefundenen Vorschaltgeräte, Scan-Ergebnis und Scan-Fehler sowie Aktionen "Scan starten" und "Geräte prüfen".
Neu: Shelly Camera (S1CM-0DXW00): scharf, Privatsphäre-Modus, Bewegung, Bewegungszonen (mit Zonennamen), Aktionen (Schnappschuss, Aufnahme starten/stoppen, Ton abspielen) und Geräteeinstellungen (RTSP, LED, Töne, Aufnahme bei Bewegung, Bewegungsempfindlichkeit, Nachtsicht).
Neu: Bei einer Kamera legt die Geräte- bzw. Komponenten-Instanz die Stream-Objekte für den RTSP-Stream (Hauptstream und zweiter Stream) automatisch an, sobald die IP-Adresse des Geräts bekannt ist (abschaltbar, Button "Stream-Objekte aktualisieren", optional Benutzer und Passwort, falls am Gerät die Authentifizierung aktiv ist).
Neu: Das Modul nutzt jetzt die Basisklasse IPSModuleStrict (Symcon 8.1 oder neuer). Der Datenfluss zum MQTT-Server/-Client ist damit HEX-kodiert, Sonderzeichen kommen ohne Umwege korrekt an. Der MQTT-Server/-Client wird bei neuen Instanzen automatisch von der Verwaltungskonsole verbunden.
Fix: Variablen werden beim Anlegen einer Instanz automatisch erzeugt. Bisher existierte nur "Erreichbar", bis "Komponenten auslesen" gedrückt wurde.
Fix: Warnung "Entry in parameter OPTIONS includes unknown sub-parameters" und ungültiges Formular bei Variablen mit Aufzählung (z. B. Wallbox, Helligkeit Dim up/down/stop).
Fix: Sonderzeichen wie "°C" werden korrekt dargestellt.
Fix: Namen von Variablen bei Geräten mit mehreren Kanälen und virtuellen Komponenten (z. B. "Boolean 200" statt dem vom Gerät gelieferten Namen).
Fix: Der Configurator findet alle Komponenten eines Geräts (paginierte Abfrage) und lädt dabei alle Geräte gleichzeitig.
Fix: Im Configurator werden die Komponenten jetzt immer dem richtigen Gerät zugeordnet (vorher hatten alle Geräte dieselbe ID).
Fix: Darstellungen entsprechen jetzt der Symcon-Dokumentation (Reachable-Variable des XT1 Device, Rollladen-Position, Wertanzeigen von Alarm, Rauchmelder und Water Valve), dadurch entfallen Warnungen in der Konsole.
Fix: Die Farbe bei RGB-, RGBW- und RGBCCT-Komponenten wird korrekt angezeigt und gesetzt (vorher blieb die Farb-Variable leer, beim Setzen konnten Rot und Blau vertauscht werden).
Fix: Meldet ein Gerät für einen Wert "kein Wert" (null, z. B. Temperatur ohne Messwert), behält die Variable ihren letzten Wert statt auf 0 zu springen.
Fix: Keine Warnungen mehr zu fehlenden Typangaben beim Laden des Moduls (alle öffentlichen Funktionen sind jetzt typisiert).

30.08.2026 - Version 1.0.13 (Beta Version)
Neu: Shelly Presence
Neu: Virtuelle / Dynamische Komponenten werden nun immer angelegt, es muss keine
Checkbox mehr aktivert werden.
Neu: Top AC Portable EV Charger als XT1 Device anlegbar
Fix: Konfigurationsform wird neu geladen, wenn man die Variablen abruft.
FIx: Objects können nun auch ausgewertet werden, dies wird zum Beispiel beim Smart Neo Water Valve für Wasserverbrauch benötigt
