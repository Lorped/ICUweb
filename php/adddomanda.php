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

$IDoggetto = $request -> IDoggetto;
$domanda = $request -> domanda;
$r1 = $request -> r1;
$r2 = $request -> r2;



$domanda = mysqli_real_escape_string($db, $domanda);
$r1 = mysqli_real_escape_string($db, $r1);
$r2 = mysqli_real_escape_string($db, $r2);

//$nome = "lorenzo";
//$password = "lor11ped";
//$postdata = 1;


if ( isset($postdata) && $IDoggetto != ""  ) {

  $MySql = "UPDATE oggetti
    SET ifdomanda = 1 , domanda = '$domanda' , r1 = '$r1' , r2 = '$r2'
    WHERE IDoggetto = $IDoggetto";

  $Result = mysqli_query($db, $MySql);

  //die($MySql);

  header("HTTP/1.1 200 OK");

  $out = "OK";
  $out = json_encode ($out, JSON_UNESCAPED_UNICODE);
  echo $out;


} else {
	header("HTTP/1.1 401 Unauthorized");
}


?>
