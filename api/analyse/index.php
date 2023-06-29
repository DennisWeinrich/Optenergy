<?php 
require_once __DIR__ . '/Analysator.php';
require_once __DIR__ . '/../DBVerwalter.php';
require_once __DIR__ . '/../ErsparnisBerechner.php';
require_once __DIR__ . '/../steckdosen/Steckdosenverwaltung.php';

if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
	$dbVerwalter = new DBVerwalter();
	$sv = new Steckdosenverwaltung();
	$eb = new ErsparnisBerechner();
	$a = new Analysator($dbVerwalter, $sv, $eb);
	
	echo json_encode($a->analysiere($_GET['days']));
} else {
	http_response_code(400);
	die();
}


?>