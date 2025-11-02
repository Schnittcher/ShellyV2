# ShellyComponent
Mit dieser Instanz kann eine Komponente einzeln angelegt werden.
Idealerweise wird dies über den Konfigurator getan, dann wird die gesamte Konfiguration der Instanz korrekt ausgefüllt.    

## Inhaltverzeichnis
- [ShellyComponent](#shellycomponent)
  - [Inhaltverzeichnis](#inhaltverzeichnis)
  - [1. Konfiguration](#1-konfiguration)
  - [2. Funktionen](#2-funktionen)
  - [3. Spenden](#3-spenden)
  - [4. Lizenz](#4-lizenz)

## 1. Konfiguration

Feld | Beschreibung
------------ | ----------------
MQTT Topic | Hier wird das Topic des Geräte hinterlegt-
Komponente      | Hier wird die Komponente hinterlegt, für welche diese Instanz gelten soll. zum Beispiel switch
Kanal      | Hier wird der Kanal hinterlegt, für welchen diese Instanz gelten soll. zum Beispiel 0 wenn es sich um die Komponente switch:0 handelt.
Debug: Fehlende Idents     | Mit diesem Schalter können im Debug mehr Daten angezeigt werden, dies kann nützlich sein, wenn Variablen fehlen und das Debug im Forum gepostet werden soll.
Variablen | In dieser Liste kann ausgewählt werden, ob die Variablen angezeigt werden sollen, ebenfalls gibt es die Möglichkeit die Funktion "Zeroing" zu aktivieren. Durch das Aktivieren der Funktion wird die Variable zurückgesetzt, wenn das Gerät offline ist.

## 2. Funktionen

`RequestAction($VariablenID, $Value);`
Mit dieser Funktion können alle Aktionen einer Variable ausgelöst werden.

**Beispiel:**

Variable ID Status 1 = 12345

```php
RequestAction(12345, true);  //Status 1 Einschalten;
RequestAction(12345, false); //Status 1 Ausschalten;
```

`boolean SHY_callRPCFunction(integer $InstanzID, string $method, array $params);`
Mit dieser Funktion können als RPC Funktionen von den Shellies ausgeführt werden.
Die RPC Funktionen können in der API Beschreibung der Shellies gefunden werden: https://shelly-api-docs.shelly.cloud/gen2/

**Beispiel:**
```php
$params = ['id' => 0, 'on' => true]; //Parameter um den Kanal 0 des Gerätes einzuschalten
SHY_callRPCFunction(integer 12345, string 'Switch.Set', $params);
```

`boolean SHY_getComponents(integer $InstanzID);`
Ruft alle Components / Services ab und legt dazu die Variablen an, diese Funktion wird automatisch beim Speichern der Instanz aufgerufen.
Diese Funktion kann ebenfalls dazu genutzt werden, um den Status der Variablen manuell abzufragen.

Beispiel:
`SHY_getComponents(12345);`

## 3. Spenden
Dieses Modul ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK4JRP87XLSHW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a> <a href="https://www.amazon.de/hz/wishlist/ls/3JVWED9SZMDPK?ref_=wl_share" target="_blank">Amazon Wunschzettel</a>

## 4. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)