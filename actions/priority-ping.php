<?php
declare(strict_types=1);
require_once __DIR__.'/../src/priority-ping.php';
try{
 if($_SERVER['REQUEST_METHOD']==='POST'){
 if(($_POST['action']??'')==='delete'){$id=(int)($_POST['id']??0);if($id<=0)jsonOut(['ok'=>false,'error'=>'Target tidak valid'],422);$q=db()->prepare('UPDATE priority_ping_targets SET deleted_at=? WHERE id=? AND deleted_at IS NULL');$now=time();$q->bind_param('ii',$now,$id);$q->execute();jsonOut(['ok'=>true]);}

 $name=trim((string)($_POST['name']??''));$host=trim((string)($_POST['host']??''));
 if($name===''||strlen($name)>120||!filter_var($host,FILTER_VALIDATE_IP))jsonOut(['ok'=>false,'error'=>'Isi nama dan alamat IP OLT yang valid'],422);
 $s=db()->prepare('INSERT INTO priority_ping_targets(name,host,created_at) VALUES(?,?,?)');$now=time();$s->bind_param('ssi',$name,$host,$now);$s->execute();jsonOut(['ok'=>true]);
 }
 $now=time();$cut=$now-86400;$targets=db()->query('SELECT * FROM priority_ping_targets WHERE deleted_at IS NULL ORDER BY id')->fetch_all(MYSQLI_ASSOC);$out=[];
 foreach($targets as $t){$id=(int)$t['id'];$s=db()->prepare('SELECT COUNT(*) AS total,COUNT(ms) AS replies,AVG(ms) AS avg_ms,MAX(ms) AS max_ms,SUM(CASE WHEN ms>=40 THEN 1 ELSE 0 END) AS spikes FROM priority_ping_samples WHERE target_id=? AND ts>=?');$s->bind_param('ii',$id,$cut);$s->execute();$stats=$s->get_result()->fetch_assoc();
 $s=db()->prepare('SELECT ts,ms FROM priority_ping_samples WHERE target_id=? ORDER BY ts DESC LIMIT 60');$s->bind_param('i',$id);$s->execute();$hist=array_reverse($s->get_result()->fetch_all(MYSQLI_ASSOC));$last=$hist?end($hist):null;$total=(int)$stats['total'];$lost=$total-(int)$stats['replies'];$ms=$last&&$last['ms']!==null?(float)$last['ms']:null;$stale=!$last||$now-(int)$last['ts']>20;
 $outage=null;
 $q=db()->prepare('SELECT ts FROM priority_ping_samples WHERE target_id=? AND ms IS NULL AND ts>=? ORDER BY ts DESC LIMIT 1');$q->bind_param('ii',$id,$cut);$q->execute();$failure=$q->get_result()->fetch_assoc();
 if($failure){
  $failedAt=(int)$failure['ts'];$start=$failedAt;$previous=$failedAt;$partial=false;
  $q=db()->prepare('SELECT ts,ms FROM priority_ping_samples WHERE target_id=? AND ts<=? AND ts>=? ORDER BY ts DESC');$q->bind_param('iii',$id,$failedAt,$cut);$q->execute();$result=$q->get_result();$boundary=false;
  while($row=$result->fetch_assoc()){$stamp=(int)$row['ts'];if($previous-$stamp>20){$partial=true;$boundary=true;break;}if($row['ms']!==null){$boundary=true;break;}$start=$stamp;$previous=$stamp;}
  if(!$boundary)$partial=true;
  $q=db()->prepare('SELECT ts FROM priority_ping_samples WHERE target_id=? AND ts>? AND ms IS NOT NULL ORDER BY ts LIMIT 1');$q->bind_param('ii',$id,$failedAt);$q->execute();$reply=$q->get_result()->fetch_assoc();
  $ongoing=!$reply&&!$stale&&$ms===null;$recovered=$reply&&(int)$reply['ts']-$failedAt<=20?(int)$reply['ts']:null;
  $end=$recovered??($ongoing?$now:$failedAt+5);
  $outage=['start'=>$start,'recovered_at'=>$recovered,'duration_seconds'=>max(0,$end-$start),'ongoing'=>$ongoing,'partial'=>$partial||(!$ongoing&&$recovered===null)];
 }
 $out[]=['id'=>$id,'name'=>$t['name'],'host'=>$t['host'],'ms'=>$ms,'status'=>$stale?'STALE':($ms===null?'TIMEOUT':(priorityPingSpike($ms)?'SPIKE':'OK')),'avg_ms'=>$stats['avg_ms']===null?null:(float)$stats['avg_ms'],'max_ms'=>$stats['max_ms']===null?null:(float)$stats['max_ms'],'spikes'=>(int)$stats['spikes'],'total'=>$total,'timeouts'=>$lost,'loss_pct'=>$total?round($lost*100/$total,2):null,'last_ts'=>$last?(int)$last['ts']:null,'last_outage'=>$outage,'history'=>array_map(fn($x)=>['ts'=>(int)$x['ts'],'ms'=>$x['ms']===null?null:(float)$x['ms']],$hist)];
 }jsonOut(['ok'=>true,'threshold'=>40,'period'=>'24 jam terakhir','interval_seconds'=>5,'targets'=>$out]);
}catch(Throwable $e){$duplicate=(int)$e->getCode()===1062;jsonOut(['ok'=>false,'error'=>$duplicate?'IP tersebut sudah terdaftar':'Gagal membaca atau menyimpan monitoring ping'], $duplicate?409:500);}
