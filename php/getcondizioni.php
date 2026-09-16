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


	require_once __DIR__ . '/db2.inc.php';    // NEW MYSQL //



	$societa = [];
	$mysql = "SELECT * FROM societa";
	$result = mysqli_query($db, $mysql);
	while($res=mysqli_fetch_array($result,MYSQLI_ASSOC)) {		
		$societa[] = $res;
	}

	$clan = [];
	$mysql = "SELECT * FROM clan";
	$result = mysqli_query($db, $mysql);
	while($res=mysqli_fetch_array($result,MYSQLI_ASSOC)) {		
		$clan[] = $res;
	}

	$domini = [];
	$mysql = "SELECT * FROM dominio";
	$result = mysqli_query($db, $mysql);
	while($res=mysqli_fetch_array($result,MYSQLI_ASSOC)) {		
		$domini[] = $res;
	}

	/*** skill **/

	$skill = [];
	$MySql = "SELECT IDskill, nomeskill FROM skill_main 
		WHERE (tipologia=0 ) and subskill=0 ORDER BY nomeskill" ;
	$Result = mysqli_query($db, $MySql);
	while ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$xidskill = $res['IDskill'];
		$xnomeskill = $res['nomeskill'];

		$subskill = [];

		$Mysql2 = "SELECT IDskill, nomeskill , 0 as livello FROM skill_main
			where subskill = $xidskill ORDER BY nomeskill" ;
		$Result2 = mysqli_query($db, $Mysql2);
		while ( $res2 = mysqli_fetch_array($Result2,MYSQLI_ASSOC)   ) {	
			$subskill[] =  $res2;
		}
		$skill[] = [
			'IDskill' => $xidskill,
			'nomeskill' => $xnomeskill,
			'livello' => 0,
			'subskill' => $subskill
		];	
	}

	$otherskill = [];
	$MySql = "SELECT IDskill, nomeskill , 0 as livello FROM skill_main 
		WHERE (tipologia=1 ) ORDER BY nomeskill" ;
	$Result = mysqli_query($db, $MySql);
	while ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$otherskill[] = $res;
	}
	$attributi = [];
	$MySql = "SELECT IDskill, nomeskill , 0 as livello FROM skill_main 
		WHERE (tipologia=2 ) ORDER BY nomeskill" ;
	$Result = mysqli_query($db, $MySql);
	while ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$attributi[] = $res;
	}

	/*** discipline */
	$discipline = [];
	$MySql = "SELECT IDdisciplina, nomedisciplina , 0 as livello FROM discipline_main";
	$Result = mysqli_query($db, $MySql);
	while ( $res = mysqli_fetch_array($Result,MYSQLI_ASSOC)   ) {
		$discipline[] = $res;
	}


	$out = [
		'societa' => $societa,
		'clan' => $clan,
		'domini' => $domini,
		'skill' => $skill,
		'otherskill' => $otherskill,
		'attributi' => $attributi,
		'discipline' => $discipline
	];

	$output = json_encode ($out, JSON_UNESCAPED_UNICODE);
    echo $output;


?>