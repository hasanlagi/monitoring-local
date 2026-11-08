# IONnet Network Monitoring v16

Versi baseline v16 untuk XAMPP.

## Struktur
- `index.php` — dashboard
- `install.php` — installer database
- `config/config.php` — konfigurasi MySQL & aplikasi
- `src/` — database, RouterOS API, helper
- `actions/` — endpoint AJAX/CRUD
- `sql/schema.sql` — skema database

## Fitur baseline
- Multi MikroTik via RouterOS API
- Multi interface
- Total Traffic pilih satu interface atau semua interface
- Grafik traffic realtime
- PPPoE aktif/nonaktif
- Monitoring ping
- Auto refresh 1 detik
- Responsive Bootstrap

## Konfigurasi

Salin `config/config.example.php` ke `config/config.php` dan isi pengaturan
MySQL sesuai lingkungan Anda. Buka `install.php` untuk memasang skema awal.

Jika ingin menambahkan gateway ISP saat menjalankan
`php bin/collect-priority-ping.php --setup`, atur variabel lingkungan
`MONITORING_ISP_GATEWAY` dengan alamat IP gateway.
