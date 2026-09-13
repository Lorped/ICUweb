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

	$user_id = $_GET['user_id'] ?? '';


	$MySql = "SELECT user_id , nomeplayer, nomepg, clan.IDclan, clan.nomeclan, societa.IDsocieta, societa.nomesocieta, dominio.IDdominio, dominio.nomedominio FROM personaggio 
		LEFT JOIN societa ON personaggio.IDsocieta = societa.IDsocieta
		LEFT JOIN dominio ON personaggio.IDdominio = dominio.IDdominio
		LEFT JOIN clan ON personaggio.IDclan = clan.IDclan
		WHERE user_id = '$user_id'  ";

	$Result = mysqli_query($db, $MySql);
	if ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$out = $res;

		$MySqlOtherskills = "SELECT skill.IDskill, nomeskill, livello FROM skill
			LEFT JOIN skill_main ON skill.IDskill = skill_main.IDskill
			WHERE user_id = '$user_id' and tipologia = 1 ";
		$ResultOtherskills = mysqli_query($db, $MySqlOtherskills);
		$Otherskills = [];
		while ($resOtherskill = mysqli_fetch_array($ResultOtherskills, MYSQLI_ASSOC)) {
			$Otherskills[] = $resOtherskill;
		}
		$out['otherskills'] = $Otherskills;


/*********************    SKILL  */
		$skills = [];

		$MySqlSkill = "SELECT skill.IDskill, nomeskill, livello  FROM skill
			LEFT JOIN skill_main ON skill_main.IDskill=skill.IDskill
			WHERE skill.user_id = '$user_id' and subskill = 0 and tipologia = 0
			order by nomeskill";

		$ResultSkill = mysqli_query($db, $MySqlSkill);
		while ( $resSkill = mysqli_fetch_array($ResultSkill,MYSQLI_ASSOC)   ) {
		
			$idx= $resSkill['IDskill'];
			$nomeskill = $resSkill['nomeskill'];
			$livello = $resSkill['livello'];

			$subskills = [];

			$MySqlSubskill = "SELECT skill.IDskill, nomeskill, livello from skill
			left join skill_main ON skill_main.IDskill=skill.IDskill
				where skill.user_id = '$user_id' and subskill = $idx"; 
		
			$ResultSubskill = mysqli_query($db, $MySqlSubskill);
			while ( $resSubskill = mysqli_fetch_array($ResultSubskill,MYSQLI_ASSOC) ) {
				$subskills[] = $resSubskill;
			}

			$skills [] = [
				'IDskill' => $idx,
				'nomeskill' => $nomeskill,
				'livello' => $livello,
				'subskills' => $subskills
			]; 
		
		}

		$out['skills'] = $skills;



		header("HTTP/1.1 200 OK");
		echo json_encode($out);
	} else {    
		header("HTTP/1.1 404 Not Found");
	}

?>
