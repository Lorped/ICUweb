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

	include ('messaggi.inc.php');
	require_once __DIR__ . '/db2.inc.php';    // NEW MYSQL //


	$user_id=$_GET['user_id'] ?? 0;
	if ($user_id=="" || $user_id == 0 ) {

		$out = [
			'nomeoggetto' => 'ATTENZIONE',
			'descrizione' => 'oggetto non definito',
			'esito' => [],
			'domanda' => null
		];
		$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
    	echo $output;
		die();
	}
	

	$barcode=$_GET['barcode'];


	// OGGETTO NORMALE //

	$condizioni = [];
	$nomeoggetto= '';
	$descrizione='';
	$esito = [];



	$Mysql8="SELECT * FROM oggetti WHERE barcode='$barcode' ";
	$Result8=mysqli_query($db, $Mysql8);

	$res8=mysqli_fetch_array($Result8);

	if ( mysqli_num_rows($Result8) == 0 ) {
		$out = [
			'nomeoggetto' => 'ATTENZIONE',
			'descrizione' => 'Oggetto non definito',
			'esito' => [],
			'esitoSI' => [],
			'esitoNO' => [],
			'domanda' => null,
			'R1' => null,
			'R2' => null,
		];
		$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
		echo $output;
		die();
	}

	


	$idx=$res8['IDoggetto'];
	$nomeoggetto=$res8['nomeoggetto'];
	$descrizione=$res8['descrizione'];
	$domanda=$res8['domanda'];
	$ifdomanda=$res8['ifdomanda'];
	$R1=$res8['r1'];
	$R2=$res8['r2'];
	$adddisciplina=$res8['adddisciplina'];




	//verifica PAIRED //

	$Mysql9="SELECT * FROM paired WHERE IDoggetto1 = '$idx' or IDoggetto2 = '$idx' ";
	$Result9=mysqli_query($db, $Mysql9);
	if ( $res9=mysqli_fetch_array($Result9) ) { // esiste un oggetto gemello

		//inserisco il log di questo oggetto e cancello i log più vecchi di 2 ore
		$MySql3 = "DELETE FROM `logscanpaired` WHERE DATE_ADD(logscanpaired.datascan, INTERVAL 120 MINUTE ) < NOW() ";
	  	$Result3 = mysqli_query($db, $MySql3);
      	$MySql3 = "INSERT INTO logscanpaired (IDoggetto, user_id, datascan ) VALUES ($idx, $user_id, NOW() )  ON DUPLICATE KEY UPDATE datascan = NOW()";
      	$Result3 = mysqli_query($db, $MySql3);



		if ($res9['IDoggetto1'] == $idx ) {	
			$altrooggetto=$res9['IDoggetto2'];
		} else {
			$altrooggetto=$res9['IDoggetto1'];
		}
		// altrooggetto è stato scansionato da poco ?
		$MySql6 = "SELECT * FROM logscanpaired WHERE
        	IDoggetto = $altrooggetto AND user_id = $user_id AND DATE_ADD(logscanpaired.datascan, INTERVAL 3 MINUTE) > NOW() ";
      	$Result6 = mysqli_query($db, $MySql6);
      	if ( $res6 = mysqli_fetch_array($Result6) ) {
        	// ok paired
			$descpaired = $res9['Paired'];

			$Mysql10="SELECT * FROM oggetti WHERE idoggetto='$altrooggetto' ";
			$Result10=mysqli_query($db, $Mysql10);
			$res10=mysqli_fetch_array($Result10);
			$messaggio = "ha scansionato oggetto ". $res8['nomeoggetto'] ." e il suo gemello ". $res10['nomeoggetto'] .".";
		

			user2master($user_id,$messaggio, $db );

			$Mysql11="SELECT nomepg FROM personaggio WHERE user_id=$user_id";
			if ( $res11=mysqli_fetch_array(mysqli_query($db, $Mysql11)) ) {
					$nomepg=$res11['nomepg'];
				} else {
					$nomepg="NARRAZIONE";
			}
			$xnomepg=mysqli_real_escape_string($db, $nomepg);
			$xmessaggio=mysqli_real_escape_string($db, $messaggio );

			$Mysql12="INSERT INTO messaggi ( idutente, nomepg, Ora, Testo, Destinatario) VALUES ( $user_id, '$xnomepg', NOW(), '$xmessaggio' , 0) ";
			mysqli_query($db, $Mysql12);

			$rigarisp = [
				'motivo' => 'Accoppiamento '.$res8['nomeoggetto'] . " + " . $res10['nomeoggetto']	,
				'descrizione' => $descpaired,
				'sino' => ''
			];
			$esito[] = $rigarisp;

		}
	}




	

	$Mysql="SELECT tipocond, tabcond, valcond, descrX, risp, subskill  FROM oggetti LEFT JOIN cond_oggetti ON oggetti.IDoggetto = cond_oggetti.IDoggetto WHERE barcode='$barcode' ORDER BY cond_oggetti.valcond ASC ";
	$Result=mysqli_query($db, $Mysql);
	if (mysqli_errno($db)) die ( mysqli_errno($db).": ".mysqli_error($db) ."+".$Mysql);

	while ( $res=mysqli_fetch_array($Result)) {
		$condizioni[]=$res;
	}



	foreach ($condizioni as $cond) {


		// CONTROLLO DISCIPLINE

		if ($cond['tipocond'] == 'D' ){
			$ids=$cond['tabcond'];
			$Mysql4="SELECT * FROM discipline left join discipline_main on discipline_main.IDdisciplina=discipline.IDdisciplina
				WHERE discipline.IDdisciplina = $ids AND discipline.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);

			if ( $res4=mysqli_fetch_array($Result4)  ) {		
				if ($res4['livello'] >= $cond['valcond'] ) {
					$rigarisp = [
						'motivo' => $res4['nomedisciplina'],
						'descrizione' => $cond['descrX'],
						'sino' => $cond['risp']
					];
					$esito[] = $rigarisp;	
				}
			}
		}

		// controllo dominio
		if ($cond['tipocond'] == 'Y' ){
			$ids=$cond['tabcond'];
			$Mysql4="SELECT * FROM personaggio left join dominio on dominio.IDdominio=personaggio.IDdominio
				WHERE dominio.IDdominio = $ids AND personaggio.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);

			if ( $res4=mysqli_fetch_array($Result4)  ) {		
					$rigarisp = [
						'motivo' => $res4['nomedominio'],
						'descrizione' => $cond['descrX'],
						'sino' => $cond['risp']
					];
					$esito[] = $rigarisp;	
			}
		}

		// controllo clan
		if ($cond['tipocond'] == 'C' ){
			$ids=$cond['tabcond'];
			$Mysql4="SELECT * FROM personaggio left join clan on clan.IDclan=personaggio.IDclan
				WHERE clan.IDclan = $ids AND personaggio.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);

			if ( $res4=mysqli_fetch_array($Result4)  ) {		
					$rigarisp = [
						'motivo' => $res4['nomeclan'],
						'descrizione' => $cond['descrX'],
						'sino' => $cond['risp']
					];
					$esito[] = $rigarisp;	
			}
		}

		// controllo societa
		if ($cond['tipocond'] == 'X' ){
			$ids=$cond['tabcond'];
			$Mysql4="SELECT * FROM personaggio left join societa on societa.IDsocieta=personaggio.IDsocieta
				WHERE societa.IDsocieta = $ids AND personaggio.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);

			if ( $res4=mysqli_fetch_array($Result4)  ) {		
					$rigarisp = [
						'motivo' => $res4['nomesocieta'],
						'descrizione' => $cond['descrX'],
						'sino' => $cond['risp']
					];
					$esito[] = $rigarisp;	
			}
		}



		// Altri SKILL e attributi

		if ($cond['tipocond'] == 'O' || $cond['tipocond'] == 'A' ){
			$ids=$cond['tabcond'];
			$Mysql4="SELECT * FROM skill left join skill_main on skill_main.IDskill=skill.IDskill
				WHERE skill.IDskill = $ids AND skill.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);


			if ( $res4=mysqli_fetch_array($Result4)  ) {
				
				$rigarisp = [
					'motivo' => $res4['nomeskill'],
					'descrizione' => $cond['descrX'],
					'sino' => $cond['risp']
				];
				$esito[] = $rigarisp;	

			}
		}

	}

	


	// INIZIO SKILL  SPECIFICI


	foreach ($condizioni as $cond) {

		if ($cond['tipocond'] == 'S' ){

		//print_r($cond); 
		//echo "<br><br>";

			$ids=$cond['tabcond'];

			$Mysql4="SELECT * FROM skill left join skill_main on skill_main.IDskill=skill.IDskill
				WHERE skill_main.IDskill = $ids AND skill.user_id = '$user_id' ";
			$Result4=mysqli_query($db, $Mysql4);


			if ( $res4=mysqli_fetch_array($Result4)  ) {
				// HO UN SKILL GENERICO confermato. ESISTE UNA CONDIZIONE Su SKILL SPECIFICO ?
				$esistesoecifico=false;
				for ( $i=0; $i< count($condizioni); $i++ ) {
					
					if ( $condizioni[$i]['tipocond'] == 'SS' && $condizioni[$i]['subskill'] == $ids ) {

						$specifica = $condizioni[$i]['tabcond'];

						$mysql5="SELECT * FROM skill_main 
							left join skill on skill_main.IDskill = skill.IDskill
							WHERE skill_main.IDskill =  $specifica  AND skill.user_id = '$user_id' ";

						$Result5=mysqli_query($db, $mysql5);

						if ( $res5=mysqli_fetch_array($Result5)  ) {
							$esistesoecifico=true;

							// c'è una condizione su skill specifico verificato?
							if ( $res5['livello'] >= $condizioni[$i]['valcond'] ) {
								$rigarisp = [
									'motivo' => $res5['nomeskill'],
									'descrizione' => $condizioni[$i]['descrX'],
									'sino' => $cond['risp']
								];
								$esito[] = $rigarisp;										
							} else {
								// allora inserisco solo il generico perchè ho lo skill specifico troppo basso
								$rigarisp = [
									'motivo' => $res4['nomeskill'],
									'descrizione' => $cond['descrX'],
									'sino' => $cond['risp']
								];
								$esito[] = $rigarisp;	
							}
						} else {
							$esistesoecifico=true;
							// allora inserisco solo il generico perchè non ho lo skill specifico
							$rigarisp = [
								'motivo' => $res4['nomeskill'],
								'descrizione' => $cond['descrX'],
								'sino' => $cond['risp']
							];
							$esito[] = $rigarisp;
						}
					}
				}
				//se alla fine non esiste uno skill specifico verificato allora inserisco il generico
				if ( $esistesoecifico == false ) {
					$rigarisp = [
						'motivo' => $res4['nomeskill'],
						'descrizione' => $cond['descrX'],
						'sino' => $cond['risp']
					];
					$esito[] = $rigarisp;	
				}								

			}
		}
	}

		

	$esitoSI = [];
	$esitoNO = [];

	foreach ($esito as $riga) {
		if ($riga['sino'] === 'S') {
			$esitoSI[] = $riga;
		} elseif ($riga['sino'] === 'N') {
			$esitoNO[] = $riga;
		}
	}
	$esito = array_values(array_filter($esito, function($riga) {
		return $riga['sino'] !== 'S' && $riga['sino'] !== 'N';
	}));




	$mysql = "SELECT * FROM logscanogg WHERE user_id = '$user_id' AND IDoggetto = '$idx' ";
	$result = mysqli_query($db, $mysql);
	if (mysqli_num_rows($result) == 0) {
		$mysql2 = "INSERT INTO logscanogg  (user_id, IDoggetto)
			VALUES ('$user_id', '$idx') ";
		mysqli_query($db, $mysql2);
		foreach ($esito as $riga) {
			$mot = mysqli_real_escape_string($db, $riga['motivo']);
			$descr=mysqli_real_escape_string($db, $riga['descrizione']);
			$mysql3 = "INSERT INTO logscanfull  (user_id, IDoggetto, motivo, descrizione)
				VALUES ('$user_id', '$idx', '$mot', '$descr') ";
			mysqli_query($db, $mysql3);
		}
	} else {
		// quello che può succedere è che devo inserire un "paired" ossia "motivo == Accoppiamento"
		foreach ($esito as $riga) {
			if ( strpos($riga['motivo'], 'Accoppiamento ') === 0 ) {
				// gestisci il caso specifico di "Accoppiamento" se necessario
				$mot = mysqli_real_escape_string($db, $riga['motivo']);
				$descr=mysqli_real_escape_string($db, $riga['descrizione']);
				$mysql3 = "INSERT ignore INTO logscanfull  (user_id, IDoggetto, motivo, descrizione)
					VALUES ('$user_id', '$idx', '$mot', '$descr') ";
				mysqli_query($db, $mysql3);
				$mysql3 = "INSERT ignore INTO logscanfull  (user_id, IDoggetto, motivo, descrizione)
					VALUES ('$user_id', '$altrooggetto', '$mot', '$descr') ";
				mysqli_query($db, $mysql3);
			}
		}
	}

	// gestione della logica per l'effetto della disciplina incrementata

	$refreshEffetti = false;



	if ($adddisciplina != '') {

		$idxdisciplina = $adddisciplina;
		$mysqlnome = "SELECT nomedisciplina from discipline_main where  IDdisciplina = '$idxdisciplina' ";
		$resultnome = mysqli_query($db, $mysqlnome);
		$res = mysqli_fetch_array($resultnome);
		$nomedisciplina = $res['nomedisciplina'];



		$rigarisp = [
			'motivo' => 'Disciplina Incrementata',
			'descrizione' => $nomedisciplina . ' +1',
			'sino' => ''
		];
		$esito[] = $rigarisp;

		$myssql= "select * from effetti where user_id = '$user_id' and IDoggetto = '$idx' ";
		$result = mysqli_query($db, $myssql);
		if ($row = mysqli_fetch_assoc($result)) {
			// già presente nella tabella effetti non faccio nulla
		} else {

			$mysql3 = "INSERT INTO effetti  (user_id, IDoggetto)
				VALUES ('$user_id', '$idx') ";
			mysqli_query($db, $mysql3);

			$mysql3 = "select * from discipline where user_id = '$user_id' and IDdisciplina	 = '$idxdisciplina' ";
			$result = mysqli_query($db, $mysql3);
			if ($row = mysqli_fetch_assoc($result)) {
				$mysqladd = "UPDATE discipline SET livello = livello + 1 WHERE user_id = '$user_id' AND IDdisciplina = '$idxdisciplina' ";
				mysqli_query($db, $mysqladd);
			} else {
				$mysql3 = "INSERT INTO discipline  (user_id, IDdisciplina, livello)
					VALUES ('$user_id', '$idxdisciplina', 1) ";
				mysqli_query($db, $mysql3);
			}

			$mysqllog = "INSERT INTO logscanfull  (user_id, IDoggetto, motivo, descrizione)
				VALUES ('$user_id', '$idx', 'Disciplina Incrementata', '$nomedisciplina +1') ";
			mysqli_query($db, $mysqllog);


			$messaggio = "Disciplina Incrementata: $nomedisciplina +1 tramite oggetto $nomeoggetto";
			user2master($user_id,$messaggio, $db );
			$myssqlx="SELECT nomepg from personaggio where user_id = $user_id";
			$resultx = mysqli_query($db, $myssqlx);
			$rowx = mysqli_fetch_assoc($resultx);
			$xnomepg = mysqli_real_escape_string($db, $rowx['nomepg']);
			$xmessaggio = mysqli_real_escape_string($db, $messaggio);
			$Mysql12="INSERT INTO messaggi ( idutente, nomepg, Ora, Testo, Destinatario) VALUES ( $user_id, '$xnomepg', NOW(), '$xmessaggio' , 0) ";
			mysqli_query($db, $Mysql12);

			$refreshEffetti = true;
		}

	}



	$out = [
		'nomeoggetto' => $nomeoggetto,
		'descrizione' => $descrizione,
		'esito' => $esito,
		'esitoSI' => $esitoSI,
		'esitoNO' => $esitoNO,
		'domanda' => $domanda,
		'R1' => $R1,
		'R2' => $R2,
		'refreshEffetti' => $refreshEffetti,
	];

	header("HTTP/1.1 200 OK");
	$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
    echo $output;


?>