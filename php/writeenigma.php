<?php
	header("Access-Control-Allow-Origin: *");

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


	header('Content-Type: text/html; charset=utf-8');

	
	require_once __DIR__ . '/db2.inc.php';    // NEW MYSQL //

	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	$user_id= $request -> user_id;
	$IDoggetto= $request -> IDoggetto;

	$mysql3 = "SELECT * FROM oggetti WHERE IDoggetto = '$IDoggetto'";
	$result3 = mysqli_query($db, $mysql3);
	$riga3 = mysqli_fetch_assoc($result3);
	$premio = $riga3['premio'];
	
	

	$mot = "Enigma";
	$descr=$premio;

	$mysql = "SELECT * FROM logscanfull WHERE user_id = '$user_id' AND IDoggetto = '$IDoggetto' AND motivo = 'Enigma'";
	$result = mysqli_query($db, $mysql);
	if ( $res = mysqli_fetch_array($result) ) {
		// Record exists, do nothing
	} else {
		// Record does not exist, insert it
		$mysql3 = "INSERT INTO logscanfull  (user_id, IDoggetto, motivo, descrizione)
			VALUES ('$user_id', '$IDoggetto', '$mot', '$descr') ";
		mysqli_query($db, $mysql3);
	}
	
	
	$out = "OK";

	header("HTTP/1.1 200 OK");
	$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
    echo $output;


?>