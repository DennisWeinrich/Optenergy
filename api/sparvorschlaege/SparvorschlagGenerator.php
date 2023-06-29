<?php 
use ReallySimpleJWT\Token;

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Methods: OPTIONS, GET, PUT, POST, DELETE");
	header("Access-Control-Allow-Headers: Authorization, content-type");
	die();
}

class Sparvorschlaggenerator {
	
	private $sparvorschlaege;
	
	public function getSparvorschlag() {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}	
		
		return $this->sparvorschlaege[rand(0, count($this->sparvorschlaege))];
	}
	
	public function __construct() {
		$this->sparvorschlaege = [
				"Schalte elektronische Geräte komplett aus statt sie im Standby-Modus zu lassen.",
				"Verwende LED-Lampen, die weniger Energie verbrauchen als herkömmliche Glühbirnen.",
				"Nutze natürliche Beleuchtung durch das Öffnen von Vorhängen und Jalousien.",
				"Stelle die Temperatur des Thermostats um ein Grad niedriger im Winter und um ein Grad höher im Sommer ein.",
				"Schalte das Licht aus, wenn du einen Raum verlässt.",
				"Verwende energieeffiziente Haushaltsgeräte mit hoher Energieeffizienzklasse.",
				"Dusche statt zu baden, um Wasser und Energie zu sparen.",
				"Verwende Zeitschaltuhren für elektrische Geräte, um sie automatisch auszuschalten.",
				"Nutze Sonnenenergie durch die Installation von Solarpaneelen auf dem Dach.",
				"Vermeide den Einsatz von elektrischen Heizgeräten und ziehe dich wärmer an.",
				"Entfroste regelmäßig den Kühlschrank und halte die Dichtungen sauber, um den Energieverbrauch zu senken.",
				"Verwende Energiesparmodus auf Computern und Laptops, wenn sie nicht verwendet werden.",
				"Wasche Kleidung mit kaltem Wasser, um den Energieverbrauch der Waschmaschine zu reduzieren.",
				"Lüfte Räume durch kurzes Stoßlüften anstelle von dauerhaft geöffneten Fenstern.",
				"Verwende einen Wäscheständer anstelle eines Wäschetrockners, wenn möglich.",
				"Schalte nicht benötigte elektrische Geräte wie Fernseher, Computer oder Spielekonsolen aus.",
				"Isoliere dein Zuhause, um den Wärmeverlust im Winter und den Wärmeeintritt im Sommer zu reduzieren.",
				"Verwende energiesparende Kochmethoden wie Induktionsherd oder Schnellkochtopf.",
				"Plane deine Aktivitäten, um mehrere Aufgaben mit einem eingeschalteten Gerät zu erledigen, anstatt sie einzeln auszuführen.",
				"Installiere Bewegungssensoren oder Timer für Außenbeleuchtung, um sie nur bei Bedarf einzuschalten.",
				"Schalte den Kühlschrank rechtzeitig vor dem Ende des Abtauzyklus ab, um die Kühlleistung zu optimieren.",
				"Benutze einen Deckenventilator, um die Raumtemperatur um einige Grad zu senken.",
				"Verwende Steckerleisten mit Ein-/Aus-Schalter, um den Standby-Verbrauch mehrerer Geräte zu reduzieren.",
				"Nutze den Energiesparmodus deiner elektronischen Geräte, wenn du sie nicht aktiv verwendest.",
				"Dämme die Fenster mit Vorhängen oder Rollläden, um Wärme im Winter zu speichern und Hitze im Sommer abzuhalten.",
				"Verwende einen Topfdeckel beim Kochen, um die Garzeit zu verkürzen und Energie zu sparen.",
				"Enteise den Gefrierschrank regelmäßig, um die Energieeffizienz zu verbessern.",
				"Schalte den Bildschirmschoner deines Computers aus, da er unnötig Energie verbraucht.",
				"Verwende den Sparmodus für den Drucker, um Tinten- und Energieverbrauch zu reduzieren.",
				"Nutze Tageslicht zur Beleuchtung von Arbeitsbereichen und reduziere den Einsatz von künstlichem Licht.",
				"Stelle sicher, dass deine Hausisolierung gut ist, um Wärme- und Kühlverluste zu minimieren.",
				"Installiere einen programmierbares Thermostat, um die Temperatur automatisch anzupassen.",
				"Verwende einen Standby-Killer, um den Standby-Verbrauch von elektrischen Geräten zu eliminieren.",
				"Überprüfe und verbessere regelmäßig die Dichtungen an Türen und Fenstern, um Energieverluste zu minimieren."
		];
	}
	
	//besser: eigenes Modul für Token Handling (Erstellung und Verifizierung)
	private function validateJWT(){
		if (!isset(apache_request_headers()['Authorization']) || apache_request_headers()['Authorization'] == null) {
			return false;
		}
		
		$jwt = apache_request_headers()['Authorization'];
		$secret = 'geheim';
		$result = Token::validate($jwt, $secret);
		
		$payload = Token::getPayload($jwt);
		if($result && Token::validateExpiration($jwt) && $payload['nbf'] < time()){
			return true;
		}
		return false;
	}
	
}

?>