<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/../../config/env.php';

$isProd = filter_var(env('MIDTRANS_IS_PRODUCTION','false'), FILTER_VALIDATE_BOOLEAN);
$clientSandbox = env('MIDTRANS_CLIENT_KEY','');
$clientProd    = env('MIDTRANS_CLIENT_KEY_PROD', env('MIDTRANS_CLIENT_KEY',''));

echo json_encode([
  'ok'        => ($isProd ? $clientProd : $clientSandbox) !== '',
  'client_key'=> $isProd ? $clientProd : $clientSandbox,
  'snap_url'  => $isProd ? 'https://app.midtrans.com/snap/snap.js'
                         : 'https://app.sandbox.midtrans.com/snap/snap.js',
]);
