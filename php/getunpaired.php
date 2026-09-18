<?php

//http://stackoverflow.com/questions/18382740/cors-not-working-php
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	exit(0);
}




  require_once __DIR__ . '/db2.inc.php'; //MYSQL//


	$IDoggetto = intval($_GET['IDoggetto']);



	$unpaired = [];


	$MySql = "SELECT COUNT(*) as paired_count FROM paired WHERE IDoggetto1 = $IDoggetto OR IDoggetto2 = $IDoggetto " ;

	$Result = mysqli_query($db, $MySql);
	$res = mysqli_fetch_array($Result,MYSQLI_ASSOC);
	$paired_count = $res['paired_count'];

	if ( $paired_count == 0 ) {


		$MySql = "SELECT IDoggetto, nomeoggetto FROM oggetti WHERE IDoggetto != $IDoggetto  AND
			IDoggetto NOT IN (SELECT IDoggetto1 FROM paired ) AND
			IDoggetto NOT IN (SELECT IDoggetto2 FROM paired ) 	" ;

		$Result = mysqli_query($db, $MySql);
		while ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
			$unpaired[] =  $res;
		}

	} 

  

	$output = [
		"unpaired" => $unpaired
  	];

	header("HTTP/1.1 200 OK");

  	$out = json_encode ($output, JSON_UNESCAPED_UNICODE);
	echo $out;




?>
