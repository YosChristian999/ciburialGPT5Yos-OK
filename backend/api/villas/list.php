<?php
require_once __DIR__ . '/../../config/bootstrap.php';

try {
  $pdo = DB::pdo();

  // Pastikan kolom ada di DB: id, nama_villa, deskripsi, harga_per_malam, cover_url, lokasi, kapasitas_maksimal
  $sql = "SELECT id, nama_villa, deskripsi, harga_per_malam, cover_url, lokasi, kapasitas_maksimal
          FROM villas
          WHERE status = 'tersedia'
          ORDER BY id ASC";
  $rows = $pdo->query($sql)->fetchAll();

  echo json_encode(['ok'=>true, 'villas'=>$rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
