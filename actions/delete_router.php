<?php declare(strict_types=1); require_once __DIR__.'/../src/helpers.php';
$id=(int)($_POST['id']??$_GET['id']??0); if(!$id) jsonOut(['ok'=>false,'error'=>'ID tidak valid'],422); $s=db()->prepare('DELETE FROM routers WHERE id=?');$s->bind_param('i',$id);$s->execute();jsonOut(['ok'=>true]);
