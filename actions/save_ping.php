<?php declare(strict_types=1); require_once __DIR__.'/../src/helpers.php';
if($_SERVER['REQUEST_METHOD']!=='POST') jsonOut(['ok'=>false,'error'=>'Method not allowed'],405);
$id=(int)($_POST['id']??0); $name=trim((string)($_POST['name']??'')); $host=trim((string)($_POST['host']??'')); $icon=trim((string)($_POST['icon']??'bi-hdd-network')); $enabled=isset($_POST['enabled'])?1:0;
if($name===''||$host==='') jsonOut(['ok'=>false,'error'=>'Nama dan IP/host wajib diisi'],422);
if($id){$s=db()->prepare('UPDATE ping_targets SET name=?,host=?,icon=?,enabled=? WHERE id=?');$s->bind_param('sssii',$name,$host,$icon,$enabled,$id);$s->execute();}
else{$s=db()->prepare('INSERT INTO ping_targets(name,host,icon,enabled) VALUES(?,?,?,?)');$s->bind_param('sssi',$name,$host,$icon,$enabled);$s->execute();$id=$s->insert_id;}
jsonOut(['ok'=>true,'id'=>$id]);
