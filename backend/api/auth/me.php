<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'ok'=>true,
  'auth'=> isset($_SESSION['uid']),
  'user'=> isset($_SESSION['uid']) ? [
    'id'=>$_SESSION['uid'],
    'name'=>$_SESSION['name'] ?? 'User',
    'role'=>$_SESSION['role'] ?? 'user'
  ] : null
]);
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'forbidden']);
  exit;
}
