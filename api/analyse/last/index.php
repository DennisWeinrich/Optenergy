<?php 
require_once __DIR__ . '/../Analysator.php';

if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
	$a = new Analysator();
	
	$la = $a->lastAnalyse();
	if(!isset($la['LetzteAnalyse'])){
		$la['LetzteAnalyse'] = "";
	}
	echo json_encode($la);
} else {
	http_response_code(400);
	die();
}

?>