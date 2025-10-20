<?php
session_start(); session_unset(); session_destroy();
header('Content-Type: application/json');
echo json_encode(['ok'=>true]);
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'forbidden']);
  exit;
}
