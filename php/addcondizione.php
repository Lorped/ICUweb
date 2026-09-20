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

require_once __DIR__ . '/db2.inc.php'; //MYSQLI//


$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$idoggetto = $request -> IDoggetto;
$tipocond = $request -> tipocond;
$tabcond = $request -> tabcond;
$valcond = $request -> valcond;
$descrX = $request -> descrX;
$risp = $request -> risp;

if ( $tipocond == 'SS'){
  $subskill = "1";
} else {
  $subskill = "0";
}


$descrX = mysqli_real_escape_string($db, $descrX);

//$nome = "lorenzo";
//$password = "lor11ped";
//$postdata = 1;


if ( isset($postdata) && $idoggetto != ""  ) {

  if ($risp == 'x'){
    $MySql = "INSERT INTO cond_oggetti
    (idoggetto, tipocond, tabcond, valcond, descrX, subskill)
    VALUES
    ($idoggetto, '$tipocond', $tabcond, $valcond, '$descrX', $subskill) ";
  } else {
    $MySql = "INSERT INTO cond_oggetti
    (idoggetto, tipocond, tabcond, valcond, descrX, risp, subskill)
    VALUES
    ($idoggetto, '$tipocond', $tabcond, $valcond, '$descrX', '$risp', '$subskill') ";
  }
  $Result = mysqli_query($db, $MySql);


  $MySql = "SELECT idcondizione from cond_oggetti where idcondizione = last_insert_id() ";
  $Result = mysqli_query($db, $MySql);
  $res = mysqli_fetch_array ($Result);

  //die($MySql);

  header("HTTP/1.1 200 OK");

  $out = $res['idcondizione'];
  $out = json_encode ($out, JSON_UNESCAPED_UNICODE);
  echo $out;


} else {
	header("HTTP/1.1 401 Unauthorized");
}


?>
