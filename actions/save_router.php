<?php
declare(strict_types=1);
require_once __DIR__.'/../src/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonOut(['ok'=>false,'error'=>'Method not allowed'],405);

try {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $host = trim((string)($_POST['host'] ?? ''));
    $port = (int)($_POST['port'] ?? 8728);
    $port = $port > 0 ? $port : 8728;
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $ssl = isset($_POST['use_ssl']) ? 1 : 0;
    $enabled = isset($_POST['enabled']) ? 1 : 0;

    if ($name === '' || $host === '' || $username === '') {
        jsonOut(['ok'=>false,'error'=>'Nama, IP/host, dan username wajib diisi'],422);
    }
    if ($id === 0 && $password === '') {
        jsonOut(['ok'=>false,'error'=>'Password wajib untuk MikroTik baru'],422);
    }

    $d = db();

    if ($id > 0) {
        if (!routerById($id)) jsonOut(['ok'=>false,'error'=>'Router tidak ditemukan'],404);
        if ($password === '') {
            $s = $d->prepare('UPDATE routers SET name=?,host=?,port=?,username=?,use_ssl=?,enabled=? WHERE id=?');
            if (!$s) throw new RuntimeException('Prepare update gagal: '.$d->error);
            $s->bind_param('ssissii',$name,$host,$port,$username,$ssl,$enabled,$id);
        } else {
            $s = $d->prepare('UPDATE routers SET name=?,host=?,port=?,username=?,password=?,use_ssl=?,enabled=? WHERE id=?');
            if (!$s) throw new RuntimeException('Prepare update gagal: '.$d->error);
            $s->bind_param('ssissiii',$name,$host,$port,$username,$password,$ssl,$enabled,$id);
        }
        if (!$s->execute()) throw new RuntimeException('Gagal memperbarui MikroTik: '.$s->error);
        $s->close();
    } else {
        $s = $d->prepare('INSERT INTO routers(name,host,port,username,password,use_ssl,enabled) VALUES(?,?,?,?,?,?,?)');
        if (!$s) throw new RuntimeException('Prepare insert gagal: '.$d->error);
        $s->bind_param('ssissii',$name,$host,$port,$username,$password,$ssl,$enabled);
        if (!$s->execute()) throw new RuntimeException('Gagal menambahkan MikroTik: '.$s->error);
        $id = (int)$s->insert_id;
        $s->close();
    }

    // IMPORTANT: saving a router is independent from API connectivity.
    // Interface discovery is explicitly done after saving from the dashboard.
    jsonOut([
        'ok'=>true,
        'id'=>$id,
        'message'=>'MikroTik berhasil disimpan. Silakan tes koneksi atau deteksi interface.',
    ]);
} catch (Throwable $e) {
    jsonOut(['ok'=>false,'error'=>$e->getMessage()],500);
}
