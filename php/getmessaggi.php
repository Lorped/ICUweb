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

require_once __DIR__ . '/db2.inc.php';  //MYSQLI //

	$user_id = $_GET['user_id'] ;

	
	$messaggi = [];

	$MySql = "SELECT * FROM `messaggi` WHERE idutente = $user_id or Destinatario = $user_id ORDER BY ora DESC";
	$Result = mysqli_query($db, $MySql);

	while ( $res = mysqli_fetch_array ($Result,MYSQLI_ASSOC) ) {

		if ($res['idutente'] == 0) {
			$res['nomepg'] = 'NARRAZIONE';
		}

		$messaggi[] = $res;
	}

	$out = [
		'messaggi' => $messaggi
	];


header("HTTP/1.1 200 OK");
echo json_encode ($out, JSON_UNESCAPED_UNICODE);

?>
