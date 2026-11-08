<?php
declare(strict_types=1);
require_once __DIR__.'/../src/traffic.php';
try{
 $id=(int)($_GET['interface_id']??0);$s=db()->prepare('SELECT i.*,r.enabled AS router_enabled FROM router_interfaces i JOIN routers r ON r.id=i.router_id WHERE i.id=? AND i.enabled=1 AND r.enabled=1');$s->bind_param('i',$id);$s->execute();$cfg=$s->get_result()->fetch_assoc();if(!$cfg)jsonOut(['ok'=>false,'error'=>'Pilih interface yang aktif'],404);
 if(($_GET['mode']??'')==='live'){$r=routerById((int)$cfg['router_id']);$api=new RouterOSAPI($r['host'],(int)$r['port'],$r['username'],$r['password'],(bool)$r['use_ssl']);try{$api->connect();$data=monitorInterface($api,$cfg['interface_name']);}finally{$api->close();}jsonOut(['ok'=>true,'ts'=>time(),'data'=>$data]);}
 $step=(int)($_GET['step']??300);if(!in_array($step,[300,3600],true))jsonOut(['ok'=>false,'error'=>'Interval tidak valid'],422);
 $now=time();$start=$now-86400;
 $s=db()->prepare("SELECT FLOOR(sampled/$step)*$step AS ts,SUM(rx_bps*duration)/SUM(duration) AS rx_bps,SUM(tx_bps*duration)/SUM(duration) AS tx_bps FROM traffic_samples WHERE interface_id=? AND sampled>=? AND sampled<=? GROUP BY ts ORDER BY ts");$s->bind_param('iii',$id,$start,$now);$s->execute();$rows=$s->get_result()->fetch_all(MYSQLI_ASSOC);$map=[];foreach($rows as $row)$map[(int)$row['ts']]=$row;$out=[];
 for($t=(int)(floor($start/$step)*$step);$t<=$now;$t+=$step){$row=$map[$t]??null;$out[]=['ts'=>$t,'rx_bps'=>$row?(float)$row['rx_bps']:null,'tx_bps'=>$row?(float)$row['tx_bps']:null];}
 $s=db()->prepare('SELECT SUM(rx_bps*duration)/SUM(duration) AS rx_bps,SUM(tx_bps*duration)/SUM(duration) AS tx_bps,SUM(duration) AS seconds FROM traffic_samples WHERE interface_id=? AND sampled>=? AND sampled<=?');$s->bind_param('iii',$id,$start,$now);$s->execute();$summary=$s->get_result()->fetch_assoc();
 jsonOut(['ok'=>true,'step'=>$step,'average'=>['rx_bps'=>$summary['rx_bps']===null?null:(float)$summary['rx_bps'],'tx_bps'=>$summary['tx_bps']===null?null:(float)$summary['tx_bps']],'data'=>$out,'samples'=>count($rows),'start'=>$start,'end'=>$now]);
}catch(Throwable $e){jsonOut(['ok'=>false,'error'=>$e->getMessage()],500);}
