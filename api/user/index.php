<?php 
require_once __DIR__ . '/Nutzerverwaltung.php';

$nv = new Nutzerverwaltung();
$data = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		echo json_encode($nv->getUser());
		break;
	case 'POST':
		echo json_encode($nv->postUser($data));
		break;
	case 'PUT':
		echo json_encode($nv->putUser($data));
		break;
	case 'DELETE':
		//not implemented
		break;
}


?>