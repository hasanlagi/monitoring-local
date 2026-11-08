<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../src/traffic.php';
$lock=fopen('/tmp/hng-monitor-traffic.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))exit;
trafficSchema();$count=0;$errors=0;
foreach(allRouters(false) as $r){
 $api=new RouterOSAPI($r['host'],(int)$r['port'],$r['username'],$r['password'],(bool)$r['use_ssl']);
 try{
  $api->connect();$resource=$api->command('/system/resource/print');$uptime=(string)($resource[0]['uptime']??'');
  $rows=$api->command('/interface/print');$now=time();$map=[];foreach($rows as $row)$map[trim($row['name']??'')]=$row;
  foreach(selectedInterfaces((int)$r['id']) as $cfg){
   $row=$map[trim($cfg['interface_name'])]??null;if(!$row||!isset($row['rx-byte'],$row['tx-byte']))continue;
   $id=(int)$cfg['id'];$rx=(float)$row['rx-byte'];$tx=(float)$row['tx-byte'];
   $old=db()->query("SELECT * FROM traffic_counters WHERE interface_id=$id")->fetch_assoc();
   if($old){$seconds=$now-(int)$old['sampled'];$rate=trafficRate((float)$old['rx'],(float)$old['tx'],$rx,$tx,$seconds);
    // A lower uptime also invalidates a sample, even when counters exceeded their previous values.
    $parse=function(string $s): int {preg_match('/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/',$s,$m);return (int)($m[1]??0)*604800+(int)($m[2]??0)*86400+(int)($m[3]??0)*3600+(int)($m[4]??0)*60+(int)($m[5]??0);};
    if($rate!==null&&$parse($uptime)>=$parse($old['uptime'])){$s=db()->prepare('INSERT IGNORE INTO traffic_samples VALUES(?,?,?,?,?)');$s->bind_param('iiddi',$id,$now,$rate[0],$rate[1],$seconds);$s->execute();$count++;}
   }
   $s=db()->prepare('REPLACE INTO traffic_counters VALUES(?,?,?,?,?)');$s->bind_param('iidds',$id,$now,$rx,$tx,$uptime);$s->execute();
  }
 }catch(Throwable $e){$errors++;fwrite(STDERR,'Router '.(int)$r['id'].': '.$e->getMessage()."\n");}finally{$api->close();}
}
$cut=time()-172800;db()->query("DELETE FROM traffic_samples WHERE sampled<$cut");echo "samples=$count errors=$errors\n";exit($errors?1:0);
