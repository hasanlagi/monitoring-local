<?php
declare(strict_types=1);
function pppoeActiveProfiles(array $active,array $secrets):array {
    $profiles=[];
    foreach($secrets as $secret)if(isset($secret['name']))$profiles[(string)$secret['name']]=(string)($secret['profile']??'');
    foreach($active as &$session){
        if(trim((string)($session['profile']??''))!=='')continue;
        $radius=in_array($session['radius']??false,[true,1,'true','yes'],true);
        $session['profile']=$radius?'':($profiles[(string)($session['name']??'')]??'');
    }unset($session);
    return $active;
}
