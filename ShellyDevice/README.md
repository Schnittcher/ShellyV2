# ShellyDevice
Diese Instanz legt alle passenden Variablen für das Shelly an.

### Inhaltsverzeichnis

- [ShellyDevice](#shellydevice)
    - [Inhaltsverzeichnis](#inhaltsverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Funktionen](#2-funktionen)
  - [3. Spenden](#3-spenden)
  - [4. Lizenz](#4-lizenz)


## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
MQTT Topic | Hier wird das Topic des Geräts hinterlegt.
Debug: Fehlende Idents     | Mit diesem Schalter können im Debug mehr Daten angezeigt werden, dies kann nützlich sein, wenn Variablen fehlen und das Debug im Forum gepostet werden soll.
Variablen | In dieser Liste kann ausgewählt werden, ob die Variablen angezeigt werden sollen, ebenfalls gibt es die Möglichkeit die Funktion "Zeroing" zu aktivieren. Durch das Aktivieren der Funktion wird die Variable zurückgesetzt, wenn das Gerät offline ist. Welche Variablen es je Komponente gibt (Name, Ident, Typ, Einheit, bedienbar), steht in [Komponenten.md](../Komponenten.md). Komponenten, die Fehler melden, haben zusätzlich die Variablen "Fehler" (die Fehlercodes als Text) und "Störung" (Ja/Nein, true sobald ein Fehler vorliegt).
Shelly Model | Hier wird das Model des Shellies eingetragen. Wird unteranderem dazu genutzt um ggf. Ausnahmen zu behandeln, zum Beispiel bei "Powered by Shelly" Geräten.
Stream-Objekte automatisch anlegen | Nur bei einer Shelly Camera: Legt die Stream-Objekte (RTSP, Hauptstream und zweiter Stream) automatisch an, sobald die IP-Adresse des Geräts bekannt ist.
Stream-Benutzer | Nur bei einer Shelly Camera, nur nötig, wenn am Gerät die Authentifizierung aktiv ist (der Benutzer ist laut Weboberfläche der Kamera "admin").
Stream-Passwort | Das zugehörige Passwort, wird in die Adresse der Stream-Objekte übernommen.

## 2. Funktionen

`RequestAction($VariablenID, $Value);`
Mit dieser Funktion können alle Aktionen einer Variable ausgelöst werden.

**Beispiel:**

Variable ID Status 1 = 12345

```php
RequestAction(12345, true);  //Status 1 Einschalten;
RequestAction(12345, false); //Status 1 Ausschalten;
```

`void SHY_callRPCFunction(integer $InstanzID, string $method, array $params);`
Mit dieser Funktion können als RPC Funktionen von den Shellies ausgeführt werden.
Die RPC Funktionen können in der API Beschreibung der Shellies gefunden werden: https://shelly-api-docs.shelly.cloud/gen2/

**Beispiel:**
```php
$params = ['id' => 0, 'on' => true]; //Parameter um den Kanal 0 des Gerätes einzuschalten
SHY_callRPCFunction(integer 12345, string 'Switch.Set', $params);
```

`void SHY_requestComponentsStatus(integer $InstanzID);`
Liest alle Komponenten und Dienste des Geräts mit Status und Konfiguration (`Shelly.GetComponents`, seitenweise), legt die Variablen an und aktualisiert die Werte. Die Funktion wird automatisch beim Speichern der Instanz und beim Verbinden mit dem MQTT-Server/-Client aufgerufen, im Formular gibt es dafür den Button "Komponenten auslesen". Sie kann auch genutzt werden, um alle Werte manuell abzufragen, z. B. nach Änderungen am Gerät.

Beispiel:
`SHY_requestComponentsStatus(12345);`

`string SHY_CreateCameraStreams(integer $InstanzID);`
Nur bei einer Shelly Camera: Legt die Stream-Objekte an bzw. aktualisiert ihre Adresse (z. B. nach geänderten Zugangsdaten), im Formular der Button "Stream-Objekte aktualisieren". Gibt einen Hinweistext zurück.

### KVS (Key-Value-Store des Geräts)
Mit diesen Funktionen kann der persistente Speicher für Schlüssel/Wert-Paare auf dem Shelly gelesen und beschrieben werden (z. B. zum Austausch von Werten mit einem Shelly-Skript). Der Wert ist ein beliebiger JSON-Wert (Text, Zahl, Boolean, Array). Grenzen laut Shelly: Schlüssel höchstens 42 Zeichen, Wert höchstens 253 Zeichen, höchstens 50 Schlüssel. Die Funktionen warten bis zu 5 Sekunden auf die Antwort des Geräts.

`bool SHY_KVSSet(integer $InstanzID, string $Key, mixed $Value);`
Schlüssel anlegen oder ändern. Gibt true zurück, wenn das Gerät den Wert übernommen hat.

`bool SHY_KVSSetIfUnchanged(integer $InstanzID, string $Key, mixed $Value, string $Etag);`
Wie SHY_KVSSet, ändert aber nur, wenn der Eintrag seit dem Lesen nicht anderweitig geändert wurde (`etag` aus SHY_KVSGet).

`string SHY_KVSGet(integer $InstanzID, string $Key);`
Wert als JSON `{"etag": "...", "value": ...}`, leer wenn der Schlüssel nicht existiert oder das Gerät nicht antwortet.

`bool SHY_KVSDelete(integer $InstanzID, string $Key);`
Schlüssel löschen.

`string SHY_KVSList(integer $InstanzID, string $Match);`
Alle zum Muster passenden Schlüssel als JSON `{"keys": {"schluessel": {"etag": "..."}}, "rev": 12}`. `*` steht für beliebig viele Zeichen, `?` für ein Zeichen, ein Komma trennt Muster; `*` liefert alle Schlüssel.

`string SHY_KVSGetMany(integer $InstanzID, string $Match);`
Wie SHY_KVSList, aber mit den Werten: `{"items": {"schluessel": {"etag": "...", "value": ...}}, "rev": 12}`.

**Beispiel:**
```php
SHY_KVSSet(12345, 'sollwert', 21.5);
$antwort = json_decode(SHY_KVSGet(12345, 'sollwert'), true); // ['etag' => '...', 'value' => 21.5]
$alle = json_decode(SHY_KVSGetMany(12345, '*'), true);
SHY_KVSDelete(12345, 'sollwert');
```

## 3. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 4. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)