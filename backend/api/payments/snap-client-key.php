<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/midtrans.php';

try {
  $isProd = midtrans_is_production();
  $client = $isProd
    ? env('MIDTRANS_CLIENT_KEY_PRODUCTION', '')
    : env('MIDTRANS_CLIENT_KEY_SANDBOX', '');

  if ($client === '') {
    throw new Exception('MIDTRANS_CLIENT_KEY belum diset di .env');
  }

  echo json_encode([
    'ok'            => true,
    'client_key'    => $client,
    'is_production' => $isProd,
    'snap_url'      => midtrans_snap_url(), // otomatis sandbox/prod
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
