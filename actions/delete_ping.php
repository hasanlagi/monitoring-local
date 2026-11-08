<?php declare(strict_types=1); require_once __DIR__.'/../src/helpers.php';
if($_SERVER['REQUEST_METHOD']!=='POST') jsonOut(['ok'=>false,'error'=>'Method not allowed'],405);
$id=(int)($_POST['id']??0);
if($id<=0) jsonOut(['ok'=>false,'error'=>'ID tidak valid'],422);
$s=db()->prepare('DELETE FROM ping_targets WHERE id=?');
$s->bind_param('i',$id); $s->execute();
jsonOut(['ok'=>$s->affected_rows>0,'error'=>$s->affected_rows?null:'Target tidak ditemukan']);
