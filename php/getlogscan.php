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

		$Mysql9 = "SELECT CASE WHEN IDoggetto1 = $idx THEN IDoggetto2 ELSE IDoggetto1 END AS altroID
			FROM paired
			WHERE IDoggetto1 = $idx OR IDoggetto2 = $idx
			LIMIT 1";
		$Result9 = mysqli_query($db, $Mysql9);
		if ( $res9 = mysqli_fetch_array($Result9, MYSQLI_ASSOC) ) {
			$altrooggetto = $res9['altroID'];
			$mysql10 = "SELECT oggetti.nomeoggetto FROM logscanpaired AS scansione_corrente
				INNER JOIN logscanpaired AS scansione_altro
				ON scansione_altro.IDoggetto = $altrooggetto
				AND scansione_altro.user_id = scansione_corrente.user_id
				AND scansione_altro.datascan BETWEEN DATE_SUB(scansione_corrente.datascan, INTERVAL 3 MINUTE)
				AND DATE_ADD(scansione_corrente.datascan, INTERVAL 3 MINUTE)
				INNER JOIN oggetti ON scansione_altro.IDoggetto = oggetti.IDoggetto
				WHERE scansione_corrente.IDoggetto = $idx AND scansione_corrente.user_id = $user_id
				LIMIT 1";
			$Result10 = mysqli_query($db, $mysql10);
			if ( $res10 = mysqli_fetch_array($Result10, MYSQLI_ASSOC) ) {
				$res['paired_nomeoggetto'] = $res10['nomeoggetto'];
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
