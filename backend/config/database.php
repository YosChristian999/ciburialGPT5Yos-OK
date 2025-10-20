<?php
require_once __DIR__ . '/env.php';

class DB {
  private static ?PDO $pdo = null;

  public static function pdo(): PDO {
    if (self::$pdo === null) {
      $dsn  = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST','127.0.0.1'),
        env('DB_NAME','ciburial')
      );
      $user = env('DB_USER','root');
      $pass = env('DB_PASS','');

      $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
      ];
      self::$pdo = new PDO($dsn, $user, $pass, $opt);
    }
    return self::$pdo;
  }
}
