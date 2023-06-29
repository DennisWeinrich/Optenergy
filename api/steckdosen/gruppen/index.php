<?php 
require_once __DIR__ . '/../Steckdosenverwaltung.php';

$sv = new Steckdosenverwaltung();
$data = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		echo json_encode($sv->getSteckdosengruppen());
		break;
	case 'POST':
		echo json_encode($sv->postSteckdosengruppe($data));
		break;
	case 'PUT':
		//not implemented
		break;
	case 'DELETE':
		$sv->removeSteckdosengruppe($_GET['SteckdosengruppeID']);
		break;
}

?>