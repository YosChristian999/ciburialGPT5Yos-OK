<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

try {
  $envPath = __DIR__ . '/../../config/env.php'; // -> backend/config/env.php
  if (!file_exists($envPath)) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'env.php not found','path'=>$envPath]); exit;
  }
  require_once $envPath;
  if (!function_exists('env')) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'env() helper missing (cek env.php)']); exit;
  }

  $client = (string) env('MIDTRANS_CLIENT_KEY', '');
  $isProd = filter_var((string) env('MIDTRANS_IS_PRODUCTION','false'), FILTER_VALIDATE_BOOLEAN);

  if ($client === '') {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'MIDTRANS_CLIENT_KEY belum diset di .env']); exit;
  }

  echo json_encode(['ok'=>true,'client_key'=>$client,'is_production'=>$isProd]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'PHP error: '.$e->getMessage()]);
}
