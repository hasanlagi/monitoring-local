<?php
declare(strict_types=1);
require_once __DIR__.'/helpers.php';
function priorityPingSchema():void{
 db()->query("CREATE TABLE IF NOT EXISTS priority_ping_targets(id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,host VARCHAR(45) NOT NULL UNIQUE,created_at BIGINT NOT NULL)");
 db()->query('CREATE TABLE IF NOT EXISTS priority_ping_samples(target_id INT UNSIGNED NOT NULL,ts BIGINT NOT NULL,ms DOUBLE NULL,PRIMARY KEY(target_id,ts),INDEX(ts))');
}
function priorityPingSpike(?float $ms):bool{return $ms!==null&&$ms>=40;}
