<?php 
require_once __DIR__ . '/Optimierer.php';

$data = json_decode(file_get_contents('php://input'), true);
$opt = new Optimierer();

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		$status = $opt->getOptimierungsStatus();
		$status['AutoOptimierung'] = $status['AutoOptimierung'] == 0 ? false : true;
		echo json_encode($status);
		break;
	case 'POST':
		echo json_encode($opt->optimiere($data));
		break;
	case 'PUT':
		echo json_encode($opt->putOptimierung($data));
		break;
	case 'DELETE':
		//not implemented
		break;
}


?>