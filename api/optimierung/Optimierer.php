<?php 
require_once __DIR__ . '/../DBVerwalter.php';
require_once __DIR__ . '/../ErsparnisBerechner.php';
require_once __DIR__ . '/../steckdosen/Steckdosenverwaltung.php';
use ReallySimpleJWT\Token;

class Optimierer {
	
	private $pdo;
	private $sv;
	private $eb;
	
	public function __construct() {
		$dbVerwalter = new DBVerwalter();
		$this->pdo = $pdo = $dbVerwalter->getConnection();
		$this->sv = new Steckdosenverwaltung();
		$this->eb = new ErsparnisBerechner();
	}
	
	public function optimiere($data) {
		$steckdosen = $this->sv->getSteckdosen();

		foreach ($data['steckdosen'] as $i=>&$sd) {
			//erste 24 Elemente
			$verbrauch = array_slice($sd['Verbrauch'], 0, 24);
			
			//finde neue Start- und Endzeit
			$start = "00:00:00";
			$end = "23:59:59";
			foreach($verbrauch as $index=>&$v) {
				if($v > 0.1){
					//ermögliche negative Modulo Werte
					$ii = (($index-1) % 24 + 24) % 24;
					if($verbrauch[$ii] <= 0.1){
						$start = $index . ":00:00";
					} 
					if($verbrauch[($index+1) % 24] <= 0.1){
						$end = $index . ":59:59";
					} 
				}
			}
			
			//speichere Zeiten in DB
			$sdToSave = $steckdosen[$i];
			$sdToSave['AktivStartzeit'] = $start;
			$sdToSave['AktivEndzeit'] = $end;
			$this->sv->putSteckdose($sdToSave);
		}
		
		$opt['steckdosen'] = $this->sv->getSteckdosen();
		$opt['ersparnisInkWh'] = $this->eb->berechneErsparnis($data);
		$opt['ersparnisInEuro'] = $this->eb->berechneErsparnisInEuro($opt['ersparnisInkWh']);
		$opt['AutoOptimierung'] = $this->getOptimierungsStatus()['AutoOptimierung'] == 0 ? false : true;
		return $opt;
	}
	
	public function getOptimierungsStatus() {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("SELECT AutoOptimierung from Optimierung WHERE UserID = :userId");
		$statement->bindParam(':userId', $userId);
		$statement->execute();
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
	
	public function putOptimierung($data){
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("UPDATE Optimierung SET AutoOptimierung = :ao WHERE UserID = :userId");
		$statement->bindParam(':ao', $data['AutoOptimierung'], PDO::PARAM_BOOL);
		$statement->bindParam(':userId', $userId);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		
		return $data;
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