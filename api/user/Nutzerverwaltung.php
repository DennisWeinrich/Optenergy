<?php 
require_once __DIR__ . '/../DBVerwalter.php';
use ReallySimpleJWT\Token;

class Nutzerverwaltung {
	
	private $pdo;
	
	public function __construct() {
		$dbVerwalter = new DBVerwalter();
		$this->pdo = $pdo = $dbVerwalter->getConnection();
	}
	
	public function getUser() {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}
		
		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		$statement = $this->pdo->prepare("select * from User where UserID = :id");
		$statement -> bindParam(':id', $userId);
		$statement->execute();
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		
		unset($user['Passwort']);
		$user['IstPremium'] = $user['IstPremium'] == '0' ? false:true;
		return $user;
	} 
	
	public function postUser($data) {
		//prüfe ob User existiert
		$statement = $this->pdo->prepare("select * from User where Email = :email");
		$statement -> bindParam(':email', $data['Email']);
		$statement->execute();
		
		$user = $statement->fetch(PDO::FETCH_ASSOC);

		if(isset($user['UserID']) || str_contains($data['Passwort'], ':')) {
			http_response_code(400);
			die();
		}
		
		//User einfügen
		//hash passwort
		$pw = password_hash(base64_decode($data['Passwort']), PASSWORD_DEFAULT);
	    $sp = doubleval(str_replace(',', ".", $data['Strompreis']));
		
		$statement = $this->pdo->prepare("
    			INSERT INTO User
    				(Vorname, Nachname, Passwort, Profilbild, Telefon, Land, Email, Strompreis, IstPremium, Rechnungsadresse, Bankadresse)
    				VALUES (:vorname, :nachname, :passwort, :profilbild, :telefon, :land, :email, :strompreis, :istpremium, :rechnungsadresse, :bankadresse)
				");
		
		$statement->bindParam(':vorname', $data['Vorname']);
		$statement->bindParam(':nachname', $data['Nachname']);
		$statement->bindParam(':passwort', $pw);
		$statement->bindParam(':profilbild', $data['Profilbild']);
		$statement->bindParam(':telefon', $data['Telefon']);
		$statement->bindParam(':land', $data['Land']);
		$statement->bindParam(':email', $data['Email']);
		$statement->bindParam(':strompreis', $sp);
		$statement->bindParam(':istpremium', $data['IstPremium'], PDO::PARAM_BOOL);
		$statement->bindParam(':rechnungsadresse', $data['Rechnungsadresse']);
		$statement->bindParam(':bankadresse', $data['Bankadresse']);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		

		$data['UserID'] = $this->pdo->lastInsertId();
		$autoOpt = false;
		$statement = $this->pdo->prepare("INSERT INTO Optimierung VALUES (:userId, :opt)");
		$statement->bindParam(':userId', $data['UserID']);
		$statement->bindParam(':opt', $autoOpt, PDO::PARAM_BOOL);
		$statement->execute();
		return $data;
	}
	
	public function putUser($data) {
		if(!$this->validateJWT()){
			http_response_code(401);
			die();
		}

		//hole UserId aus dem Token
		$userId = Token::getPayload(apache_request_headers()['Authorization'])['uid'];
		
		if(isset($data['Passwort'])){
			//hole User aus db
			$statement = $this->pdo->prepare("select * from User where UserID = :userId");
			$statement -> bindParam(':userId', $userId);
			$statement->execute();
			$user = $statement->fetch(PDO::FETCH_ASSOC);
			
			if ($user !== false && password_verify(base64_decode($data['oldPasswort']), $user['Passwort'])){
				$pw = password_hash(base64_decode($data['Passwort']), PASSWORD_DEFAULT);
				$statement = $pdo->prepare("
    				UPDATE User
    					SET Passwort = :pw
    					WHERE UserID = :userid
					");
				$statement->bindParam(':passwort', $pw);
				$statement->bindParam(':userid', $userId);
				$statement->execute();
				
			} else {
				http_response_code(401);
			}
			
		}
 	
        $sp = doubleval(str_replace(',', ".", $data['Strompreis']));
		
		$statement = $this->pdo->prepare("
    		UPDATE User
    			SET Vorname = :vorname, Nachname = :nachname, Profilbild = :profilbild, Telefon = :telefon, Land = :land, Email = :email, Strompreis = :strompreis, IstPremium = :istpremium, Rechnungsadresse = :rechnungsadresse, Bankadresse = :bankadresse
    			WHERE UserID = :userid
			");
		
		$statement->bindParam(':vorname', $data['Vorname']);
		$statement->bindParam(':nachname', $data['Nachname']);
		$statement->bindParam(':profilbild', $data['Profilbild']);
		$statement->bindParam(':telefon', $data['Telefon']);
		$statement->bindParam(':land', $data['Land']);
		$statement->bindParam(':email', $data['Email']);
		$statement->bindParam(':strompreis', $sp);
		$statement->bindParam(':istpremium', $data['IstPremium'], PDO::PARAM_BOOL);
		$statement->bindParam(':rechnungsadresse', $data['Rechnungsadresse']);
		$statement->bindParam(':bankadresse', $data['Bankadresse']);
		$statement->bindParam(':userid', $userId);
		$statement->execute();
		
		if(!$statement) {
			http_response_code(500);
			die();
		}
		
		return $data;
	}
	
	public function login($email, $passwort) {
		//hole User aus db
		$statement = $this->pdo->prepare("select * from User where Email = :email");
		$statement -> bindParam(':email', $email);
		$statement->execute();
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		
		if ($user !== false && password_verify($passwort, $user['Passwort'])){
			//user ist ok, erstelle Token
			$payload = [
					'iss' => 'OPTENERGY',
					'uid' => $user['UserID'],
					'iat' => time(),
					'nbf' => time()-1,
					'exp' => time() + 3600,
			];
			
			$jwt['accessToken'] = Token::customPayload($payload, 'geheim');
			return $jwt;
		} else {
			http_response_code(401);
		}
	}
	
	//besser: eigenes Modul für Token Handling (Erstellung und Verifizierung)
	private function validateJWT(){
		if (!isset(apache_request_headers()['Authorization']) 
				|| apache_request_headers()['Authorization'] == null) {
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
