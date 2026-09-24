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

	$logscan = [];

	$MySql = "SELECT oggetti.IDoggetto, logscanogg.user_id, nomepg, datascan, oggetti.nomeoggetto, oggetti.descrizione FROM `logscanogg` 
		LEFT JOIN personaggio ON logscanogg.user_id = personaggio.user_id
		LEFT JOIN oggetti  ON logscanogg.IDoggetto = oggetti.IDoggetto";
	$Result = mysqli_query($db, $MySql);

	while ( $res = mysqli_fetch_array ($Result,MYSQLI_ASSOC) ) {

		$res['paired_nomeoggetto'] = '';

		$idx = $res['IDoggetto'];
		$user_id = $res['user_id'];
		$datascan = $res['datascan'];

		$Mysql9="SELECT * FROM paired WHERE IDoggetto1 = '$idx' or IDoggetto2 = '$idx' ";
		$Result9=mysqli_query($db, $Mysql9);
			if ( $res9=mysqli_fetch_array($Result9) ) {
				if ($res9['IDoggetto1'] == $idx ) {	
				$altrooggetto=$res9['IDoggetto2'];
			} else {
				$altrooggetto=$res9['IDoggetto1'];
			}
			// altrooggetto è stato scansionato da poco ?
			$MySql6 = "SELECT * FROM logscanpaired WHERE
				IDoggetto = $altrooggetto AND user_id = $user_id AND ABS(TIMESTAMPDIFF(MINUTE, logscanpaired.datascan, '$datascan')) <= 3 ";
			$Result6 = mysqli_query($db, $MySql6);
			if ( $res6 = mysqli_fetch_array($Result6) ) {
				// paired object has been scanned recently
				$mysql10 = "SELECT oggetti.nomeoggetto FROM logscanpaired 
					LEFT JOIN oggetti ON logscanpaired.IDoggetto = oggetti.IDoggetto
					WHERE logscanpaired.IDoggetto = $altrooggetto AND logscanpaired.user_id = $user_id AND ABS(TIMESTAMPDIFF(MINUTE, logscanpaired.datascan, '$datascan')) <= 3 ";
				$Result10 = mysqli_query($db, $mysql10);
				if ( $res10 = mysqli_fetch_array($Result10) ) {
					$res['paired_nomeoggetto'] = $res10['nomeoggetto'];
				}
			}

		}

		$logscan[] = $res;
	}

	$out = [
		'logscan' => $logscan
	];


header("HTTP/1.1 200 OK");
echo json_encode ($out, JSON_UNESCAPED_UNICODE);

?>
