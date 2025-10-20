<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$pass  = trim($_POST['password'] ?? '');

if($email==='' || $pass===''){ echo json_encode(['ok'=>false,'error'=>'Lengkapi email & password']); exit; }

try{
  $pdo = DB::pdo();
  $s = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email=? LIMIT 1");
  $s->execute([$email]);
  $u = $s->fetch();
  if(!$u || !password_verify($pass, $u['password'])){
    echo json_encode(['ok'=>false,'error'=>'Email atau password salah']); exit;
  }
  $_SESSION['uid'] = $u['id'];
  $_SESSION['name'] = $u['name'];
  echo json_encode(['ok'=>true]);
}catch(Throwable $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'forbidden']);
  exit;
}
