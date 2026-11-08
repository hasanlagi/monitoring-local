<?php
declare(strict_types=1);
require_once __DIR__.'/helpers.php';
require_once __DIR__.'/sfp.php';

function sfpSchema(): void {
    db()->query("CREATE TABLE IF NOT EXISTS sfp_interfaces (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        router_id INT UNSIGNED NOT NULL,
        port_key VARCHAR(120) NOT NULL,
        interface_name VARCHAR(120) NOT NULL,
        threshold_rx DOUBLE NOT NULL DEFAULT -20,
        last_reading LONGTEXT NULL,
        sampled_at BIGINT NOT NULL DEFAULT 0,
        last_attempt BIGINT NOT NULL DEFAULT 0,
        last_error VARCHAR(255) NULL,
        UNIQUE KEY uq_sfp_port(router_id,port_key),
        CONSTRAINT fk_sfp_router FOREIGN KEY(router_id) REFERENCES routers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function sfpStore(int $routerId, string $key, string $name, array $reading, int $time): void {
    $json = json_encode($reading, JSON_THROW_ON_ERROR);
    $s = db()->prepare('INSERT INTO sfp_interfaces(router_id,port_key,interface_name,last_reading,sampled_at,last_attempt) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE interface_name=VALUES(interface_name),last_reading=VALUES(last_reading),sampled_at=VALUES(sampled_at),last_attempt=VALUES(last_attempt),last_error=NULL');
    $s->bind_param('isssii', $routerId, $key, $name, $json, $time, $time);
    $s->execute();
}

function sfpStoreError(int $routerId, string $key, string $name, string $error, int $time): void {
    $s = db()->prepare('INSERT INTO sfp_interfaces(router_id,port_key,interface_name,last_attempt,last_error) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE interface_name=VALUES(interface_name),last_attempt=VALUES(last_attempt),last_error=VALUES(last_error)');
    $s->bind_param('issis', $routerId, $key, $name, $time, $error);
    $s->execute();
}

function sfpCards(): array {
    $rows = db()->query('SELECT s.*,r.name AS router_name FROM sfp_interfaces s JOIN routers r ON r.id=s.router_id WHERE r.enabled=1 ORDER BY r.id,s.interface_name')->fetch_all(MYSQLI_ASSOC);
    $now = time();
    $cards = [];
    foreach ($rows as $row) {
        $reading = json_decode($row['last_reading'] ?? '{}', true) ?: [];
        $reading['sampled_at'] = (int)$row['sampled_at'];
        $reading['error'] = $row['last_error'];
        $state = sfpState($reading, (float)$row['threshold_rx'], $now);
        $fresh = !in_array($state['code'], ['error','stale'], true);
        $cards[] = [
            'id'=>(int)$row['id'], 'router_name'=>$row['router_name'],
            'interface_name'=>$row['interface_name'], 'threshold_rx'=>(float)$row['threshold_rx'],
            'rx_dbm'=>$fresh ? ($reading['rx_dbm'] ?? null) : null,
            'tx_dbm'=>$fresh ? ($reading['tx_dbm'] ?? null) : null,
            'temperature_c'=>$fresh ? ($reading['temperature_c'] ?? null) : null,
            'link'=>$fresh ? ($reading['link'] ?? 'unknown') : 'unknown',
            'sampled_at'=>(int)$row['sampled_at'], 'status'=>$state,
        ];
    }
    return $cards;
}

function sfpSetThreshold(int $id, mixed $value): bool {
    $threshold = sfpThreshold($value);
    $s = db()->prepare('SELECT s.id FROM sfp_interfaces s JOIN routers r ON r.id=s.router_id WHERE s.id=? AND r.enabled=1');
    $s->bind_param('i', $id);
    $s->execute();
    if (!$s->get_result()->fetch_assoc()) return false;
    $s = db()->prepare('UPDATE sfp_interfaces SET threshold_rx=? WHERE id=?');
    $s->bind_param('di', $threshold, $id);
    $s->execute();
    return true;
}
