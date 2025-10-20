<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/env.php';

$client = env('MIDTRANS_CLIENT_KEY', '');
$isProd = filter_var(env('MIDTRANS_IS_PRODUCTION', 'false'), FILTER_VALIDATE_BOOLEAN);

if ($client === '') {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'MIDTRANS_CLIENT_KEY belum diset di .env']);
  exit;
}

$snap_url = $isProd
  ? 'https://app.midtrans.com/snap/snap.js'
  : 'https://app.sandbox.midtrans.com/snap/snap.js';

echo json_encode([
  'ok'            => true,
  'client_key'    => $client,
  'is_production' => $isProd,
  'snap_url'      => $snap_url
]);
