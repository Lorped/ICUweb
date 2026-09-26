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

	
	$messaggi = [];

	$MySql = "SELECT ID, messaggi.nomepg, Ora, Testo, personaggio.nomepg as Nomedestinatario FROM `messaggi` 
		LEFT JOIN personaggio ON personaggio.user_id = messaggi.Destinatario 
		ORDER BY ora DESC;";
	$Result = mysqli_query($db, $MySql);

	while ( $res = mysqli_fetch_array ($Result,MYSQLI_ASSOC) ) {

		if ($res['nomepg'] == '') {
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
