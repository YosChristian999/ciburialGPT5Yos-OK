<?php
require __DIR__ . '/../config/env.php';
header('Content-Type: application/json');
echo json_encode([
  'ok'    => true,
  'APP_ENV' => env('APP_ENV','-'),
  'db_host' => env('DB_HOST','-'),
  'time' => date('c')
]);
