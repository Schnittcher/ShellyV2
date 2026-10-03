03.10.2026 - Version 1.0.14 (Beta Version)
Neu: The Pill by Shelly wird im Configurator erkannt und kann angelegt werden, inklusive der angeschlossenen Add-on-Sensoren (z. B. Temperatur).
Neu: Alle Komponenten und Variablen werden jetzt ausschließlich über Shelly.GetComponents ermittelt. Dadurch werden auch Add-on-Sensoren, BLU TRVs und virtuelle Komponenten zuverlässig gefunden. Die Checkbox "Status über Shelly.GetComponents beziehen" entfällt.
Neu: Das generische Shelly Device kann auch Powered by Shelly Geräte (XT1) anlegen, der Configurator bietet diese zusätzlich als "ShellyDevice (generisch)" an.
Neu: Der vom Nutzer auf dem Gerät vergebene Name wird bei normalen Komponenten als Präfix vor die Variablennamen geschrieben (z. B. "Waschmaschine - Wirkleistung").
Neu: Min-, Max- und Schrittweite-Werte werden vom Gerät übernommen, sofern es sie liefert (z. B. Stromlimit der Wallbox, Farbtemperatur bei CCT, BLU TRV). Fehlt die Schrittweite, wird 1 verwendet.
Neu: Weitere Übersetzungen für die von den Geräten gelieferten Namen (z. B. Smart WaterValve, LinkedGo Thermostate).
Fix: Variablen werden beim Anlegen einer Instanz automatisch erzeugt. Bisher existierte nur "Erreichbar", bis "Komponenten auslesen" gedrückt wurde.
Fix: Warnung "Entry in parameter OPTIONS includes unknown sub-parameters" und ungültiges Formular bei Variablen mit Aufzählung (z. B. Wallbox, Helligkeit Dim up/down/stop).
Fix: Sonderzeichen wie "°C" werden korrekt dargestellt.
Fix: Namen von Variablen bei Geräten mit mehreren Kanälen und virtuellen Komponenten (z. B. "Boolean 200" statt dem vom Gerät gelieferten Namen).
Fix: Der Configurator findet alle Komponenten eines Geräts (paginierte Abfrage) und lädt dabei alle Geräte gleichzeitig.

30.08.2026 - Version 1.0.13 (Beta Version)
Neu: Shelly Presence
Neu: Virtuelle / Dynamische Komponenten werden nun immer angelegt, es muss keine
Checkbox mehr aktivert werden.
Neu: Top AC Portable EV Charger als XT1 Device anlegbar
Fix: Konfigurationsform wird neu geladen, wenn man die Variablen abruft.
FIx: Objects können nun auch ausgewertet werden, dies wird zum Beispiel beim Smart Neo Water Valve für Wasserverbrauch benötigt
