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



	$oggetti = [];

	$MySql = "SELECT IDoggetto, barcode, nomeoggetto, descrizione, fissomobile, ifdomanda, domanda, r1, r2, nomedisciplina as adddisciplina 
	FROM oggetti 
	left join discipline_main on oggetti.adddisciplina = discipline_main.IDdisciplina 
	order by IDoggetto ";
	$Result = mysqli_query($db, $MySql);

	while ( $res = mysqli_fetch_array ($Result,MYSQLI_ASSOC) ) {

		$idoggetto = $res['IDoggetto'];

		$MySql2 = "SELECT * from cond_oggetti WHERE cond_oggetti.IDoggetto = $idoggetto ";
		$Result2 = mysqli_query($db, $MySql2);

		$condizioni = [];
		$condizioni2 = [];
		while ( $res2 = mysqli_fetch_array ($Result2,MYSQLI_ASSOC) ) {
			$tipocond = $res2['tipocond'];

			switch ( $tipocond ) {
				case 'S':
				case 'SS':
				case 'A':
				case 'O':					
					$ids=$res2['tabcond'];
					$Mysqlx = "SELECT nomeskill FROM skill_main WHERE IDskill = $ids";
					$Resultx = mysqli_query($db, $Mysqlx);
					$resx = mysqli_fetch_array($Resultx);
					$cond = $resx['nomeskill'];
					break;
				case 'D':
					$ids=$res2['tabcond'];
					$Mysqlx="SELECT nomedisciplina FROM discipline_main WHERE IDdisciplina = $ids";
					$Resultx = mysqli_query($db, $Mysqlx);
					$resx = mysqli_fetch_array($Resultx);
					$cond = $resx['nomedisciplina'];
					break;
				case 'X':
					$ids=$res2['tabcond'];
					$Mysqlx="SELECT nomesocieta FROM societa WHERE IDsocieta = $ids";
					$Resultx = mysqli_query($db, $Mysqlx);
					$resx = mysqli_fetch_array($Resultx);
					$cond = $resx['nomesocieta'];
					break;
				case 'Y':
					$ids=$res2['tabcond'];
					$Mysqlx="SELECT nomedominio FROM dominio WHERE IDdominio = $ids";
					$Resultx = mysqli_query($db, $Mysqlx);
					$resx = mysqli_fetch_array($Resultx);
					$cond = $resx['nomedominio'];
					break;
				case 'C':
					$ids=$res2['tabcond'];
					$Mysqlx="SELECT nomeclan FROM clan WHERE IDclan = $ids";
					$Resultx = mysqli_query($db, $Mysqlx);
					$resx = mysqli_fetch_array($Resultx);
					$cond = $resx['nomeclan'];
					break;											
			}

			$res2['tipocond'] = $cond;

			if ( $res2['risp'] != '') {
				$condizioni2[] = $res2;
			} else {
				$condizioni[] = $res2;
			}



		}
		$ids=0;
		$descpaired = '';
		$nomepaired = '';
		$Mysqly = "SELECT * FROM paired where IDoggetto1 = $idoggetto or IDoggetto2 = $idoggetto ";
		$Resulty = mysqli_query($db, $Mysqly);
		while ( $resy = mysqli_fetch_array ($Resulty,MYSQLI_ASSOC) ) {
			if ( $resy['IDoggetto1'] == $idoggetto ) {
				$ids = $resy['IDoggetto2'];
			} else {
				$ids = $resy['IDoggetto1'];
			}
			$descpaired = $resy['Paired'];
		}

		$Mysqlx = "SELECT nomeoggetto FROM oggetti WHERE idoggetto = $ids ";
		$Resultx = mysqli_query($db, $Mysqlx);
		$resx = mysqli_fetch_array($Resultx);
		$nomepaired = $resx['nomeoggetto'];
		

		$fulloggetto = $res;
		$fulloggetto['condizioni'] = $condizioni;
		$fulloggetto['condizioni2'] = $condizioni2;
		$fulloggetto['paired'] = [
			'idpaired' => $ids,
			'nomepaired' => $nomepaired,
			'descpaired' => $descpaired
		];

		$oggetti[] = $fulloggetto;

	}


	$out = [
		'oggetti' => $oggetti
	];

	//print_r( $out);
	//die();
	


header("HTTP/1.1 200 OK");
echo json_encode ($out, JSON_UNESCAPED_UNICODE);

?>
