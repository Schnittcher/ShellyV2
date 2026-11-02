# Projektkontext

Fachliche und technische Orientierung für das Repository `ShellyV2` (Bibliothek "Shelly Gen2+"). Ergänzt die zentralen Regeln, wiederholt sie aber nicht.

## Zweck und Benutzer

- Projektzweck: Alle Shellies ab Generation 2 (auch BLU-Geräte über BLU-Gateways) per MQTT und RPC in Symcon einbinden. Variablen, Aktionen und Geräte-Erkennung entstehen aus dem, was das Gerät selbst meldet (`Shelly.GetComponents`).
- Hauptnutzer: Privatanwender und Installateure, die Shellies über den Symcon MQTT Server oder Client betreiben.
- Nicht im Umfang: Shelly Gen1, der Cloud-Zugang der Geräte.

## Unterstützte Plattform

- Symcon: 8.1 oder neuer (`compatibility` in `library.json`), Basisklasse `IPSModuleStrict`.
- Zusätzliche Geräte: Shelly Gen2, Gen3 und Gen4, Shelly BLU über ein BLU-Gateway.

## Einstiegspunkte

| Bereich | Datei oder Verzeichnis | Bedeutung |
| --- | --- | --- |
| Bibliothek | `library.json` | Name, Version, GUID `{4485FA60-6BBA-7775-7824-9716B7308F49}` |
| Basisklasse | `libs/ShellyModuleBase.php` | Abrufe, Variablenliste, Aktionen, gemeinsame Logik aller Instanzen |
| Komponenten | `libs/components.php` | Definitionen der Variablen je Komponente (Typ, Name, Darstellung, Aktion) |
| BLU (BTHome) | `libs/BTHomeObjects.php`, `libs/BTHomeModels.php` | Sensoren, Tasten, Modelltabelle der BLU-Geräte |
| Modelle | `libs/ShellyModels.php` | Modellcode zu Gerätename (nur Anzeige im Konfigurator) |
| Instanzen | `ShellyDevice/`, `ShellyComponent/`, `ShellyBLUDevice/`, `ShellyXT1Device/` | Gerät, Komponente, BLU-Gerät an mehreren Gateways, XT1-Geräte |
| Konfigurator | `ShellyConfigurator/` | Findet Geräte über `announce`, legt Instanzen an |
| Qualität | `.style/` | StylePHP und php-cs-fixer, lokal `symcon-check --strict` |

## Architektur und Datenfluss

Das Modul hängt am Symcon MQTT Server oder Client. Eine Instanz kennt ein Topic (`<Topic>/rpc` für Anfragen, `<Topic>/events/rpc` für Meldungen). Die Variablen entstehen aus `Shelly.GetComponents` und der Auswahl in der Liste "Variablen". Beim Start liest eine Instanz nur den Status ihrer Komponenten (`keys`, versetzt), ein vollständiger Abruf läuft bei neuer Instanz, geänderter Konfiguration oder neuer Version. BLU-Geräte werden über die MAC-Adresse aus mehreren Gateways zu einer Instanz zusammengeführt (Idents mit der Nummer des Haupt-Gateways).

## Öffentliche und persistente Verträge

- Properties: `MQTTTopic`, `VariableList`, bei Komponenten `Component` und `Channel`, bei BLU `BLUAddress` und `AdditionalGateways`.
- Variablen und Idents: `<komponente>_<Kanal>_<feld>` (zum Beispiel `switch_0_output`), BLU `bthomedevice_<N>_…` und `bthomesensor_<Objekt*100+Index>_value`. Idents dürfen sich nicht ändern, sonst gehen Archive und Skripte verloren.
- Öffentliche Methoden: `SHY_callRPCFunction`, `SHY_requestComponentsStatus`, `SHY_KVS…`.
- Datenformate und Caches: Attribute `ReadState` und `BluState` (gemerkter Stand für den schnellen Start), Buffer (flüchtig, nach einem Neustart leer).
- Migrationen: Eine neue Bibliotheksversion löst einmal einen vollständigen Abruf aus.

## Prüfung und Veröffentlichung

- Lokale Prüfungen: `symcon-check --strict` im Modulordner. Die Meldungen zu `tools/` und `.claude/settings.local.json` betreffen lokale, nicht eingecheckte Dateien.
- Notwendige Praxistests: `symcon-dev` mit simulierten Geräten (kleine Node-Skripte als MQTT-Client) und mit echten Geräten am Dev-MQTT-Server.
- Branch-Besonderheiten: Keine, Entwicklung auf Feature-Branches, Merge lokal nach `main`.
- Forum-Thread: https://community.symcon.de/t/modul-shelly-gen2/140756
- Store-Bundle-ID: `schnittcher.info.ips.shellyv2`
- Webseite: veröffentlicht (Seite `shellyv2`, https://deinsmarthome.info/module/shellyv2/)

## Bekannte Risiken und offene Fragen

- Derzeit keine.

## Pflege

Aktualisieren, wenn sich Einstiegspunkte, Datenfluss, öffentliche Verträge oder Abhängigkeiten wesentlich ändern.
