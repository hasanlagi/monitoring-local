<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
require_once __DIR__.'/RouterOSAPI.php';
require_once __DIR__.'/pppoe-profiles.php';

function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function jsonOut(mixed $data,int $status=200): never { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data,JSON_UNESCAPED_SLASHES); exit; }
function redirectHome(): never { header('Location: index.php'); exit; }
function allRouters(bool $includeDisabled=true): array { $sql='SELECT * FROM routers'.($includeDisabled?'':' WHERE enabled=1').' ORDER BY id DESC'; $r=db()->query($sql); return $r?$r->fetch_all(MYSQLI_ASSOC):[]; }
function routerById(int $id): ?array { $s=db()->prepare('SELECT * FROM routers WHERE id=? LIMIT 1'); $s->bind_param('i',$id); $s->execute(); return $s->get_result()->fetch_assoc() ?: null; }
function allPingTargets(bool $includeDisabled=true): array { $sql='SELECT * FROM ping_targets'.($includeDisabled?'':' WHERE enabled=1').' ORDER BY id DESC'; $r=db()->query($sql); return $r?$r->fetch_all(MYSQLI_ASSOC):[]; }
function pingTargetById(int $id): ?array { $s=db()->prepare('SELECT * FROM ping_targets WHERE id=? LIMIT 1'); $s->bind_param('i',$id); $s->execute(); return $s->get_result()->fetch_assoc() ?: null; }
function selectedInterfaces(int $routerId): array { $s=db()->prepare('SELECT * FROM router_interfaces WHERE router_id=? AND enabled=1 ORDER BY sort_order,id'); $s->bind_param('i',$routerId); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
function pingHost(string $host): array {
    $host=trim($host); if($host==='') return ['ok'=>false,'latency_ms'=>null,'loss'=>100,'error'=>'Host kosong'];
    $isWin = strtoupper(substr(PHP_OS,0,3))==='WIN';
    $cmd=$isWin ? 'ping -n 1 -w 1000 '.escapeshellarg($host).' 2>&1' : 'ping -c 1 -W 1 '.escapeshellarg($host).' 2>&1';
    $out=[];$status=1; @exec($cmd,$out,$status); $txt=implode("\n",$out); $lat=null;
    if(preg_match('/(?:time[=<]|Average\s*=\s*)([0-9]+(?:[\.,][0-9]+)?)\s*ms/i',$txt,$m)) $lat=(float)str_replace(',','.',$m[1]);
    $ok=$status===0 && $lat!==null; return ['ok'=>$ok,'latency_ms'=>$lat,'loss'=>$ok?0:100,'error'=>$ok?null:'Ping timeout'];
}
function availableInterfaces(array $r): array { $api=new RouterOSAPI($r['host'],(int)$r['port'],$r['username'],$r['password'],(bool)$r['use_ssl']); try{$api->connect();$rows=$api->command('/interface/print');return array_map(fn($x)=>['name'=>$x['name']??'','type'=>$x['type']??'','running'=>($x['running']??'false')==='true','disabled'=>($x['disabled']??'false')==='true'],$rows);}finally{$api->close();} }
function monitorInterface(RouterOSAPI $api,string $name): array { $rows=$api->command('/interface/monitor-traffic',['interface'=>$name,'once'=>'']); $x=$rows[0]??[]; return ['name'=>$name,'rx_bps'=>(int)($x['rx-bits-per-second']??0),'tx_bps'=>(int)($x['tx-bits-per-second']??0),'rx_pps'=>(int)($x['rx-packets-per-second']??0),'tx_pps'=>(int)($x['tx-packets-per-second']??0),'running'=>($x['running']??'true')!=='false']; }
function routerMetric(array $r): array {
    $ping=pingHost($r['host']); $api=new RouterOSAPI($r['host'],(int)$r['port'],$r['username'],$r['password'],(bool)$r['use_ssl']);
    try{
        $api->connect(); $res=$api->command('/system/resource/print'); $sys=$res[0]??[]; $active=$api->command('/ppp/active/print'); $secrets=$api->command('/ppp/secret/print');
        $active=pppoeActiveProfiles($active,$secrets);
        $activeNames=[]; foreach($active as $a) if(isset($a['name'])) $activeNames[(string)$a['name']]=true;
        $inactive=[]; foreach($secrets as $s){$name=(string)($s['name']??''); if($name===''||isset($activeNames[$name])) continue; $inactive[]=['name'=>$name,'profile'=>$s['profile']??'','service'=>$s['service']??'','disabled'=>($s['disabled']??'false')==='true','comment'=>$s['comment']??'','last_logged_out'=>$s['last-logged-out']??$s['last-logged-out-time']??$s['last-logged-out-at']??'','last_disconnect_reason'=>$s['last-disconnect-reason']??$s['last-disconnect']??$s['disconnect-reason']??''];}
        $ifs=[]; foreach(selectedInterfaces((int)$r['id']) as $cfg){try{$m=monitorInterface($api,$cfg['interface_name']);$m['id']=(int)$cfg['id'];$m['label']=$cfg['display_name']?:$cfg['interface_name'];$ifs[]=$m;}catch(Throwable $e){$ifs[]=['id'=>(int)$cfg['id'],'name'=>$cfg['interface_name'],'label'=>$cfg['display_name']?:$cfg['interface_name'],'rx_bps'=>0,'tx_bps'=>0,'running'=>false,'error'=>$e->getMessage()];}}
        return ['online'=>true,'id'=>(int)$r['id'],'name'=>$r['name'],'host'=>$r['host'],'ping'=>$ping,'cpu'=>isset($sys['cpu-load'])?(int)$sys['cpu-load']:null,'free_memory'=>isset($sys['free-memory'])?(int)$sys['free-memory']:null,'total_memory'=>isset($sys['total-memory'])?(int)$sys['total-memory']:null,'uptime'=>$sys['uptime']??null,'version'=>$sys['version']??null,'pppoe_count'=>count($active),'pppoe'=>$active,'pppoe_inactive_count'=>count($inactive),'pppoe_inactive'=>$inactive,'interfaces'=>$ifs];
    } finally { $api->close(); }
}

