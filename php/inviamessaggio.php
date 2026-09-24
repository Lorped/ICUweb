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

require_once __DIR__ . '/db2.inc.php';
require_once __DIR__ . '/messaggi.inc.php';


	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);

	$messaggio = $request -> messaggio;
	$destinatari = $request -> destinatari;


	foreach ($destinatari as $user_id) {
		master2user($user_id, $messaggio ,  $db);


		$messaggio = mysqli_real_escape_string($db, $messaggio);


		$MySql = "INSERT INTO messaggi (ora , destinatario, testo) VALUES (NOW(), $user_id, '$messaggio')";
		$Result=mysqli_query($db, $MySql);
	
	}

	


	$out = $destinatari;

	header("HTTP/1.1 200 OK");
	echo json_encode ($out, JSON_UNESCAPED_UNICODE);
?>

