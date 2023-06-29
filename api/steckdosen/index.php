<?php 
require_once __DIR__ . '/Steckdosenverwaltung.php';

$sv = new Steckdosenverwaltung();
$data = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		echo json_encode($sv->getSteckdosen());
		break;
	case 'POST':
		echo json_encode($sv->postSteckdose($data));
		break;
	case 'PUT':
		echo json_encode($sv->putSteckdose($data));
		break;
	case 'DELETE':
		$sv->removeSteckdose($_GET['SteckdoseID']);
		break;
}

?>