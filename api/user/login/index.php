<?php 
require_once __DIR__ . '/../Nutzerverwaltung.php';

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset(apache_request_headers()['Authorization']) && apache_request_headers()['Authorization'] != null) {
	$nv = new Nutzerverwaltung();
	
	//hole base64 codierte Email und Passwort (mit : getrennt) aus dem Auth Header
	$auth = base64_decode(apache_request_headers()['Authorization']);
	
	if(!$auth){
		http_response_code(400);
		die();
	}
	
	$auth = explode(':', $auth);

	if(count($auth) != 2){
		http_response_code(400);
		die();
	}
	
	//rufe Login der Nutzerverwaltung auf
	echo json_encode($nv->login($auth[0], $auth[1]));
} else {
	http_response_code(401);
	die();
}


?>