<?php
require_once __DIR__ . '/../_bootstrap.php';
$u = me();
json_ok(['user'=>$u]);
header('Content-Type: application/json');
session_start();
echo json_encode(['ok'=> isset($_SESSION['uid'])]);
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'forbidden']);
  exit;
}
