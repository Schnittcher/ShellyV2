05.10.2026 - Version 1.1.2 (Beta Version)
Neu: Eingänge vom Typ Taster (z. B. an einem Rollladenaktor wie dem Shelly 2PM Gen4) haben die Variable "Taste" mit dem letzten Tastendruck (einfach, doppelt, dreifach oder lang gedrückt), je Eingang eine eigene. Die Variable "Eingangsstatus" gibt es nur noch bei Eingängen vom Typ Schalter, weil das Gerät für Taster keinen Status meldet. Bei bestehenden Instanzen bleibt die nicht mehr genutzte Variable "Eingangsstatus" der Taster stehen und kann gelöscht werden.

04.10.2026 - Version 1.1.1 (Beta Version)
Neu: BLU-Geräte (BTHome): Eine Instanz je BLU-Gerät legt alle seine Messwerte als Variablen an (Name und Einheit kommen vom Gerät), dazu Batterie, Empfang, Kopplung, Verschlüsselungs-Status, Firmware sowie Fehler und Störung. Die neue Instanz "ShellyBLUDevice" erkennt das Gerät über die MAC-Adresse und kann an mehreren Gateways angelernt sein: Messwerte kommen vom Gateway mit dem neuesten Zeitstempel, ein Tastendruck von jedem Gateway (derselbe Druck zählt einmal), das Gerät ist erreichbar, solange ein Gateway online ist. Je Gateway gibt es RSSI und "Letzte Aktualisierung", dazu "Stärkstes Gateway" (z. B. für die Raumerkennung). Bei einem BLU TRV (Thermostat) enthält die Instanz zusätzlich Zieltemperatur, Position und externe Temperatur. Geräte ohne eigenen Namen erscheinen mit ihrer MAC-Adresse.
Neu: Liste der BLU-Modelle (libs/BTHomeModels.php, Erkennung über die Modell-ID des Geräts): Der Konfigurator zeigt den Gerätetyp (Spalte "Device Type") und fasst die BLU-Geräte (außer BLU TRVs) in der Gruppe "BLU-Geräte" mit einer Zeile je MAC-Adresse zusammen; die Liste zeigt nur die MAC, der Instanzname bekommt den Typ davor (z. B. "Shelly BLU Button Tough 1 ZB - f8:44:77:43:0d:86"). Die Instanz legt alle Sensoren des Modells direkt an (z. B. Fenster, Luftfeuchtigkeit, Temperatur, Beleuchtungsstärke), auch wenn das Gateway sie noch nicht gemeldet hat; die Idents sind an allen Gateways gleich.
Neu: Tasten und Rad der BLU-Geräte: Der letzte Tastendruck steht in der Variable "Taste" (bei Geräten mit Taste von Anfang an, sonst ab dem ersten Druck). Geräte mit mehreren Tasten (z. B. BLU RC Button 4, Wall Switch 4) haben "Taste 1" bis "Taste 4". Die BLU Remote Control ZB hat für das Drehrad "Rad" (Hoch gedreht / Runter gedreht) und "Rad Schritte" (runter negativ); ihre drei Drehwinkel heißen "Drehung 1" bis "Drehung 3". Bei einem Tastendruck werden die mitgelieferten Sensorwerte übernommen und der Gerätestatus (Empfang, letzte Aktualisierung, Batterie) neu abgefragt.
Neu: Alle Komponenten, die Fehler melden (u. a. Switch, Cover, Light, RGB/RGBW/CCT, PM1, EM, EM1, Input, Temperatur, Feuchte, Flood, CB, Cury, Kamera, DALI, BTHome), haben die Variablen "Fehler" (die Fehlercodes übersetzt, z. B. "Übertemperatur, Überlast") und "Störung" (Ja/Nein, wird true, sobald ein Fehler vorliegt). Melden die Geräte keinen Fehler mehr, werden beide beim nächsten Auslesen zurückgesetzt. Unbekannte Fehlercodes werden unverändert angezeigt.
Neu: KVS (Key-Value-Store der Shellies): Neue Funktionen der Instanz zum Lesen und Schreiben des persistenten Speichers auf dem Gerät: SHY_KVSSet, SHY_KVSSetIfUnchanged, SHY_KVSGet, SHY_KVSDelete, SHY_KVSList und SHY_KVSGetMany (mit Muster und seitenweiser Abfrage).
Fix: Mehrere Instanzen desselben Geräts (z.B. eine je Komponente) stören sich beim Auslesen der Komponenten nicht mehr gegenseitig, und ein zweiter, gleichzeitig angestoßener Abruf überschreibt das fertige Ergebnis nicht mehr (dadurch fehlten bei Geräten mit vielen Komponenten teils Variablen).
Fix: BLU-Geräte: Ereignisse, die keine Tastendrücke sind (z. B. "config_changed" beim Ändern der Geräteeinstellungen), erscheinen nicht mehr in der Variable "Taste".
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
