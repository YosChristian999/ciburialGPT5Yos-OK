<?php
// backend/config/env.php
// Loader .env sederhana (ambil dari /ciburial/.env)
function env(string $key, string $default = ''): string {
  static $ENV = null;
  if ($ENV === null) {
    $ENV = [];
    $file = __DIR__ . '/../../.env';
    if (is_file($file)) {
      foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (preg_match('/^\s*#/', $line)) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2) + ['', '']);
        if ($k !== '') $ENV[$k] = trim($v, " \t\n\r\0\x0B\"'");
      }
    }
  }
  return $ENV[$key] ?? $default;
}
