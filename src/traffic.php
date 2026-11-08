<?php
declare(strict_types=1);
require_once __DIR__.'/helpers.php';
function trafficRate(float $rx0,float $tx0,float $rx,float $tx,int $seconds): ?array {
 if($seconds<=0 || $seconds>180 || $rx<$rx0 || $tx<$tx0)return null;
 return [($rx-$rx0)*8/$seconds,($tx-$tx0)*8/$seconds];
}
function trafficSchema(): void {
 db()->query('CREATE TABLE IF NOT EXISTS traffic_counters (interface_id INT UNSIGNED PRIMARY KEY, sampled BIGINT NOT NULL, rx DOUBLE NOT NULL, tx DOUBLE NOT NULL, uptime VARCHAR(100) NOT NULL)');
 db()->query('CREATE TABLE IF NOT EXISTS traffic_samples (interface_id INT UNSIGNED NOT NULL, sampled BIGINT NOT NULL, rx_bps DOUBLE NOT NULL, tx_bps DOUBLE NOT NULL, duration INT NOT NULL, PRIMARY KEY(interface_id,sampled), INDEX(sampled))');
}
