<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../src/priority-ping.php';
priorityPingSchema();
if(in_array('--setup',$argv,true)){
 $now=time();$s=db()->prepare('INSERT IGNORE INTO priority_ping_targets(name,host,created_at) VALUES(?,?,?)');
 $targets=[['Google DNS','8.8.8.8'],['Cloudflare DNS','1.1.1.1']];
 $gateway=getenv('MONITORING_ISP_GATEWAY');
 if(filter_var($gateway,FILTER_VALIDATE_IP))array_unshift($targets,['Gateway ISP',$gateway]);
 foreach($targets as $row){$s->bind_param('ssi',$row[0],$row[1],$now);$s->execute();}exit;
}
$lock=fopen('/tmp/hng-priority-ping.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))exit;
$round=0;
do{
 $begin=microtime(true);$targets=db()->query('SELECT * FROM priority_ping_targets WHERE deleted_at IS NULL ORDER BY id')->fetch_all(MYSQLI_ASSOC);$jobs=[];
 foreach($targets as $t){if(!filter_var($t['host'],FILTER_VALIDATE_IP))continue;$pipes=[];$p=proc_open(['ping','-n','-c','1','-W','1',$t['host']],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);if(is_resource($p)){fclose($pipes[0]);$jobs[]=[$t,$p,$pipes];}}
 foreach($jobs as [$t,$p,$pipes]){$text=stream_get_contents($pipes[1]);fclose($pipes[1]);fclose($pipes[2]);$status=proc_close($p);$ms=null;if($status===0&&preg_match('/time[=<]([0-9.]+)\s*ms/',$text,$m))$ms=(float)$m[1];$id=(int)$t['id'];$ts=time();$s=db()->prepare('INSERT IGNORE INTO priority_ping_samples(target_id,ts,ms) VALUES(?,?,?)');$s->bind_param('iid',$id,$ts,$ms);$s->execute();}
 if($round++%120===0){$cut=time()-90000;db()->query("DELETE FROM priority_ping_samples WHERE ts<$cut");}
 if(in_array('--once',$argv,true)){echo 'sampled '.count($jobs)." targets\n";break;}
 usleep((int)(max(0.1,5-(microtime(true)-$begin))*1000000));
}while(true);

