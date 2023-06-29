<?php 
require_once __DIR__ . '/../DBVerwalter.php';
require_once __DIR__ . '/../ErsparnisBerechner.php';
require_once __DIR__ . '/../steckdosen/Steckdosenverwaltung.php';
use ReallySimpleJWT\Token;

class Analysator {
	
	private $pdo;
	private $sv;
	private $eb;
	
	public function __construct($dbVerwalter, $sv, $eb) {
		$this->pdo = $dbVerwalter->getConnection();
		$this->sv = $sv;
		$this->eb = $eb;
	}
	
	public function analysiere($days) {
		$steckdosen = $this->sv->getSteckdosen();

		foreach ($steckdosen as &$sd) {
			unset($sd['AktivStartzeit']);
			unset($sd['AktivEndzeit']);
			unset($sd['UserID']);
			
			$standByDuration = rand(6, 18);
			$standByBegin = rand(0, 23);
			//erhöhe Wahrscheinlichkeit, dass standby nachts/morgens beginnt
			$standByBegin = $standByBegin - rand(0, $standByBegin);
			$standByEnd = ($standByBegin + $standByDuration) % 24;
			$standByVerbrauch = rand(2, 9);
			$normalVerbrauch = rand(20, 100);
			
			for($i = 0; $i<24*$days; $i++){
				if($standByBegin > $standByEnd){
					if(($i % 24) >= $standByEnd && ($i % 24) < $standByBegin){
						//standby Verbrauch erfinden
						$verbrauch[$i] = rand($standByVerbrauch-2, $standByVerbrauch+2) / 100;
					} else {
						//normalen Verbrauch erfinden
						$verbrauch[$i] = rand($normalVerbrauch-10, $normalVerbrauch+10) / 100;
					}
				} else {
					if(($i % 24) >= $standByBegin && ($i % 24) < $standByEnd){
						//standby Verbrauch erfinden
						$verbrauch[$i] = rand($standByVerbrauch-2, $standByVerbrauch+2) / 100;
					} else {
						//normalen Verbrauch erfinden
						$verbrauch[$i] = rand($normalVerbrauch-10, $normalVerbrauch+10) / 100;
					}
				}
			
			}
			$sd['Verbrauch'] = $verbrauch;
		}
		
		$analyse['steckdosen'] = $steckdosen;
		$analyse['ersparnisInkWh'] = $this->eb->berechneErsparnis($analyse);
		$analyse['ersparnisInEuro'] = $this->eb->berechneErsparnisInEuro($analyse['ersparnisInkWh']);
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		$date = date('Y-m-d H:i:s');
		
		//speichere Analyse Zeitpunkt
		if(isset($this->lastAnalyse()['LetzteAnalyse'])){
			$statement = $this->pdo->prepare("UPDATE Analyse SET LetzteAnalyse = :letzteAnalyse WHERE UserID = :userId;");
		} else {
			$statement = $this->pdo->prepare("INSERT INTO Analyse VALUES (:userId, :letzteAnalyse);");
		}
		$statement->bindParam(':userId', $userId);
		$statement->bindParam(':letzteAnalyse', $date);
		$statement->execute();
		
		return $analyse;
	}
	
	public function lastAnalyse(){
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("SELECT LetzteAnalyse from Analyse WHERE UserID = :userId");
		$statement->bindParam(':userId', $userId);
		$statement->execute();
		return $statement->fetch(PDO::FETCH_ASSOC);
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