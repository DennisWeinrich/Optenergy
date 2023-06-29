<?php 
require_once __DIR__ . '/Support.php';

$data = json_decode(file_get_contents('php://input'), true);

if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$s = new Support();
	$s->sendMail($data['empfaenger'], $data['betreff'], $data['text']);
} else {
	http_response_code(400);
	die();
}

?>