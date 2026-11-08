<?php
declare(strict_types=1);
require_once __DIR__.'/../src/sfp-store.php';
header('Cache-Control: no-store');
try {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        if (!sfpSameOrigin($_SERVER)) jsonOut(['ok'=>false, 'error'=>'Permintaan pengaturan harus dari halaman monitoring.'], 403);
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if (!$id) jsonOut(['ok'=>false, 'error'=>'Pilih interface yang valid.'], 422);
        if (!sfpSetThreshold($id, $_POST['threshold_rx'] ?? null)) jsonOut(['ok'=>false, 'error'=>'Interface tidak ditemukan.'], 404);
        jsonOut(['ok'=>true]);
    }
    if ($method !== 'GET') jsonOut(['ok'=>false, 'error'=>'Method not allowed'], 405);
    jsonOut(['ok'=>true, 'interval_seconds'=>30, 'data'=>sfpCards(), 'ts'=>time()]);
} catch (InvalidArgumentException $e) {
    jsonOut(['ok'=>false, 'error'=>$e->getMessage()], 422);
} catch (Throwable $e) {
    jsonOut(['ok'=>false, 'error'=>'Monitoring SFP belum dapat dibaca.'], 500);
}
