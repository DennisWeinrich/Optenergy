<?php 
require_once __DIR__ . '/SparvorschlagGenerator.php';

if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
	$svg = new SparvorschlagGenerator();
	
	$sparvorschlag['Sparvorschlag'] = $svg->getSparvorschlag();
	
	echo json_encode($sparvorschlag);
} else {
	http_response_code(400);
	die();
}

?>