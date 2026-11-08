<?php
declare(strict_types=1);

function sfpNumber(mixed $value): ?float {
    if (!is_scalar($value) || is_bool($value)) return null;
    $text = trim((string)$value);
    if (!preg_match('/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)\s*(?:dBm|°?C)?$/iu', $text)) return null;
    $number = (float)$text;
    return is_finite($number) ? $number : null;
}

function sfpThreshold(mixed $value): float {
    if (!is_scalar($value) || is_bool($value) || !is_numeric($value)) {
        throw new InvalidArgumentException('Batas RX harus berupa angka dBm (-40 hingga 0).');
    }
    $number = (float)$value;
    if (!is_finite($number) || $number < -40 || $number > 0) {
        throw new InvalidArgumentException('Batas RX harus antara -40 dan 0 dBm.');
    }
    return $number;
}

function sfpSameOrigin(array $server): bool {
    if (($server['HTTP_SEC_FETCH_SITE'] ?? '') === 'cross-site') return false;
    if (!isset($server['HTTP_ORIGIN'])) return true;
    $origin = parse_url($server['HTTP_ORIGIN']);
    if (!is_array($origin) || !isset($origin['scheme'], $origin['host'])) return false;
    $scheme = !empty($server['HTTPS']) && $server['HTTPS'] !== 'off' ? 'https' : 'http';
    $authority = strtolower($origin['host']).(isset($origin['port']) ? ':'.$origin['port'] : '');
    return $origin['scheme'] === $scheme && $authority === strtolower($server['HTTP_HOST'] ?? '');
}

function sfpReading(array $monitor): array {
    $present = isset($monitor['sfp-module-present']) ? $monitor['sfp-module-present'] === 'true' : null;
    $rx = sfpNumber($monitor['sfp-rx-power'] ?? null);
    $tx = sfpNumber($monitor['sfp-tx-power'] ?? null);
    $temperature = sfpNumber($monitor['sfp-temperature'] ?? null);
    if ($present === false) $rx = $tx = $temperature = null;
    if (str_contains(strtolower($monitor['sfp-connector-type'] ?? ''), 'copper')) $rx = $tx = null;
    if ($present === null && ($rx !== null || $tx !== null)) $present = true;
    return [
        'rx_dbm'=>$rx, 'tx_dbm'=>$tx, 'temperature_c'=>$temperature,
        'module_present'=>$present, 'link'=>(string)($monitor['status'] ?? 'unknown'),
        'rx_loss'=>($monitor['sfp-rx-loss'] ?? '') === 'true',
        'tx_fault'=>($monitor['sfp-tx-fault'] ?? '') === 'true',
        'connector'=>(string)($monitor['sfp-connector-type'] ?? ''),
    ];
}

function sfpState(array $reading, float $threshold, int $now): array {
    $state = static fn(string $code, string $label, bool $alarm=false): array => compact('code', 'label', 'alarm');
    if (!empty($reading['error'])) return $state('error', 'Gagal membaca SFP');
    if (empty($reading['sampled_at']) || $now-(int)$reading['sampled_at'] > 90) return $state('stale', 'Data terlambat');
    if (($reading['module_present'] ?? null) === false) return $state('absent', 'Modul belum terpasang');
    if (!empty($reading['rx_loss'])) return $state('alarm', 'RX LOSS', true);
    if (!empty($reading['tx_fault'])) return $state('alarm', 'TX FAULT', true);
    if (($reading['link'] ?? '') === 'no-link') {
        return ($reading['module_present'] ?? null) === true
            ? $state('down', 'Link putus', true)
            : $state('unavailable', 'Status modul belum tersedia');
    }
    if (($reading['link'] ?? '') !== 'link-ok') return $state('unavailable', 'Status belum tersedia');
    $rx = $reading['rx_dbm'] ?? null;
    if ($rx === null) {
        if (str_contains(strtolower($reading['connector'] ?? ''), 'copper')) return $state('dac', 'DAC · tanpa optik');
        return $state('unavailable', 'RX tidak tersedia');
    }
    if ($rx <= $threshold) return $state('alarm', 'RX rendah', true);
    return $state('normal', 'Normal');
}
