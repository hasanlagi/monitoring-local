<?php declare(strict_types=1); require_once __DIR__.'/src/helpers.php';
$routers=allRouters(false); $pings=allPingTargets(false);
?><!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=e(APP_NAME)?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"><script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<style>
:root{--bg:#071421;--sidebar:#06111d;--panel:#0c1c2c;--panel2:#0f2437;--line:#21435d;--muted:#8fa8bb;--blue:#16a4ff;--green:#22d783;--yellow:#f5bb3a;--red:#ff5b68}*{box-sizing:border-box}body{background:radial-gradient(circle at top right,#0b263b 0,#071421 35%,#06121f 100%);color:#edf6fb;font-size:.88rem}.main{padding:12px 16px 18px}.topbar,.panel{background:rgba(10,29,45,.93);border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 35px rgba(0,0,0,.16)}.topbar{padding:10px 12px}.searchbox{max-width:520px}.stat{padding:9px 12px;min-height:74px;height:74px;position:relative;overflow:hidden;display:flex;align-items:center;gap:10px}.stat-icon{width:38px;height:38px;min-width:38px;border-radius:9px;display:grid;place-items:center;font-size:1.12rem;margin:0;background:linear-gradient(135deg,#0c77d9,#20b4ff)}.stat-body{min-width:0;flex:1}.traffic-line{display:flex;align-items:center;gap:8px;justify-content:space-between}.traffic-line .stat-value{white-space:nowrap}.traffic-select-wrap{min-width:118px;max-width:42%;}.stat-title{color:#a7bbca;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;line-height:1.1}.stat-value{font-size:1.45rem;font-weight:800;line-height:1.05;margin-top:2px}.stat-meta{color:#7891a5;font-size:.68rem;line-height:1.1}.traffic-select{width:100%;font-size:.66rem;padding:.12rem .3rem;height:24px;margin:0}.traffic-selected-label{color:#8fa8bb;font-size:.6rem;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.section-head{padding:10px 12px 0}.section-title{font-size:1rem;font-weight:800}.muted{color:var(--muted)}.chart-card{padding:11px;height:335px;background:linear-gradient(180deg,#0b1b2b,#081725);overflow:hidden}.chart-card .chart-wrap{height:225px;margin-top:5px}.chart-meta{display:flex;justify-content:space-between;align-items:start}.chart-title{font-size:.88rem;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:72%}.chart-sub{font-size:.7rem;color:#71899b;margin-top:2px}.speed{font-size:.78rem;font-weight:800;display:flex;gap:12px;align-items:center}.chart-statbar{display:flex;justify-content:space-between;gap:10px;border-top:1px solid #1c374c;margin-top:4px;padding-top:6px;font-size:.68rem;color:#8097a8}.chart-statbar strong{color:#d7e7ef;font-size:.72rem}.graph-legend{display:flex;gap:11px;align-items:center;font-size:.68rem;color:#8ca2b2;margin-top:2px}.legend-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:4px}.rx-dot{background:#20a5ff;box-shadow:0 0 6px rgba(32,165,255,.55)}.tx-dot{background:#22d783;box-shadow:0 0 6px rgba(34,215,131,.5)}.up{color:var(--green)}.down{color:#37a7ff}.danger{color:var(--red)}.warning{color:var(--yellow)}.badge-soft{border:1px solid #22506e;background:#0b263a;color:#bcd2e1}.table{--bs-table-bg:transparent;--bs-table-color:#dbe7ef;--bs-table-border-color:#234258}.table-hover>tbody>tr:hover>*{--bs-table-bg:#112b40;color:#fff}.panel-scroll{overflow:hidden}.inactive-panel{min-height:0;height:610px}.inactive-table{font-size:.78rem}.inactive-table th,.inactive-table td{padding:.36rem .5rem}.inactive-table tbody tr{height:30px}.active-panel{height:300px;overflow:hidden}.active-table-wrap{height:255px;overflow:auto}.active-table-wrap thead th{position:sticky;top:0;background:#0b1d2d;z-index:2;border-bottom:1px solid #2b4b61}.active-table{font-size:.75rem}.active-table th,.active-table td{padding:.32rem .45rem}.active-table tbody tr{height:29px}.inactive-table .offline-duration{font-weight:800;color:#f5bb3a;white-space:nowrap}.status-dot{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:7px}.status-online{background:var(--green);box-shadow:0 0 0 4px rgba(34,215,131,.12)}.status-offline{background:var(--red);box-shadow:0 0 0 4px rgba(255,91,104,.12)}.ping-card{padding:0}.ping-table-wrap{max-height:540px;overflow:auto}.ping-table-wrap thead th{position:sticky;top:0;background:#0b1d2d;z-index:2;border-bottom:1px solid #2b4b61}.ping-table-wrap tbody tr{height:34px}.ping-table-name{font-weight:800}.ping-table-host{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.68rem;color:#7893a5}.ping-media-icon{width:30px;height:30px;border-radius:8px;display:grid;place-items:center;background:#102c42;color:#4ab8ff;font-size:.9rem}.ping-inline-chart{height:28px;min-width:160px}.ping-row-status{display:inline-flex;align-items:center;gap:6px}.ping-action .btn{padding:.12rem .28rem}.ping-avg{font-weight:800}.ping-last{font-size:.74rem;color:#b6cfdd}.ping-row{padding:11px 13px;border-bottom:1px solid #1d374c}.ping-row:last-child{border-bottom:0}.ping-icon{width:36px;height:36px;border-radius:9px;display:grid;place-items:center;background:#102c42;color:#49b7ff;font-size:1rem}.spark{height:42px}.ping-box{background:linear-gradient(180deg,#0b1d2d,#081725);border:1px solid #1e4158;border-radius:10px;padding:10px;height:150px;position:relative;overflow:hidden}.ping-box .ping-head{display:flex;justify-content:space-between;align-items:center;gap:8px}.ping-box .ping-name{font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ping-box .ping-host{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.7rem;color:#7893a5}.ping-box .ping-value{font-size:1.05rem;font-weight:900}.ping-chart{height:68px!important;margin-top:4px}.ping-foot{display:flex;justify-content:space-between;gap:8px;font-size:.66rem;color:#7893a5}.ping-foot strong{color:#dcecf4}.mini-kpi{font-size:.72rem;color:#839aab;text-transform:uppercase}.mini-val{font-weight:800}.form-control,.form-select,.input-group-text{background:#081722!important;border-color:#2a4b62!important;color:#e9f3f8!important}.form-control:focus,.form-select:focus{border-color:#38a9ff!important;box-shadow:0 0 0 .2rem rgba(56,169,255,.12)}.modal-content{background:#0b1c2a;border:1px solid #28495f;color:#eef5f9}.footer{color:#678096}.table-sm td,.table-sm th{padding:.42rem .55rem}.inactive-table th,.inactive-table td{white-space:nowrap}.btn-primary{background:#087be5;border-color:#087be5}.btn-outline-light{border-color:#35546a}.card-icon{font-size:1.1rem;margin-right:6px}@media(max-width:700px){.main{padding:8px}.traffic-line{gap:6px}.traffic-select-wrap{min-width:105px;max-width:48%}.stat-value{font-size:1.22rem}.ping-table-wrap{max-height:none}.inactive-panel{height:auto}.active-panel{height:260px}.active-table-wrap{height:220px}.topbar{gap:8px}.searchbox{max-width:none}.topbar .btn-label{display:none}}
/* Let exactly two traffic charts fill the row on tablet and desktop. */
@media (min-width: 768px) {
  #interfaceGrid > [data-if-card]:first-child:nth-last-child(2),
  #interfaceGrid > [data-if-card]:first-child:nth-last-child(2) ~ [data-if-card] {
    width: 50%;
  }
}
.traffic-directions{display:flex;flex-wrap:wrap;gap:3px 12px;font-size:.65rem;line-height:1.35;margin-top:3px}.stat:has(#kTraffic){height:auto;min-height:94px}.traffic-directions span{white-space:nowrap}
.stat{height:auto;min-height:112px}.traffic-main .stat-value{font-size:clamp(1.5rem,2.3vw,2.25rem);line-height:1.2}.traffic-main .stat-title{font-size:.74rem}.traffic-average{display:flex;flex-wrap:wrap;gap:12px 24px;padding:10px 0}.traffic-average small{display:block;color:#8fa8bb;font-size:.7rem}.traffic-average strong{font-size:1.2rem}

/* Balanced traffic cards and matching graph panels. */
#dashboard>.col-6{display:flex}#dashboard>.col-6>.stat{width:100%;height:100%;min-height:112px}
.traffic-main .stat-value{font-size:clamp(1.4rem,1.75vw,1.85rem);line-height:1.15}
#interfaceGrid>div{display:flex}#interfaceGrid>div>.panel{width:100%;display:flex;flex-direction:column}
.traffic-chart-head{min-height:120px;display:flex;flex-direction:column;justify-content:flex-start}
.traffic-chart-plot{height:210px;position:relative;flex-shrink:0}
@media(max-width:575px){.traffic-chart-head{min-height:140px}.traffic-main .stat-value{font-size:1.4rem}#dashboard>.col-6>.stat{min-height:130px}.traffic-main .stat-icon{width:30px;height:30px;min-width:30px}.traffic-main{gap:7px;padding:9px}}

/* Compact summary cards, with matching heights. */
#dashboard>.col-6>.stat{min-height:82px;padding:7px 10px;gap:8px}
#dashboard .stat-icon{width:32px;height:32px;min-width:32px;font-size:1rem}
#dashboard .traffic-main .stat-value{font-size:clamp(1.25rem,1.55vw,1.6rem);line-height:1.1}
#dashboard .traffic-select{height:21px;font-size:.62rem;padding:0 .25rem}
#dashboard .traffic-selected-label{font-size:.55rem;line-height:1.1}
#dashboard .traffic-main .traffic-selected-label.mt-2{margin-top:4px!important}
@media(max-width:575px){#dashboard>.col-6>.stat{min-height:94px;padding:7px;gap:6px}#dashboard .stat-icon{width:26px;height:26px;min-width:26px;font-size:.85rem}#dashboard .traffic-main .stat-value{font-size:1.2rem}}

/* Keep offline PPPoE rows visible higher on the dashboard. */
.traffic-chart-head{min-height:90px}
.traffic-chart-plot{height:180px}
.traffic-average{padding:4px 0;gap:8px 18px}
.traffic-average strong{font-size:1rem}
#interfaceGrid>div>.panel{padding:10px!important}
#interfaces .p-3.pt-2{padding:8px!important}
#priorityPing{padding:9px 12px!important}
#priorityPingGrid svg{height:32px}
#priorityPingMessage{font-size:.65rem;margin-top:3px!important}
#priorityPingGrid{margin-top:0!important}
#priorityPingGrid .p-2{padding:6px!important}
#priorityPingGrid strong{font-size:1.05rem!important}
#priorityPingGrid .small{font-size:.68rem}
#dashboard,#interfaces,#priorityPing{margin-bottom:10px!important}
@media(max-width:575px){.traffic-chart-head{min-height:120px}.traffic-chart-plot{height:170px}}

/* Smaller desktop summary row. */
#dashboard>.col-6>.stat{min-height:64px;padding:5px 8px;gap:6px}
#dashboard .stat-icon{width:26px;height:26px;min-width:26px;font-size:.85rem}
#dashboard .stat-title{font-size:.59rem;line-height:1.05;letter-spacing:.025em}
#dashboard .stat-value{font-size:1.15rem;line-height:1.05;margin-top:2px}
#dashboard .traffic-main .stat-value{font-size:clamp(1.1rem,1.25vw,1.35rem);line-height:1.05}
#dashboard .traffic-select{height:18px;font-size:.55rem;padding:0 .2rem;margin-top:2px!important}
#dashboard .traffic-selected-label,#dashboard .stat-meta{font-size:.5rem;line-height:1.05;margin-top:2px}
#dashboard .traffic-main .traffic-selected-label.mt-2{margin-top:2px!important}
.topbar{padding:7px 10px}
@media(max-width:575px){#dashboard>.col-6>.stat{min-height:76px}#dashboard .traffic-main .stat-value{font-size:1.1rem}}

.ping-stat-roll{height:34px;overflow:hidden;width:96px;position:relative}
.ping-stat-track{animation:pingStatRoll 8s ease-in-out infinite}
.ping-stat-item{height:34px;display:flex;flex-direction:column;justify-content:center;line-height:1.15;white-space:nowrap;font-size:.73rem}
.ping-stat-item small{font-size:.59rem;color:#8fa8bb}
@keyframes pingStatRoll{0%,40%{transform:translateY(0)}50%,90%{transform:translateY(-34px)}100%{transform:translateY(0)}}
.ping-stat-roll:hover .ping-stat-track{animation-play-state:paused}
@media(prefers-reduced-motion:reduce){.ping-stat-roll{height:auto}.ping-stat-track{animation:none}.ping-stat-item{height:28px}}

/* Compact priority ping cards: four per desktop row. */
#priorityPingGrid{--bs-gutter-x:6px;--bs-gutter-y:6px;max-height:190px;overflow-y:auto}
#priorityPingGrid svg{height:20px;display:block}
#priorityPingGrid .p-2{padding:5px!important}
#priorityPingGrid strong{font-size:.95rem!important}
#priorityPingGrid b{font-size:.66rem}
#priorityPingGrid .small{font-size:.6rem;line-height:1.15}
#priorityPingGrid .d-flex.flex-wrap{gap:3px 7px!important}
#priorityPing{padding:7px 10px!important}
#priorityPingMessage{display:none}
#priorityPing>.d-flex>div>.small{font-size:.61rem}
@media(max-width:575px){#priorityPingGrid{max-height:230px}}

@media(min-width:1200px){#priorityPingGrid>div{flex:0 0 auto;width:12.5%}#priorityPingGrid .d-flex.justify-content-between{gap:3px!important}#priorityPingGrid b{font-size:.6rem}#priorityPingGrid strong{font-size:.8rem!important}#priorityPingGrid .small{font-size:.55rem}#priorityPingGrid .d-flex.flex-wrap{gap:2px 5px!important}}

/* Show loss and peak side by side without rotation. */
.ping-stat-roll{height:auto;min-height:28px;width:128px;overflow:visible}
.ping-stat-track{animation:none!important;transform:none!important;display:flex;align-items:center;gap:12px}
.ping-stat-item{height:28px;flex:0 0 auto;font-size:.7rem}
.ping-stat-item small{font-size:.57rem}

/* Readable type inside the eight-column ping grid. */
#priorityPingGrid .p-2{padding:7px!important}
#priorityPingGrid b{font-size:.76rem;line-height:1.25}
#priorityPingGrid strong{font-size:1.12rem!important;line-height:1.2}
#priorityPingGrid .small{font-size:.72rem;line-height:1.3}
#priorityPingGrid .d-flex.flex-wrap{gap:4px 8px!important}
#priorityPingGrid .p-2>div:last-child{font-size:.66rem!important;line-height:1.25;margin-top:3px}
#priorityPing>.d-flex>div>.small{font-size:.72rem}
#priorityPingGrid{max-height:245px}
@media(min-width:1200px){#priorityPingGrid b{font-size:.76rem}#priorityPingGrid strong{font-size:1.12rem!important}#priorityPingGrid .small{font-size:.72rem}}

/* Larger text for platform ping monitoring. */
.ping-table-wrap .table{font-size:.9rem}
.ping-table-wrap thead th{font-size:.85rem}
.ping-table-name{font-size:.95rem;line-height:1.25}
.ping-table-host{font-size:.76rem;line-height:1.25}
.ping-table-wrap .smallmono{font-size:.85rem}
.ping-avg,.ping-last,.ping-row-status{font-size:.88rem}
.ping-table-wrap .small.fw-bold{font-size:.85rem!important}
.ping-stat-roll{width:150px;min-height:38px}
.ping-stat-track{gap:14px}
.ping-stat-item{height:38px;font-size:.87rem;line-height:1.25}
.ping-stat-item small{font-size:.76rem;line-height:1.25}
.ping-table-wrap tbody tr{height:45px}
.ping-table-wrap .table td{padding:.4rem .5rem}

/* Slightly smaller platform ping text. */
.ping-table-wrap .table{font-size:.83rem}
.ping-table-wrap thead th{font-size:.79rem}
.ping-table-name{font-size:.87rem}
.ping-table-host{font-size:.7rem}
.ping-table-wrap .smallmono{font-size:.79rem}
.ping-avg,.ping-last,.ping-row-status{font-size:.81rem}
.ping-table-wrap .small.fw-bold{font-size:.79rem!important}
.ping-stat-item{font-size:.8rem;height:34px}
.ping-stat-item small{font-size:.7rem}
.ping-stat-roll{min-height:34px}
.ping-table-wrap tbody tr{height:41px}
.ping-table-wrap .table td{padding:.34rem .45rem}

/* Default dashboard display scale. */
html{zoom:0.9}

#pppoe[hidden]{display:none!important}

/* Sideways ping card navigation. */
#priorityPingGrid{flex-wrap:nowrap;overflow-x:auto;overflow-y:hidden;max-height:none;margin-left:0;margin-right:0;padding-bottom:5px;scroll-snap-type:x proximity}
#priorityPingGrid>div{flex:0 0 80%;max-width:80%;scroll-snap-align:start}
@media(min-width:576px){#priorityPingGrid>div{flex-basis:45%;max-width:45%}}
@media(min-width:992px){#priorityPingGrid>div{flex-basis:25%;max-width:25%}}
@media(min-width:1200px){#priorityPingGrid>div{flex-basis:12.5%;max-width:12.5%}}
@media(prefers-reduced-motion:reduce){#priorityPingGrid{scroll-behavior:auto}}

#priorityPingGrid{cursor:grab;touch-action:pan-x pan-y}#priorityPingGrid.is-dragging{cursor:grabbing;user-select:none;scroll-snap-type:none}#priorityPingGrid button{cursor:pointer}
</style><link rel="stylesheet" href="sfp-cards.css?v=20261103i"><link rel="stylesheet" href="pppoe-off-alarm.css?v=20261103b"><script src="pppoe-split.js?v=20261106b"></script><style>.pppoe-tabs{display:flex;gap:8px;flex-wrap:wrap;margin:10px 0}.pppoe-tabs .btn{min-height:36px;padding:6px 12px}.pppoe-tabs [aria-pressed="true"]{background:#183e55;border-color:#59bde8;color:#fff}.pppoe-tabs b{margin-left:6px}.pppoe-tabs [data-pppoe-tab="blocked"][aria-pressed="true"]{background:#4d3917;border-color:#c99b39}@media(max-width:767px){.pppoe-tabs .btn{min-height:44px;flex:1}}</style><script src="pppoe-active.js?v=20261106a"></script></head>
<body>

<main class="main"><div class="topbar d-flex align-items-center justify-content-between gap-2 mb-3"><div class="topbar-title d-flex align-items-center gap-2"><i class="bi bi-speedometer2 text-info fs-5"></i><span class="fw-bold">HNG Network Monitoring</span></div><div class="d-flex align-items-center gap-2"><span class="badge badge-soft d-none d-md-inline"><i class="bi bi-circle-fill text-success me-1"></i> System Online</span><button class="btn btn-primary" onclick="openRouter()"><i class="bi bi-router"></i> <span class="btn-label">Tambah MikroTik</span></button></div></div>
<div class="row g-3 mb-3" id="dashboard">
<div class="col-6 col-xl-3"><div class="panel stat traffic-main"><div class="stat-icon"><i class="bi bi-arrow-down"></i></div><div class="stat-body"><div class="stat-title">Total Download</div><div class="stat-value down" id="kDownload">—</div><select id="trafficSelect" aria-label="Interface download dan upload" class="form-select form-select-sm traffic-select mt-1"><option value="all">Semua Interface</option></select><div class="traffic-selected-label" id="trafficSelectedLabel">Realtime</div><span id="kTraffic" hidden></span></div></div></div>
<div class="col-6 col-xl-3"><div class="panel stat traffic-main"><div class="stat-icon" style="background:linear-gradient(135deg,#008f5a,#22d783)"><i class="bi bi-arrow-up"></i></div><div class="stat-body"><div class="stat-title">Total Upload</div><div class="stat-value up" id="kUpload">—</div><div class="traffic-selected-label mt-2">Mengikuti pilihan interface Download</div></div></div></div>
<div class="col-6 col-xl"><div class="panel stat"><div class="stat-icon" style="background:linear-gradient(135deg,#008f5a,#22d783)"><i class="bi bi-diagram-3-fill"></i></div><div class="stat-body"><div class="stat-title">Interface UP</div><div class="stat-value text-success" id="kIf">-</div><div class="stat-meta">interface terpilih</div></div></div></div>
<div class="col-6 col-xl"><div class="panel stat"><div class="stat-icon" style="background:linear-gradient(135deg,#6338e8,#9a63ff)"><i class="bi bi-people-fill"></i></div><div class="stat-body"><div class="stat-title">Aktif Berbayar</div><div class="stat-value" id="kPpp">-</div><div class="stat-meta" id="paidMeta">pelanggan online</div></div></div></div>
<div class="col-6 col-xl"><div class="panel stat"><div class="stat-icon" style="background:linear-gradient(135deg,#087c69,#18bba0)"><i class="bi bi-gift-fill"></i></div><div class="stat-body"><div class="stat-title">Aktif Gratis</div><div class="stat-value" id="kFree">-</div><div class="stat-meta">pelanggan online</div></div></div></div><div class="col-6 col-xl"><div class="panel stat"><div class="stat-icon" style="background:linear-gradient(135deg,#d9930e,#f5bb3a)"><i class="bi bi-person-x-fill"></i></div><div class="stat-body"><div class="stat-title">Pelanggan Offline</div><div class="stat-value warning" id="kOff">-</div><div class="stat-meta">akun offline</div></div></div></div>
<div class="col-6 col-xl"><div class="panel stat"><div class="stat-icon" style="background:linear-gradient(135deg,#9d273c,#ff5b68)"><i class="bi bi-speedometer2"></i></div><div class="stat-body"><div class="stat-title">Rata-rata Ping</div><div class="stat-value" id="kPing">-</div><div class="stat-meta">router + IP monitor</div></div></div></div>
</div>
<section class="panel mb-3" id="interfaces"><div class="section-head d-flex justify-content-between align-items-center"><div><div class="section-title"><i class="bi bi-bar-chart-fill text-info card-icon"></i>Grafik Traffic Interface</div></div><div class="d-flex gap-2 align-items-center"><span class="badge badge-soft" id="ifaceBadge">0 interface</span></div></div><div class="p-3 pt-2"><div id="interfaceGrid" class="row g-3"></div></div></section>
<section class="row g-3 mb-3" id="ping"><div class="col-xl-6 col-lg-6"><div class="panel h-100"><div class="section-head d-flex justify-content-between align-items-center"><div><div class="section-title"><i class="bi bi-heart-pulse-fill text-success card-icon"></i>Monitoring Ping Platform</div><small class="muted">Google, Cloudflare, YouTube, WhatsApp, Facebook, Instagram, TikTok, dan target lain.</small></div><div class="d-flex gap-2 align-items-center"><span class="badge badge-soft"><i class="bi bi-arrow-repeat me-1"></i>1 detik</span><button class="btn btn-sm btn-primary" onclick="openPing()"><i class="bi bi-plus-lg"></i> Tambah IP</button></div></div><div class="table-responsive ping-table-wrap"><table class="table table-hover table-sm align-middle mb-0"><thead><tr><th style="width:40px">#</th><th style="min-width:185px">Nama / Keterangan</th><th style="width:115px">IP / Host</th><th style="width:85px">Rata-rata</th><th style="width:75px">Terakhir</th><th style="width:75px">Status</th><th style="min-width:190px">Grafik Ping Realtime</th><th title="Loss dan ping tertinggi sejak halaman dibuka" style="width:165px;min-width:165px">Statistik <small class="muted d-block">sesi ini</small></th><th style="width:85px">Aksi</th></tr></thead><tbody id="pingBody"></tbody></table></div></div></div><div class="col-xl-6 col-lg-6"><div class="panel h-100 inactive-panel"><div class="section-head d-flex justify-content-between align-items-center"><div><div class="section-title"><i class="bi bi-person-x-fill text-danger card-icon"></i>PPPoE Offline &amp; Diblokir</div><small class="muted">Offline dan akun diblokir dipisahkan menurut profil atau status akun.</small></div><span class="badge text-bg-danger" id="inactiveCount">0</span></div><div style="padding:0 12px"><div class="pppoe-tabs" role="group" aria-label="Kategori PPPoE"><button type="button" class="btn btn-sm btn-outline-info" data-pppoe-tab="offline" aria-pressed="true">Offline <b id="offlineTabCount">0</b></button><button type="button" class="btn btn-sm btn-outline-warning" data-pppoe-tab="blocked" aria-pressed="false">Diblokir <b id="blockedTabCount">0</b></button></div></div><div class="panel-scroll"><table class="table table-hover table-sm mb-0 inactive-table"><thead><tr><th style="width:75px">Router</th><th>Nama</th><th>Profile</th><th style="width:155px">Last Logged Out</th><th style="width:145px">Durasi Offline</th><th>Reason</th></tr></thead><tbody id="inactiveBody"></tbody></table></div></div></div></section>
<section class="panel mb-3 active-panel" id="pppoe" hidden><div class="section-head d-flex justify-content-between align-items-center py-2"><div class="section-title"><i class="bi bi-person-check-fill text-success card-icon"></i>PPPoE Aktif</div><span class="badge text-bg-success" id="activeCount">0</span></div><div class="active-table-wrap"><table class="table table-hover table-sm mb-0 active-table"><thead><tr><th style="width:90px">Router</th><th>Nama</th><th style="width:130px">IP</th><th>Profile</th></tr></thead><tbody id="activeBody"></tbody></table></div></section>
<section class="panel mb-3" id="routers"><div class="section-head d-flex justify-content-between align-items-center"><div><div class="section-title"><i class="bi bi-router-fill text-info card-icon"></i>Daftar MikroTik</div><small class="muted">Kelola router dan interface yang dimonitor.</small></div><button class="btn btn-sm btn-primary" onclick="openRouter()"><i class="bi bi-plus"></i> Tambah MikroTik</button></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Status</th><th>Nama</th><th>IP</th><th>Ping</th><th>PPPoE</th><th>Interface</th><th>Aksi</th></tr></thead><tbody id="routerBody"></tbody></table></div></section>
<div class="text-center footer py-3"><i class="bi bi-shield-check me-1"></i> IONnet Network Monitoring · Bootstrap 5 · XAMPP · Realtime</div></main>

<div class="modal fade" id="routerModal" tabindex="-1"><div class="modal-dialog modal-lg"><form id="routerForm" class="modal-content"><div class="modal-header"><h5 id="routerTitle" class="modal-title"><i class="bi bi-router-fill me-2"></i>Tambah MikroTik</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="routerId" value="0"><div class="row g-3"><div class="col-md-6"><label class="form-label">Nama Router</label><input id="routerName" class="form-control" required></div><div class="col-md-6"><label class="form-label">IP / Host</label><input id="routerHost" class="form-control" required></div><div class="col-md-4"><label class="form-label">Port API</label><input id="routerPort" type="number" class="form-control" value="8728"></div><div class="col-md-8"><label class="form-label">Username API</label><input id="routerUser" class="form-control" required></div><div class="col-md-8"><label class="form-label">Password API</label><input id="routerPass" type="password" class="form-control"><div class="form-text text-secondary">Saat edit, kosongkan untuk mempertahankan password.</div></div><div class="col-md-4 d-flex align-items-end"><div class="form-check"><input id="routerSsl" class="form-check-input" type="checkbox"><label class="form-check-label">API-SSL</label></div></div></div><div id="routerMsg" class="mt-3"></div></div><div class="modal-footer"><button class="btn btn-outline-info" type="button" onclick="testRouterFromForm()"><i class="bi bi-plug"></i> Tes Koneksi</button><button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button></div></form></div></div>
<div class="modal fade" id="ifModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-sliders2 me-2"></i>Pilih Interface Monitoring</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="ifList"></div></div><div class="modal-footer"><button class="btn btn-primary" onclick="saveIfs()"><i class="bi bi-check2"></i> Simpan Interface</button></div></div></div></div>
<div class="modal fade" id="pingModal" tabindex="-1"><div class="modal-dialog"><form id="pingForm" class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-activity me-2"></i><span id="pingTitle">Tambah IP Monitor</span></h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="pingId" value="0"><div class="mb-3"><label class="form-label">Nama / Keterangan</label><input id="pingName" class="form-control" placeholder="Contoh: Gateway CORE" required></div><div class="mb-3"><label class="form-label">IP Address / Host</label><input id="pingHost" class="form-control" placeholder="192.0.2.1" required></div><div class="mb-3"><label class="form-label">Icon</label><select id="pingIcon" class="form-select"><option value="bi-hdd-network">Server / Network</option><option value="bi-globe2">Internet</option><option value="bi-cloud">Cloud</option><option value="bi-router">Router</option><option value="bi-database">Database</option><option value="bi-camera-video">CCTV</option><option value="bi-building">Site / Kantor</option><option value="bi-wifi">WiFi</option></select></div><div id="pingMsg"></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-info" onclick="testPingForm()"><i class="bi bi-broadcast"></i> Tes Ping</button><button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button></div></form></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const state={routers:[],pings:[],charts:{},pingCharts:{},ifHistory:{},pingHistory:{},ifRouter:0,loading:false,trafficSelection:localStorage.getItem("ionnetTrafficSelection")||"all"}; const rModal=new bootstrap.Modal(document.getElementById('routerModal')),iModal=new bootstrap.Modal(document.getElementById('ifModal')),pModal=new bootstrap.Modal(document.getElementById('pingModal'));
const $=id=>document.getElementById(id); const esc=s=>String(s??'').replace(/[&<>'"]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[m]));
function bits(v){v=Number(v||0);if(v>=1e9)return (v/1e9).toFixed(2)+' Gbps';if(v>=1e6)return (v/1e6).toFixed(1)+' Mbps';if(v>=1e3)return (v/1e3).toFixed(1)+' Kbps';return v.toFixed(0)+' bps'}
function openRouter(id=0){$('routerForm').reset();$('routerId').value=id;$('routerTitle').innerHTML='<i class="bi bi-router-fill me-2"></i>'+(id?'Edit MikroTik':'Tambah MikroTik');$('routerPort').value=8728;$('routerMsg').innerHTML='';if(id){const r=state.routers.find(x=>x.id==id)||{};$('routerName').value=r.name||'';$('routerHost').value=r.host||'';$('routerPort').value=r.port||8728;$('routerUser').value=r.username||'';}rModal.show()}
$('routerForm').addEventListener('submit',async e=>{e.preventDefault();const fd=new FormData();fd.append('id',$('routerId').value);fd.append('name',$('routerName').value);fd.append('host',$('routerHost').value);fd.append('port',$('routerPort').value||'8728');fd.append('username',$('routerUser').value);fd.append('password',$('routerPass').value);if($('routerSsl').checked)fd.append('use_ssl','1');fd.append('enabled','1');
 $('routerMsg').innerHTML='<div class="alert alert-info"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan MikroTik...</div>';
 try{const r=await fetch('actions/save_router.php',{method:'POST',body:fd,cache:'no-store'});const j=await r.json();if(j.ok){$('routerMsg').innerHTML='<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>MikroTik berhasil disimpan. Sekarang lakukan <b>Tes Koneksi</b> atau buka <b>Interface</b> untuk mendeteksi interface.</div>';setTimeout(()=>{rModal.hide();load()},700)}else $('routerMsg').innerHTML='<div class="alert alert-danger">'+esc(j.error||'Gagal menyimpan')+'</div>'}catch(e){$('routerMsg').innerHTML='<div class="alert alert-danger">Respons server tidak valid. Cek Apache/PHP/MySQL. '+esc(e.message)+'</div>'}
});
async function testRouterFromForm(){
 const fd=new FormData(); fd.append('host',$('routerHost').value.trim()); fd.append('port',$('routerPort').value||'8728'); fd.append('username',$('routerUser').value.trim()); fd.append('password',$('routerPass').value); if($('routerSsl').checked)fd.append('use_ssl','1');
 if(!fd.get('host')||!fd.get('username')){$('routerMsg').innerHTML='<div class="alert alert-warning">Isi IP/Host dan Username API terlebih dahulu.</div>';return}
 $('routerMsg').innerHTML='<div class="alert alert-info"><span class="spinner-border spinner-border-sm me-2"></span>Menguji koneksi API...</div>';
 try{const r=await fetch('actions/api.php?action=test_router_form',{method:'POST',body:fd,cache:'no-store'});const j=await r.json();$('routerMsg').innerHTML='<div class="alert '+(j.ok?'alert-success':'alert-danger')+'">'+esc(j.ok?'API OK · '+j.latency_ms+' ms · ROS '+(j.resource?.version||'-'):j.error||'Tes koneksi gagal')+'</div>'}catch(e){$('routerMsg').innerHTML='<div class="alert alert-danger">Tidak dapat membaca respons server: '+esc(e.message)+'</div>'}
}
async function routerDelete(id){if(!confirm('Hapus MikroTik ini?'))return;const fd=new FormData();fd.append('id',id);const j=await(await fetch('actions/delete_router.php',{method:'POST',body:fd})).json();if(j.ok)load()}
async function openIfs(id){state.ifRouter=id;$('ifList').innerHTML='<div class="text-secondary p-3"><span class="spinner-border spinner-border-sm me-2"></span>Memuat interface...</div>';iModal.show();const [a,b]=await Promise.all([fetch('actions/api.php?action=interfaces&router_id='+id).then(r=>r.json()),fetch('actions/api.php?action=selected_interfaces&router_id='+id).then(r=>r.json())]);const selected=new Map((b.data||[]).map(x=>[x.interface_name,x]));$('ifList').innerHTML=(a.data||[]).map(x=>{const s=selected.get(x.name);return `<div class="row align-items-center g-2 border-bottom border-secondary-subtle py-2"><div class="col-1"><input class="form-check-input ifcheck" type="checkbox" data-name="${esc(x.name)}" ${s?'checked':''}></div><div class="col-5"><strong>${esc(x.name)}</strong><div class="small text-secondary">${esc(x.type)} · ${x.running?'RUNNING':'DOWN'}</div></div><div class="col-6"><input class="form-control form-control-sm iflabel" data-name="${esc(x.name)}" value="${esc(s?.display_name||'')}" placeholder="Nama tampilan"></div></div>`}).join('')||'<div class="text-secondary p-3">Tidak ada interface.</div>'}
async function saveIfs(){const items=[...document.querySelectorAll('.ifcheck:checked')].map(c=>({name:c.dataset.name,label:document.querySelector(`.iflabel[data-name="${CSS.escape(c.dataset.name)}"]`)?.value||''}));const fd=new FormData();fd.append('router_id',state.ifRouter);fd.append('items',JSON.stringify(items));const j=await(await fetch('actions/save_interfaces.php',{method:'POST',body:fd})).json();if(j.ok){iModal.hide();load()}else alert(j.error)}
function openPing(id=0){$('pingForm').reset();$('pingId').value=id;$('pingTitle').textContent=id?'Edit IP Monitor':'Tambah IP Monitor';$('pingIcon').value='bi-hdd-network';$('pingMsg').innerHTML='';if(id){const t=state.pings.find(x=>x.id==id)||{};$('pingName').value=t.name||'';$('pingHost').value=t.host||'';$('pingIcon').value=t.icon||'bi-hdd-network';}pModal.show()}
$('pingForm').addEventListener('submit',async e=>{e.preventDefault();const fd=new FormData();fd.append('id',$('pingId').value);fd.append('name',$('pingName').value);fd.append('host',$('pingHost').value);fd.append('icon',$('pingIcon').value);fd.append('enabled','1');const j=await(await fetch('actions/save_ping.php',{method:'POST',body:fd})).json();if(j.ok){pModal.hide();load()}else $('pingMsg').innerHTML='<div class="alert alert-danger">'+esc(j.error)+'</div>'});
async function testPingForm(){const host=$('pingHost').value.trim();if(!host){$('pingMsg').innerHTML='<div class="alert alert-warning">Masukkan IP/host.</div>';return}const fd=new FormData();fd.append('host',host);const j=await(await fetch('actions/api.php?action=test_ping_form',{method:'POST',body:fd})).json();$('pingMsg').innerHTML='<div class="alert '+(j.data?.ok?'alert-success':'alert-danger')+'">'+esc(j.data?.ok?('Reply '+j.data.latency_ms+' ms'):'Ping timeout')+'</div>'}
async function pingDelete(id){if(!confirm('Hapus target ping ini?'))return;const fd=new FormData();fd.append('id',id);const j=await(await fetch('actions/delete_ping.php',{method:'POST',body:fd})).json();if(j.ok)load()}
function ifKey(x){return `r${x.router_id||x.routerId||0}_i${x.id||x.name}`.replace(/[^a-zA-Z0-9_-]/g,'_')}
function graphTime(ts){return new Date(ts).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'})}
function chartAxisBits(v){v=Number(v||0);if(v>=1e9)return (v/1e9).toFixed(v>=10e9?0:1)+'G';if(v>=1e6)return (v/1e6).toFixed(v>=10e6?0:1)+'M';if(v>=1e3)return (v/1e3).toFixed(v>=10e3?0:1)+'K';return Math.round(v)+'b'}
function avg(arr){const a=arr.filter(v=>Number.isFinite(v));return a.length?a.reduce((x,y)=>x+y,0)/a.length:0}
function initIfChart(key,canvas){const h=state.ifHistory[key]||{labels:[],rx:[],tx:[]};const c=new Chart(canvas,{type:'line',data:{labels:h.labels,datasets:[{label:'RX',data:h.rx,borderColor:'#20a5ff',backgroundColor:'rgba(32,165,255,.16)',borderWidth:1.8,fill:true,pointRadius:0,tension:0},{label:'TX',data:h.tx,borderColor:'#22d783',backgroundColor:'rgba(34,215,131,.10)',borderWidth:1.8,fill:true,pointRadius:0,tension:0}]},options:{responsive:true,maintainAspectRatio:false,animation:false,interaction:{mode:'index',intersect:false},plugins:{legend:{display:false},tooltip:{displayColors:true,callbacks:{label:ctx=>`${ctx.dataset.label}: ${bits(ctx.parsed.y)}`}}},scales:{x:{ticks:{color:'#6f8798',maxTicksLimit:7,font:{size:9}},grid:{color:'rgba(100,130,150,.12)'},border:{color:'#2b465b'}},y:{beginAtZero:true,suggestedMax:1,ticks:{color:'#8098aa',maxTicksLimit:5,font:{size:9},callback:chartAxisBits},grid:{color:'rgba(100,130,150,.13)'},border:{color:'#2b465b'}}}}});state.charts[key]=c;return c}
function updateIfChart(x){const key=ifKey(x);if(!state.ifHistory[key])state.ifHistory[key]={labels:[],rx:[],tx:[]};const h=state.ifHistory[key],now=Date.now();h.labels.push(graphTime(now));h.rx.push(Number(x.rx_bps||0));h.tx.push(Number(x.tx_bps||0));while(h.labels.length>60){h.labels.shift();h.rx.shift();h.tx.shift();}const c=state.charts[key]||initIfChart(key,$('canvas_'+key));c.data.labels=h.labels;c.data.datasets[0].data=h.rx;c.data.datasets[1].data=h.tx;const peak=Math.max(...h.rx,...h.tx,1);c.options.scales.y.suggestedMax=peak<=1000?1000:peak*1.18;c.update('none');const card=$('card_'+key);if(card){$('rx_'+key).textContent=bits(x.rx_bps);$('tx_'+key).textContent=bits(x.tx_bps);$('ppsrx_'+key).textContent=(Number(x.rx_pps||0)).toLocaleString('en-US');$('ppstx_'+key).textContent=(Number(x.tx_pps||0)).toLocaleString('en-US');$('avg_'+key).textContent=bits(avg([...h.rx,...h.tx]));$('peak_'+key).textContent=bits(peak);const badge=$('badge_'+key);badge.className='badge '+(x.running?'text-bg-success':'text-bg-danger');badge.textContent=x.running?'UP':'DOWN';}}
function renderInterfaces(all){const wanted=new Set(all.map(x=>ifKey(x)));$('ifaceBadge').textContent=all.length+' interface';const grid=$('interfaceGrid');if(!all.length){Object.keys(state.charts).forEach(k=>{state.charts[k]?.destroy();delete state.charts[k];});grid.innerHTML='<div class="col-12 text-center muted py-5"><i class="bi bi-bar-chart fs-2 d-block mb-2"></i>Pilih interface dari menu MikroTik.</div>';return;}Object.keys(state.charts).forEach(k=>{if(!wanted.has(k)){state.charts[k]?.destroy();delete state.charts[k];delete state.ifHistory[k];}});const existing=new Set([...grid.querySelectorAll('[data-if-card]')].map(n=>n.dataset.ifCard));all.forEach(x=>{const key=ifKey(x);if(!existing.has(key)){const div=document.createElement('div');div.className='col-md-6 col-xl-4 col-xxl-3';div.dataset.ifCard=key;div.innerHTML=`<div class="panel chart-card" id="card_${key}"><div class="chart-meta"><div class="chart-title"><i class="bi bi-ethernet text-info me-1"></i>${esc(x.label||x.name)}</div><span id="badge_${key}" class="badge ${x.running?'text-bg-success':'text-bg-danger'}">${x.running?'UP':'DOWN'}</span></div><div class="chart-sub"><i class="bi bi-router me-1"></i>${esc(x.router_name)} · ${esc(x.name)}</div><div class="speed mt-2"><span class="down">RX <b id="rx_${key}">${bits(x.rx_bps)}</b></span><span class="up">TX <b id="tx_${key}">${bits(x.tx_bps)}</b></span></div><div class="graph-legend"><span><span class="legend-dot rx-dot"></span>RX</span><span><span class="legend-dot tx-dot"></span>TX</span><span class="ms-auto muted">1s</span></div><div class="chart-wrap"><canvas id="canvas_${key}"></canvas></div><div class="chart-statbar"><span>Avg <strong id="avg_${key}">-</strong></span><span>Peak <strong id="peak_${key}">-</strong></span><span>RX pps <strong id="ppsrx_${key}">0</strong></span><span>TX pps <strong id="ppstx_${key}">0</strong></span></div></div>`;grid.appendChild(div);initIfChart(key,$('canvas_'+key));}});all.forEach(updateIfChart)}
function parseLogoutTime(v){
 const s=String(v||'').trim(); if(!s) return 0;
 const normalized=s.replace('T',' ').replace(/\.\d+Z?$/,'').replace(/Z$/,'');
 let t=Date.parse(normalized.replace(/-/g,'/'));
 if(Number.isNaN(t)){const m=normalized.match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?/); if(m)t=new Date(+m[1],+m[2]-1,+m[3],+m[4],+m[5],+(m[6]||0)).getTime();}
 return Number.isFinite(t)?t:0;
}
function formatOfflineDuration(v){ const t=parseLogoutTime(v); if(!t) return '—'; let sec=Math.max(0,Math.floor((Date.now()-t)/1000)); const d=Math.floor(sec/86400); sec%=86400; const h=Math.floor(sec/3600); sec%=3600; const m=Math.floor(sec/60); const s=sec%60; const parts=[]; if(d)parts.push(d+'h'); parts.push(String(h).padStart(2,'0')+'j',String(m).padStart(2,'0')+'m',String(s).padStart(2,'0')+'d'); return parts.join(' ');}
function updateOfflineDurations(){document.querySelectorAll('.offline-duration').forEach(el=>{el.textContent=formatOfflineDuration(el.dataset.offlineTime||'');});}
setInterval(updateOfflineDurations,1000);
function pingKey(x,i){return ('p'+(x.id??i)).replace(/[^a-zA-Z0-9_-]/g,'_')}
function mediaIcon(name, fallback){const n=(name||'').toLowerCase();if(n.includes('youtube'))return 'bi-youtube';if(n.includes('whatsapp'))return 'bi-whatsapp';if(n.includes('facebook'))return 'bi-facebook';if(n.includes('instagram'))return 'bi-instagram';if(n.includes('tiktok'))return 'bi-tiktok';if(n.includes('telegram'))return 'bi-telegram';if(n.includes('google'))return 'bi-google';if(n.includes('cloudflare'))return 'bi-cloud-fill';if(n.includes('netflix'))return 'bi-play-circle-fill';if(n.includes('x ' )||n==='x'||n.includes('twitter'))return 'bi-twitter-x';return fallback||'bi-globe2';}
function initPingChart(key,canvas){const h=state.pingHistory[key]||{labels:[],values:[]};const c=new Chart(canvas,{type:'line',data:{labels:h.labels,datasets:[{data:h.values,borderColor:'#22d783',backgroundColor:'rgba(34,215,131,.08)',borderWidth:1.5,pointRadius:0,tension:.16,fill:true}]},options:{responsive:true,maintainAspectRatio:false,animation:false,plugins:{legend:{display:false},tooltip:{enabled:false}},scales:{x:{display:false},y:{display:false,beginAtZero:true}}}});state.pingCharts[key]=c;return c}
function renderPing(data){
 const body=$('pingBody');
 if(!data.length){body.innerHTML='<tr><td colspan="9" class="text-center muted py-4"><i class="bi bi-activity fs-4 d-block mb-2"></i>Belum ada target ping.</td></tr>';return;}
 const wanted=[]; const existing=new Set([...body.querySelectorAll('[data-ping-row]')].map(n=>n.dataset.pingRow));
 data.forEach((x,i)=>{
   const key=pingKey(x,i); wanted.push(key);
   if(!existing.has(key)){
     const tr=document.createElement('tr'); tr.dataset.pingRow=key;
     const icon=esc(x.icon||mediaIcon(x.name));
     const actions=x.source==='router' ? '<span class="text-secondary"><i class="bi bi-router"></i></span>' : '<button class="btn btn-sm btn-outline-info" onclick="openPing('+Number(x.id)+')"><i class="bi bi-pencil"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="pingDelete('+Number(x.id)+')"><i class="bi bi-trash"></i></button>';
     tr.innerHTML='<td>'+(i+1)+'</td>'+
       '<td><div class="d-flex align-items-center gap-2"><span class="ping-media-icon"><i class="bi '+icon+'"></i></span><div><div class="ping-table-name" id="pname_'+key+'"></div><div class="ping-table-host" id="phost_'+key+'"></div></div></div></td>'+
       '<td class="smallmono">'+esc(x.host)+'</td>'+
       '<td><span class="ping-avg" id="pavg_'+key+'">—</span></td>'+
       '<td><span class="ping-last" id="plast_'+key+'">—</span></td>'+
       '<td><span id="pstatus_'+key+'" class="ping-row-status"><span class="status-dot status-offline"></span>Offline</span></td>'+
       '<td><div class="d-flex align-items-center gap-2"><div class="ping-inline-chart"><canvas id="pcv_'+key+'"></canvas></div><span class="small fw-bold" id="pnow_'+key+'">—</span></div></td>'+
       '<td><div class="ping-stat-roll" title="Loss dan ping tertinggi sejak halaman dibuka"><div class="ping-stat-track"><div class="ping-stat-item"><small>Loss</small><b class="warning" id="ploss_'+key+'">0</b></div><div class="ping-stat-item"><small>Tertinggi</small><b id="ppeak_'+key+'">—</b></div></div></div></td>'+
       '<td class="ping-action">'+actions+'</td>';
     body.appendChild(tr);
   }
 });
 [...body.querySelectorAll('[data-ping-row]')].forEach(tr=>{if(!wanted.includes(tr.dataset.pingRow)){const k=tr.dataset.pingRow;state.pingCharts[k]?.destroy();delete state.pingCharts[k];delete state.pingHistory[k];tr.remove();}});
 data.forEach((x,i)=>{
   const key=pingKey(x,i), p=x.ping||{};
   if(!state.pingHistory[key])state.pingHistory[key]={labels:[],values:[]};
   const h=state.pingHistory[key], val=p.ok?Number(p.latency_ms):0;
   h.lossCount=(h.lossCount||0)+(p.ok?0:1);
   if(p.ok&&Number.isFinite(val))h.peakMs=h.peakMs==null?val:Math.max(h.peakMs,val);
   $('ploss_'+key).textContent=h.lossCount+' kali';
   $('ppeak_'+key).textContent=h.peakMs==null?'—':h.peakMs.toFixed(1)+' ms';
   h.labels.push(graphTime(Date.now())); h.values.push(val); while(h.labels.length>60){h.labels.shift();h.values.shift();}
   const good=h.values.filter(v=>v>0), av=good.length?avg(good):0;
   $('pname_'+key).textContent=x.name; $('phost_'+key).textContent=x.host;
   $('pavg_'+key).textContent=good.length?av.toFixed(1)+' ms':'—'; $('plast_'+key).textContent=p.ok?val.toFixed(1)+' ms':'Timeout'; $('pnow_'+key).textContent=p.ok?val.toFixed(1)+' ms':'—';
   const st=$('pstatus_'+key); st.innerHTML=p.ok?'<span class="status-dot status-online"></span>Online':'<span class="status-dot status-offline"></span>Offline'; st.className='ping-row-status '+(p.ok?'up':'danger');
   let c=state.pingCharts[key]; if(!c)c=initPingChart(key,$('pcv_'+key));
   c.data.labels=h.labels; c.data.datasets[0].data=h.values; c.data.datasets[0].borderColor=p.ok?'#22d783':'#ff5b68'; c.data.datasets[0].backgroundColor=p.ok?'rgba(34,215,131,.08)':'rgba(255,91,104,.08)'; c.options.scales.y.suggestedMax=Math.max(10,Math.max(...h.values,1)*1.2); c.update('none');
 });
}
function renderRouters(){ $('routerBody').innerHTML=state.routers.map(r=>`<tr><td><span class="status-dot ${r.online?'status-online':'status-offline'}"></span>${r.online?'Online':'Offline'}</td><td><i class="bi bi-router-fill text-info me-1"></i>${esc(r.name)}</td><td class="smallmono">${esc(r.host)}</td><td class="${r.ping?.ok?'up':'danger'}">${r.ping?.latency_ms!=null?r.ping.latency_ms+' ms':'Timeout'}</td><td>${r.pppoe_count??0} / ${r.pppoe_inactive_count??0}</td><td>${(r.interfaces||[]).length}</td><td><button class="btn btn-sm btn-outline-info" onclick="openIfs(${r.id})"><i class="bi bi-sliders2"></i></button> <button class="btn btn-sm btn-outline-light" onclick="openRouter(${r.id})"><i class="bi bi-pencil"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="routerDelete(${r.id})"><i class="bi bi-trash"></i></button></td></tr>`).join('')||'<tr><td colspan="7" class="text-center muted py-4">Belum ada MikroTik.</td></tr>'; }
function renderSummary(){ $('routerSummary').innerHTML=state.routers.map(r=>`<div class="ping-row"><div class="d-flex justify-content-between align-items-center"><div><span class="status-dot ${r.online?'status-online':'status-offline'}"></span><strong>${esc(r.name)}</strong><div class="small muted ms-3">${esc(r.host)}</div></div><div class="text-end"><div class="fw-bold ${r.ping?.ok?'up':'danger'}">${r.ping?.latency_ms!=null?r.ping.latency_ms+' ms':'Timeout'}</div><div class="small muted">PPPoE ${r.pppoe_count??0}</div></div></div></div>`).join('')||'<div class="text-center muted p-4">Belum ada router.</div>' }
function fillTrafficSelect(all){
 const sel=$('trafficSelect'); if(!sel)return;
 const current=state.trafficSelection;
 const options=['<option value="all">Semua Interface</option>'];
 all.forEach(x=>{const k=ifKey(x); options.push(`<option value="${esc(k)}">${esc(x.router_name)} · ${esc(x.label||x.name)}</option>`)});
 sel.innerHTML=options.join('');
 const exists=all.some(x=>ifKey(x)===current);
 state.trafficSelection=exists?current:'all'; sel.value=state.trafficSelection;
 const chosen=all.find(x=>ifKey(x)===state.trafficSelection);
 $('trafficSelectedLabel').textContent=chosen?`RX + TX · ${chosen.router_name} · ${chosen.label||chosen.name}`:'RX + TX realtime · Semua Interface';
}
function updateSelectedTraffic(all){
 const sel=state.trafficSelection; let total=0; let chosen=null;
 if(sel==='all'){all.forEach(x=>total+=Number(x.rx_bps||0)+Number(x.tx_bps||0));}
 else {chosen=all.find(x=>ifKey(x)===sel); if(chosen) total=Number(chosen.rx_bps||0)+Number(chosen.tx_bps||0); else {state.trafficSelection='all'; localStorage.setItem('ionnetTrafficSelection','all'); all.forEach(x=>total+=Number(x.rx_bps||0)+Number(x.tx_bps||0));}}
 const selected=state.trafficSelection==='all'?all:all.filter(x=>ifKey(x)===state.trafficSelection);
 $('kDownload').textContent=bits(selected.reduce((sum,x)=>sum+Number(x.rx_bps||0),0));
 $('kUpload').textContent=bits(selected.reduce((sum,x)=>sum+Number(x.tx_bps||0),0));
 $('kTraffic').textContent=bits(total); if($('trafficSelect'))$('trafficSelect').value=state.trafficSelection; $('trafficSelectedLabel').textContent=chosen?`RX + TX · ${chosen.router_name} · ${chosen.label||chosen.name}`:'RX + TX realtime · Semua Interface';
}
function onTrafficSelectionChange(){state.trafficSelection=$('trafficSelect').value||'all';localStorage.setItem('ionnetTrafficSelection',state.trafficSelection);updateSelectedTraffic(state._allInterfaces||[]);}
let pppoeCategory='offline';
function renderPppoeCategory(){const groups=HngPppoeSplit.split(state._inactiveRows||[]);const rows=groups[pppoeCategory].slice();$('offlineTabCount').textContent=groups.offline.length;$('blockedTabCount').textContent=groups.blocked.length;$('inactiveCount').textContent=rows.length;
rows.sort((a,b)=>{const ta=parseLogoutTime(a.last_logged_out),tb=parseLogoutTime(b.last_logged_out); return (tb||0)-(ta||0);});
$('inactiveBody').innerHTML=rows.map(x=>`<tr><td>${esc(x.router)}</td><td class="fw-bold">${esc(x.name)}</td><td>${esc(x.profile||'')}</td><td>${esc(x.last_logged_out||'-')}</td><td class="offline-duration" data-offline-time="${esc(x.last_logged_out||'')}">${formatOfflineDuration(x.last_logged_out)}</td><td>${esc(x.last_disconnect_reason||'-')}</td></tr>`).join('')||'<tr><td colspan="6" class="text-center muted py-4">Tidak ada akun pada kategori ini</td></tr>'}
document.querySelectorAll('[data-pppoe-tab]').forEach(button=>button.addEventListener('click',()=>{pppoeCategory=button.dataset.pppoeTab;document.querySelectorAll('[data-pppoe-tab]').forEach(b=>b.setAttribute('aria-pressed',String(b===button)));renderPppoeCategory();}));
async function load(){if(state.loading)return;state.loading=true;try{const [a,p]=await Promise.all([fetch('actions/api.php?action=routers&ts='+Date.now(),{cache:'no-store'}).then(r=>r.json()),fetch('actions/api.php?action=ping_targets&ts='+Date.now(),{cache:'no-store'}).then(r=>r.json())]);state.routers=a.data||[];let allIf=[],up=0,ppp=0,off=0,pls=[];const act=[],ina=[];state.routers.forEach(r=>{ppp+=Number(r.pppoe_count||0);off+=Number(r.pppoe_inactive_count||0);if(r.ping?.ok)pls.push(Number(r.ping.latency_ms));(r.interfaces||[]).forEach(x=>{allIf.push({...x,router_name:r.name});if(x.running)up++});(r.pppoe||[]).forEach(x=>act.push({router:r.name,...x}));(r.pppoe_inactive||[]).forEach(x=>ina.push({router:r.name,...x}));});state._allInterfaces=allIf;fillTrafficSelect(allIf);updateSelectedTraffic(allIf);$('kIf').textContent=up;const activeSummary=HngPppoeActive.summary(act);$('kPpp').textContent=activeSummary.paid.toLocaleString('id-ID');$('kFree').textContent=activeSummary.free.toLocaleString('id-ID');$('paidMeta').textContent=activeSummary.unknown?activeSummary.unknown+' profil belum diketahui':'pelanggan online';const separated=HngPppoeSplit.split(ina);off=separated.offline.length;$('kOff').textContent=off.toLocaleString('id-ID');window.updatePppoeOffAlarm?.(off,a.ok===true&&Array.isArray(a.data)&&a.data.length>0&&a.data.every(r=>r.online===true&&Number.isFinite(Number(r.pppoe_inactive_count))));const pingData=p.data||[];pingData.forEach(x=>{if(x.ping?.ok)pls.push(Number(x.ping.latency_ms))});$('kPing').textContent=pls.length?(pls.reduce((a,b)=>a+b,0)/pls.length).toFixed(1)+' ms':'—';$('activeCount').textContent=act.length;$('inactiveCount').textContent=ina.length;renderInterfaces(allIf);renderPing(pingData);renderRouters();$('activeBody').innerHTML=act.map(x=>`<tr><td>${esc(x.router)}</td><td>${esc(x.name)}</td><td class="smallmono">${esc(x.address||'')}</td><td>${esc(x.profile||'')}</td></tr>`).join('')||'<tr><td colspan="4" class="text-center muted py-4">Tidak ada PPPoE aktif</td></tr>';
state._inactiveRows=ina;renderPppoeCategory();;}catch(e){window.updatePppoeOffAlarm?.(NaN,false);console.error(e)}finally{state.loading=false}}
document.addEventListener('change',e=>{if(e.target&&e.target.id==='trafficSelect')onTrafficSelectionChange();}); load();setInterval(load,1000);
</script><script src="traffic-charts.js?v=20261103b"></script><script src="priority-ping.js?v=20261102c"></script><script src="sfp-cards.js?v=20261103b"></script><script src="pppoe-off-alarm.js?v=20261103"></script></body></html>
