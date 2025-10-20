<?php
// backend/api/_bootstrap.php
declare(strict_types=1);
session_start();

header('Content-Type: application/json');
// Jika front-end & back-end satu domain, Anda bisa hapus CORS.
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

function json_ok($data = []) {
  http_response_code(200);
  echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_SLASHES);
  exit;
}
function json_fail($message, $code = 400) {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_SLASHES);
  exit;
}
function me() {
  if (!isset($_SESSION['user_id'])) return null;
  return [
    'id' => intval($_SESSION['user_id']),
    'role' => $_SESSION['user_role'] ?? 'user',
    'name' => $_SESSION['user_name'] ?? ''
  ];
}
function require_login() {
  $u = me();
  if (!$u) json_fail('Unauthorized', 401);
  return $u;
}
function require_role(string ...$roles) {
  $u = require_login();
  if (!in_array($u['role'], $roles, true)) json_fail('Forbidden', 403);
  return $u;
}
