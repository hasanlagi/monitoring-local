<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../src/sfp-store.php';

if (in_array('--setup', $argv, true)) { sfpSchema(); echo "SFP schema ready\n"; exit; }
$lock = db()->query("SELECT GET_LOCK('hng_sfp_collector',0) AS acquired")->fetch_assoc();
if ((int)($lock['acquired'] ?? 0) !== 1) exit;
$count = 0;
$errors = 0;
foreach (allRouters(false) as $router) {
    $routerId = (int)$router['id'];
    $scanTime = time();
    $api = new RouterOSAPI($router['host'], (int)$router['port'], $router['username'], $router['password'], (bool)$router['use_ssl']);
    try {
        $api->connect();
        $interfaces = $api->command('/interface/ethernet/print');
        foreach ($interfaces as $interface) {
            $name = (string)($interface['name'] ?? '');
            $key = (string)($interface['default-name'] ?? $name);
            if (!preg_match('/(?:q?sfp)/i', $key.' '.$name)) continue;
            try {
                $rows = $api->command('/interface/ethernet/monitor', ['numbers'=>$interface['.id'], 'once'=>'']);
                if (!$rows) throw new RuntimeException('No monitor data');
                sfpStore($routerId, $key, $name, sfpReading($rows[0]), $scanTime);
                $count++;
            } catch (Throwable $e) {
                sfpStoreError($routerId, $key, $name, 'Data interface tidak dapat dibaca', $scanTime);
                $errors++;
            }
        }
        $s = db()->prepare("UPDATE sfp_interfaces SET last_error='Interface tidak ditemukan' WHERE router_id=? AND last_attempt<>?");
        $s->bind_param('ii', $routerId, $scanTime);
        $s->execute();
    } catch (Throwable $e) {
        $s = db()->prepare("UPDATE sfp_interfaces SET last_error='Router tidak dapat dihubungi',last_attempt=? WHERE router_id=?");
        $s->bind_param('ii', $scanTime, $routerId);
        $s->execute();
        $errors++;
        fwrite(STDERR, "SFP router $routerId: collection failed\n");
    } finally { $api->close(); }
}
echo "SFP interfaces=$count errors=$errors\n";
exit($errors ? 1 : 0);
