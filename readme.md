# OPTENERGY Backend

### Einleitung
---
Das Backend basiert auf PHP und stellt Ressource der Datenbank über HTTP-Endpunkte bereit. Das verwendete Format ist **JSON**.
Die Basis-URL lautet: `https://optenergy.immotickety.de/api`
GET-Aufrufe liefern Ressourcen entsprechend dem Angegebenen JSON-Schema zurück.
POST-Aufrufe liefern die angelegte Ressource zurück.
PUT-Aufrufe liefern die veränderte Ressource zurück.

### Authorisierung
---
Zur Autorisierung einer Anfrage wird ein **JWT (JSON Web Token)** verwendet. Dieser muss bei jeder Anfrage in dem Header  `Authorization` übergeben werden. 
Ein gültiger Token (Gültigkeit: 60 Minuten) kann über einen GET-Request an den Endpunkt `/user/login` bezogen werden. Alle Endpunkte überprüfen anhand der im Token enthaltenen _userId_ die Zugriffsberechtigungen auf eine Ressource. 
Der Endpunkt erwartet die **Base64-Encodierte** E-Mail und das Passwort durch einen Doppelpunkt getrennt im `Authorization` Header der Anfrage.  Zum BeispieL:
```
Klartext E-Mail: name@itc.de
Klartext Passwort: test
Base64 von name@itc.de:test -> bmFtZUBpdGMuZGU6dGVzdA==
```
Antwort des Endpunktes:
```
{
	"accessToken": JWT
}
```
Bei Fehlender Authorisierung antwortet der Server mit dem Status-Code **401**.

## User
Pfad: `/user/`
### JSON-Schema
```
{
	"UserID": "5",
	"Vorname": "Opt",
	"Nachname": "Energy",
	"Profilbild": "profile.jpg",
	"Telefon": "02351623440",
	"Land": "Deutschland",
	"Email": "test@test.de",
	"Strompreis": 0.27,
	"IstPremium": false,
	"Rechnungsadresse": "Hauptstraße 10",
	"Bankadresse": "An der Sparkasse 1"
}
```
#### GET

GET-Anfrage an: `/user`

#### POST
Erwartet:
```
{
	"Vorname": "Opt",
	"Nachname": "Energy",
	"Profilbild": "profile.jpg",
	"Telefon": "02351623440",
	"Land": "Deutschland",
	"Email": "test@test.de",
	"Strompreis": 0.27,
	"IstPremium": false,
	"Passwort": "dGVzdA==", 
	"Rechnungsadresse": "Hauptstraße 10",
	"Bankadresse": "An der Sparkasse 1"
}
```
Das Passwort muss **Base64-encodiert** sein. 
#### PUT
Erwartet:
```
{
	"Vorname": "Opt",
	"Nachname": "Energy",
	"Profilbild": "profile.jpg",
	"Telefon": "02351623440",
	"Land": "Deutschland",
	"Email": "test@test.de",
	"Strompreis": 0.27,
	"IstPremium": false,
	"Rechnungsadresse": "Hauptstraße 10",
	"Bankadresse": "An der Sparkasse 1"
	"oldPasswort": "dGVzdA==",
	"Passwort": "dGVzdDEy"
}
```
Die Passwörter müssen **Base64-encodiert** sein. Soll das Passwort unverändert bleiben, darf das Attribut "Passwort" nicht existieren.

## Steckdosen
Pfad: `/steckdosen/`
### JSON-Schema
```
{
	"SteckdoseID": "1",
	"Bezeichnung": "Lampe",
	"IstAn": false,
	"UserID": "5",
	"AktivStartzeit": "2023-06-23 10:00:00",
	"AktivEndzeit": "2023-06-23 14:00:00",
	"SteckdosengruppeID": 1,
	"SteckdosengruppeBezeichnung": "Wohnzimmer"
}
```
#### GET

GET-Anfrage an: `/steckdosen/`
Liefert alle Steckdosen des Users

#### POST
Erwartet:
```
{
	"Bezeichnung": "Lampe",
	"IstAn": false,
	"AktivStartzeit": "2023-06-23 10:00:00",
	"AktivEndzeit": "2023-06-23 14:00:00",
	"SteckdosengruppeID": "1"
}
```
Die _UserID_ wird aus dem JWT ausgelsen.
Die Angabe der _SteckdosengruppeID_ ist zwingend notwendig.
#### PUT
Erwartet:
```
{
	"SteckdosenID": 1
	"Bezeichnung": "Lampe",
	"IstAn": false,
	"AktivStartzeit": "2023-06-23 10:00:00",
	"AktivEndzeit": "2023-06-23 14:00:00",
	"SteckdosengruppeID": "1"
}
```
Die _UserID_ wird aus dem JWT ausgelsen.
Die Angabe der _SteckdosengruppeID_ ist zwingend notwendig.
## Steckdosengruppe
Pfad: `/steckdosen/gruppen/`
### JSON-Schema
```
{
	"SteckdosengruppeID": "1",
	"Bezeichnung": "Wohnzimmer",
	"UserID": "5"
}
```
#### GET

GET-Anfrage an: `/steckdosen/gruppen`
Liefert alle Steckdosen des Users

#### POST
Erwartet:
```
{
	"Bezeichnung": "Wohnzimmer",
}
```
Die _UserID_ wird aus dem JWT ausgelsen.

## Sparvorschlag
Pfad: `/sparvorschlaege/`
### JSON-Schema
```
{
	"Sparvorschlag": "Der Vorschlag"
}
```
#### GET

GET-Anfrage an: `/sparvorschlaege/`
Liefert einen zufälligen Sparvorschlag.

## Analyse
Pfad: `/analyse/`
### JSON-Schema
```
{
	"steckdosen": [
		{
			"SteckdoseID": "1",
			"Bezeichnung": "Lampe",
			"IstAn": false,
			"SteckdosengruppeBezeichnung": "Wohnzimmer",
			"Verbrauch": [
					0.03,
					0.03,
					0.04,
					0.01,
					0.03,
					0.04,
					0.03,
					0.02,
					0.03,
					0.04,
					0.75,
					0.59,
					0.61,
					0.62,
					0.61,
					0.61,
					0.64,
					0.63,
					0.66,
					0.6,
					0.75,
					0.71,
					0.7,
					0.72
			]
		}
	],
	"ersparnisInkWh": 0.41,
	"ersparnisInEuro": 0.12
}
```
#### GET

GET-Anfrage an: `/analyse/?days=X`
Liefert eine Analyse für alle Steckdosen des Nutzers.
Der URL-Parameter _days_ gibt an, wie viele Daten das Verbrauchsarray enthält. (1 Tag = 24 Studen = 24 Werte im Array Verbrauch).
Die _UserID_ wird aus dem JWT ausgelsen.

### Letzte Analyse

GET-Anfrage an: `/analyse/last`
```
{
	"LetzteAnalyse": "2023-06-25 14:36:28"
}
```
Gibt den Zeitpunkt der letzten Analyse an.
Die _UserID_ wird aus dem JWT ausgelsen.

## Optimierung
Pfad: `/optimierung/`
### JSON-Schema
```
{
	"steckdosen": [
		{
			"SteckdoseID": "1",
			"Bezeichnung": "Lampe",
			"IstAn": false,
			"UserID": "5",
			"AktivStartzeit": "14:00:00",
			"AktivEndzeit": "03:59:59",
			"SteckdosengruppeID": "1",
			"SteckdosengruppeBezeichnung": "Wohnzimmer"
		}
	],
	"ersparnisInkWh": 2.90,
	"ersparnisInEuro": 0.87,
	"AutoOptimierung": true
}
```
#### GET

GET-Anfrage an: `/optimierung/`
```
{
	"AutoOptimierung": true
}
```
Liefert den Status der Auto-Optimierung.
Die _UserID_ wird aus dem JWT ausgelsen.

#### POST
Erwartet ein Analyse-Objekt:
```
{
	"steckdosen": [
		{
			"SteckdoseID": "1",
			"Bezeichnung": "Lampe",
			"IstAn": false,
			"SteckdosengruppeBezeichnung": "Wohnzimmer",
			"Verbrauch": [
					0.03,
					0.03,
					0.04,
					0.01,
					0.03,
					0.04,
					0.03,
					0.02,
					0.03,
					0.04,
					0.75,
					0.59,
					0.61,
					0.62,
					0.61,
					0.61,
					0.64,
					0.63,
					0.66,
					0.6,
					0.75,
					0.71,
					0.7,
					0.72
			]
		}
	],
	"ersparnisInkWh": 0.41,
	"ersparnisInEuro": 0.12
}
```
Führt die Optimierung durch und liefert ein Optimierungsobjekt zurück.
Die _UserID_ wird aus dem JWT ausgelsen.

#### PUT
Erwartet:
```
{
	"AutoOptimierung": true
}
```
Ändert den Status der AutoOptimierung.
Die _UserID_ wird aus dem JWT ausgelsen.

## Support
Pfad: `/support/`

#### POST
Erwartet:
```
{
	"empfaenger": "dennis.weinrich@gmx.net",
	"betreff": "Hallo",
	"text": "Hallo vom Support"
}
```
Sendet eine Supportanfrage an optenergy@immotickety.de und eine Bestätigung an den angegebenen Empfänger.
Wird als Empfänger ein Leerstring übergeben, so wird die E-Mail Adresse des Users über die  _UserID_ aus dem JWT ermittelt.
