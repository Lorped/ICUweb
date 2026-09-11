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
 

	require_once __DIR__ . '/db2.inc.php';  // NEW MYSQL //

	$nomeutente = $_GET['nomeutente'] ?? '';
	$password = $_GET['password'] ?? '';

	$nomeutente = mysqli_real_escape_string($db, $nomeutente);
	$password = mysqli_real_escape_string($db, $password);

	$MySql = "SELECT user_id FROM personaggio WHERE nomeutente = '$nomeutente' AND password = '$password'  ";

	$Result = mysqli_query($db, $MySql);
	if ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$user_id = $res['user_id'];
		$out = [
			'user_id' => $user_id
		];
		echo json_encode($out);
	} else {    
		header("HTTP/1.1 401 Unauthorized");
	}

?>
