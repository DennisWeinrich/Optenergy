<?php 
require_once __DIR__ . '/../DBVerwalter.php';
use ReallySimpleJWT\Token;

class Steckdosenverwaltung {
	
	private $pdo;
	
	public function __construct() {
		$dbVerwalter = new DBVerwalter();
		$this->pdo = $pdo = $dbVerwalter->getConnection();
	}
	
	public function getSteckdosen() {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("SELECT
											SteckdoseID, s.Bezeichnung as Bezeichnung, IstAn, s.UserID as UserID, AktivStartzeit, AktivEndzeit,
											s.SteckdosengruppeID as SteckdosengruppeID, sg.Bezeichnung as SteckdosengruppeBezeichnung from Steckdose s
										  JOIN Steckdosengruppe sg on s.SteckdosengruppeID = sg.SteckdosengruppeID
										  WHERE s.UserID = :id");
		$statement -> bindParam(':id', $userId);
		$statement->execute();
		$sd = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($sd as &$steckdose) {
			if($steckdose['AktivStartzeit'] != null && $steckdose['AktivEndzeit'] != null){
				$date1 = DateTime::createFromFormat('H:i:s', $steckdose['AktivStartzeit']);
				$date2 = DateTime::createFromFormat('H:i:s', $steckdose['AktivEndzeit']);
				$date3 = DateTime::createFromFormat('H:i:s', date('H:i:s'));
				
				if($date1 > $date2){
					if($date3 > $date1 || $date3 < $date2) {
						$steckdose['IstAn'] = true;
						continue;
					}
				} else {
					if($date3 > $date1 && $date3 < $date2) {
						$steckdose['IstAn'] = true;
						continue;
					}
				}
				$steckdose['IstAn'] = false;
			} else {
				$steckdose['IstAn'] = $steckdose['IstAn'] == 0 ? false:true;
			}
		}
		
		return $sd;
	} 
	
	public function getSteckdosengruppen() {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("select * from Steckdosengruppe where UserID = :id");
		$statement -> bindParam(':id', $userId);
		$statement->execute();
		$sdg = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		return $sdg;
	} 
	
	public function postSteckdose($data) {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}

		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		if(!isset($data['SteckdosengruppeID']) || $data['SteckdosengruppeID'] == null){
			http_response_code(400);
			die();
		}
		
		$statement = $this->pdo->prepare("
    			INSERT INTO Steckdose
    				(Bezeichnung, IstAn, UserID, AktivStartzeit, AktivEndzeit, SteckdosengruppeID)
   					 VALUES (:bezeichnung, :istan, :userid, :aktivstartzeit, :aktivendzeit, :steckdosengruppeid)
					");
		
		$statement->bindParam(':bezeichnung', $data['Bezeichnung']);
		$statement->bindParam(':istan', $data['IstAn'], PDO::PARAM_BOOL);
		$statement->bindParam(':userid', $userId);
		$statement->bindParam(':aktivstartzeit', $data['AktivStartzeit']);
		$statement->bindParam(':aktivendzeit', $data['AktivEndzeit']);
		$statement->bindParam(':steckdosengruppeid', $data['SteckdosengruppeID']);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		
		$data['SteckdosenID'] = $this->pdo->lastInsertId();
		return $data;
	}
	
	public function postSteckdosengruppe($data) {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("
    			INSERT INTO Steckdosengruppe
    				(Bezeichnung, UserID)
   					 VALUES (:bezeichnung, :userid)
					");
		
		$statement->bindParam(':bezeichnung', $data['Bezeichnung']);
		$statement->bindParam(':userid', $userId);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		
		$data['SteckdosengruppeID'] = $this->pdo->lastInsertId();
		return $data;
	}
	
	public function putSteckdose($data){
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		if(!isset($data['SteckdosengruppeID']) || $data['SteckdosengruppeID'] == null){
			http_response_code(400);
			die();
		}
		
		$statement = $this->pdo->prepare("
    			UPDATE Steckdose
    				SET Bezeichnung = :bezeichnung, IstAn = :istan, AktivStartzeit = :aktivstartzeit, AktivEndzeit = :aktivendzeit, SteckdosengruppeID = :steckdosengruppeid
    				WHERE SteckdoseID = :steckdoseid AND UserID = :userid
					");
		
		$statement->bindParam(':steckdoseid', $data['SteckdoseID']);
		$statement->bindParam(':bezeichnung', $data['Bezeichnung']);
		$statement->bindParam(':istan', $data['IstAn'], PDO::PARAM_BOOL);
		$statement->bindParam(':userid', $userId);
		$statement->bindParam(':aktivstartzeit', $data['AktivStartzeit']);
		$statement->bindParam(':aktivendzeit', $data['AktivEndzeit']);
		$statement->bindParam(':steckdosengruppeid', $data['SteckdosengruppeID']);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		
		return $data;
	}
	
	public function removeSteckdose($id) {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("
    			DELETE FROM Steckdose
    				WHERE SteckdoseID = :steckdoseid AND UserID = :userid
					");
		$statement->bindParam(':steckdoseid', $id);
		$statement->bindParam(':userid', $userId);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		} else {
			http_response_code(200);
		}
	}
	
	public function removeSteckdosengruppe($id) {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("
    			DELETE FROM Steckdosengruppe
    				WHERE SteckdosengruppeID = :steckdosengruppeid AND UserID = :userid
					");
		$statement->bindParam(':steckdosengruppeid', $id);
		$statement->bindParam(':userid', $userId);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		} else {
			http_response_code(200);
		}
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
