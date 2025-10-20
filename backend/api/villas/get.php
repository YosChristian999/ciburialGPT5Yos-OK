<?php
// backend/api/villas/get.php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

function pdo() { return DB::pdo(); }

// Ubah URL publik /ciburial/... menjadi path file di disk agar bisa dicek is_file()
function local_path_from_url(string $url): ?string {
  $prefix = '/ciburial/';
  if (strpos($url, $prefix) === 0) {
    $rel = substr($url, strlen($prefix)); // contoh: assets/images/Villas/villa3/xxx.jpg
    $path = __DIR__ . '/../../../' . $rel;
    return $path;
  }
  return null; // remote http(s) atau path lain -> tak bisa dicek
}

try {
  $pdo = pdo();
  $id = (int)($_GET['id'] ?? 0);
  if (!$id) throw new Exception('ID tidak valid');

  // ---- Ambil data villa (tanpa menyebut kolom baru supaya aman di semua skema)
  $st = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
  $st->execute([$id]);
  $villa = $st->fetch(PDO::FETCH_ASSOC);
  if (!$villa) throw new Exception('Villa tidak ditemukan');

  // ---- Harga bertingkat (fallback ke harga_per_malam bila kolom tak ada/0)
  $base  = (int)($villa['harga_per_malam'] ?? 0);
  $cols  = $pdo->query("SHOW COLUMNS FROM villas")->fetchAll(PDO::FETCH_COLUMN);
  $has_wd = in_array('harga_weekday', $cols, true);
  $has_fr = in_array('harga_friday',  $cols, true);
  $has_we = in_array('harga_weekend', $cols, true);

  $prices = [
    'weekday' => (int)($has_wd ? ($villa['harga_weekday'] ?? 0) : 0) ?: $base,
    'friday'  => (int)($has_fr ? ($villa['harga_friday']  ?? 0) : 0) ?: $base,
    'weekend' => (int)($has_we ? ($villa['harga_weekend'] ?? 0) : 0) ?: $base,
  ];

  // ===== MEDIA (FS-first) =====
  $media = [];

  // 1) Scan folder dulu: /ciburial/assets/images/Villas/villa{ID}/
  $publicBase = '/ciburial/assets/images/Villas/villa' . $id . '/';
  $diskDir    = __DIR__ . '/../../../assets/images/Villas/villa' . $id . '/';

  if (is_dir($diskDir)) {
    $list = [];
    $patterns = [
      '*.jpg','*.jpeg','*.png','*.webp','*.JPG','*.JPEG','*.PNG','*.WEBP',
      '*.mp4','*.mov','*.MP4','*.MOV'
    ];
    foreach ($patterns as $p) {
      foreach (glob($diskDir . $p) as $f) { $list[] = basename($f); }
    }
    sort($list, SORT_NATURAL);
    foreach ($list as $f) {
      $ext  = strtolower(pathinfo($f, PATHINFO_EXTENSION));
      $type = in_array($ext, ['mp4','mov'], true) ? 'video' : 'image';
      $media[] = ['type' => $type, 'url' => $publicBase . $f];
    }
  }

  // 2) Kalau folder kosong, baru coba dari tabel villa_media (atau gabungkan tambahan remote)
  try {
    $tbl = $pdo->query("SHOW TABLES LIKE 'villa_media'")->fetch();
    if ($tbl) {
      $ms = $pdo->prepare("SELECT media_type AS type, url FROM villa_media WHERE villa_id = ? ORDER BY id ASC");
      $ms->execute([$id]);
      $fromDb = $ms->fetchAll(PDO::FETCH_ASSOC) ?: [];

      // Filter: jika URL lokal /ciburial/... tapi file tidak ada -> SKIP
      $filtered = [];
      foreach ($fromDb as $m) {
        $url = (string)($m['url'] ?? '');
        if ($url === '') continue;
        $lp = local_path_from_url($url);
        if ($lp !== null) {            // URL lokal, bisa dicek
          if (is_file($lp)) $filtered[] = ['type'=>$m['type']==='video'?'video':'image','url'=>$url];
        } else {
          // remote http(s) -> ikutkan (anggap valid)
          $filtered[] = ['type'=>$m['type']==='video'?'video':'image','url'=>$url];
        }
      }

      // Jika dari folder kosong, pakai hasil DB; kalau tidak kosong, tambahkan
      if (empty($media)) {
        $media = $filtered;
      } else {
        // gabungkan yang belum ada (hindari duplikat URL)
        $exist = array_flip(array_map(fn($x)=>$x['url'], $media));
        foreach ($filtered as $m) {
          if (!isset($exist[$m['url']])) $media[] = $m;
        }
      }
    }
  } catch(Throwable $e) {
    // diamkan saja; media tetap dari FS
  }

  echo json_encode(['ok'=>true,'villa'=>$villa,'prices'=>$prices,'media'=>$media], JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
