<?php
// public_html/backend/config/env.php
function env_load(string $path): void {
  if (!is_file($path)) return;
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (preg_match('/^\s*[#;]/', $line)) continue;
    if (!str_contains($line, '=')) continue;
    [$k,$v] = array_map('trim', explode('=', $line, 2));
    if ($v !== '' && ($v[0] === '"' || $v[0] === "'")) $v = trim($v, "\"'");
    putenv("$k=$v");
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
  }
}
env_load(__DIR__ . '/../.env');

function env(string $key, $default=null) {
  $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
  return ($val !== false && $val !== null && $val !== '') ? $val : $default;
}
