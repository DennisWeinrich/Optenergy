<?php 
require_once __DIR__ . '/../vendor/autoload.php';

//Cross Origin Ressource Sharing
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Methods: OPTIONS, GET, PUT, POST, DELETE");
	header("Access-Control-Allow-Headers: Authorization, content-type");
	die();
}

class DBVerwalter {
	
	public function getConnection(): PDO {

		try {
			$conString = 'mysql:host=[host];port=3306;dbname=[dbname];charset=utf8';
			
			return new PDO(
					$conString,
					"[user]",
					"[passwort]",
					[
							PDO::ATTR_PERSISTENT=> true,
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
					]
					);
		} catch (PDOException $e) {
			print "Error: " . $e ->getMessage();
			die();
		}
	}
	
}

?>