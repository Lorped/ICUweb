<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  exit(0);
}

require_once __DIR__ . '/db2.inc.php';

$request = json_decode(file_get_contents('php://input'));
if (!is_array($request) || count($request) === 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Nessun oggetto selezionato']);
  exit;
}

mysqli_query($db, 'CREATE TABLE IF NOT EXISTS prestampa (
  idoggetto INT NOT NULL PRIMARY KEY,
  quantita INT NOT NULL
)');
mysqli_query($db, 'DELETE FROM prestampa');

$statement = mysqli_prepare($db, 'INSERT INTO prestampa (idoggetto, quantita) VALUES (?, ?)');
foreach ($request as $riga) {
  $idoggetto = filter_var($riga->IDoggetto ?? null, FILTER_VALIDATE_INT);
  $quantita = filter_var($riga->quantita ?? null, FILTER_VALIDATE_INT);
  if ($idoggetto === false || $quantita === false || $quantita < 1 || $quantita > 10) {
    continue;
  }
  mysqli_stmt_bind_param($statement, 'ii', $idoggetto, $quantita);
  mysqli_stmt_execute($statement);
}
mysqli_stmt_close($statement);

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
