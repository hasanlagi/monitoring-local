<?php
declare(strict_types=1);
require_once __DIR__.'/config/config.php';
$msg=''; $ok=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
    $mysqli=@new mysqli(DB_HOST,DB_USER,DB_PASS); if($mysqli->connect_errno) throw new RuntimeException($mysqli->connect_error);
    $sql=file_get_contents(__DIR__.'/sql/schema.sql');
    if(!$mysqli->multi_query($sql)) throw new RuntimeException($mysqli->error);
    while($mysqli->more_results() && $mysqli->next_result()){}
    $defaults=[
      ['Google DNS','8.8.8.8','bi-google'],
      ['Cloudflare DNS','1.1.1.1','bi-cloud-fill'],
      ['Google','google.com','bi-google'],
      ['YouTube','youtube.com','bi-youtube'],
      ['WhatsApp','whatsapp.com','bi-whatsapp'],
      ['Facebook','facebook.com','bi-facebook'],
      ['Instagram','instagram.com','bi-instagram'],
      ['TikTok','tiktok.com','bi-tiktok'],
      ['Telegram','telegram.org','bi-telegram'],
      ['Netflix','netflix.com','bi-play-circle-fill']
    ];
    $q=$mysqli->prepare('SELECT id FROM ping_targets WHERE name=? AND host=? LIMIT 1');
    $ins=$mysqli->prepare('INSERT INTO ping_targets(name,host,icon,enabled) VALUES(?,?,?,1)');
    foreach($defaults as $d){$q->bind_param('ss',$d[0],$d[1]);$q->execute();if($q->get_result()->num_rows===0){$ins->bind_param('sss',$d[0],$d[1],$d[2]);$ins->execute();}}
    $q->close();$ins->close();
    $ok=true; $msg='Database, tabel, dan target ping platform berhasil disiapkan. Hapus/rename install.php setelah selesai.';
  }catch(Throwable $e){$msg=$e->getMessage();}
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install <?=e(APP_NAME)?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-5"><div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm p-4"><h2 class="fw-bold">Install <?=e(APP_NAME)?></h2><p class="text-secondary">Installer khusus XAMPP. Pastikan MySQL aktif dan konfigurasi <code>config/config.php</code> sudah sesuai.</p><?php if($msg):?><div class="alert <?=$ok?'alert-success':'alert-danger'?>"><?=e($msg)?></div><?php endif;?><form method="post"><button class="btn btn-primary btn-lg">Buat Database & Tabel</button></form><?php if($ok):?><hr><a class="btn btn-success" href="index.php">Buka Dashboard</a><?php endif;?></div></div></div></div></body></html>
