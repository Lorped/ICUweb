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


	$user_id=$_GET['user_id'] ?? 0;
	if ($user_id=="" || $user_id == 0 ) {
		die();
	}
	
	$out = [];

	$mysql = "SELECT logscanogg.IDoggetto, nomeoggetto, DATE_FORMAT(datascan, '%d/%m %H:%i') as datascan, descrizione 
		FROM logscanogg 
		left join oggetti on oggetti.IDoggetto = logscanogg.IDoggetto
		WHERE user_id = '$user_id' 
		order by datascan desc ";
	$result = mysqli_query($db, $mysql);
	while($res=mysqli_fetch_array($result)) {		
		$idx=$res['IDoggetto'];
		$datascan=$res['datascan'];
		$mysql2 = "SELECT * FROM oggetti WHERE IDoggetto = '$idx' ";
		$result2 = mysqli_query($db, $mysql2);
		$res2=mysqli_fetch_array($result2);
		$nomeoggetto=$res2['nomeoggetto'];
		$descrizione=$res2['descrizione'];

		$esito = [];
		$mysql3 = "SELECT motivo, descrizione FROM logscanfull WHERE user_id = '$user_id' AND IDoggetto = '$idx' ";
		$result3 = mysqli_query($db, $mysql3);
		while ( $res3=mysqli_fetch_array($result3,MYSQLI_ASSOC) ) {
			$esito[] = $res3;
		}

		$xx = [
			'nomeoggetto' => $nomeoggetto,
			'descrizione' => $descrizione,
			'datascan' => $datascan,
			'esito' => $esito
		];
		$out[] = $xx;

	}

	

	$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
    echo $output;


?>