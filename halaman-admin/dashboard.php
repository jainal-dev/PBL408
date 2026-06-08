<?php require_once __DIR__ . '/admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Monitoring Parkir</title>

<link rel="stylesheet" href="css/dashboard.css?v=4">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-top">

            <!-- LOGO -->
            <div class="logo">

                <div class="logo-icon">
                    <i class="fa-solid fa-car"></i>
                </div>

                <div>
                    <h2>SPARK</h2>
                    <p>SISTEM MONITORING PARKIR</p>
                </div>

            </div>

            <!-- MENU -->
            <nav class="menu">

                <a href="dashboard.php" class="active">
                    <i class="fa-solid fa-house"></i>
                    Beranda
                </a>

                <a href="userreports.php">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Laporan
                </a>

                <a href="manajemen-slot.php">
                    <i class="fa-solid fa-square-parking"></i>
                    Manajemen Slot
                </a>

                <a href="pengguna.php">
                    <i class="fa-solid fa-users"></i>
                    Pengguna
                </a>

                <a href="pengaturan.php">
                    <i class="fa-solid fa-gear"></i>
                    Pengaturan
                </a>

            </nav>

        </div>

        <!-- BOTTOM -->
        <div class="sidebar-bottom">

            <!-- ALERT -->
            <div class="alert-box">

                <p>PERINGATAN SISTEM</p>

                <h3>2 Sensor Offline di Zona B</h3>

                <button>CEK SEKARANG</button>

            </div>

            <!-- LOGOUT -->
            <button class="logout-btn"
                onclick="fetch('../spark/api/logout.php').finally(() => location.href = '../spark/login.php')">

                <i class="fa-solid fa-right-from-bracket"></i>

                Keluar

            </button>

        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text"
                placeholder="Cari zona parkir, sensor, atau laporan...">

            </div>

            <div class="top-icons">

                <i class="fa-regular fa-bell"></i>

                <i class="fa-solid fa-gear"></i>

                <img src="https://i.pravatar.cc/100" alt="">

            </div>

        </div>

        <!-- TITLE -->
        <div class="title-section">

            <div>

                <h1>Dashboard Monitoring</h1>

                <p>
                    Pemantauan kondisi parkir dan sensor secara realtime.
                </p>

            </div>

            <button class="export-btn">

                <i class="fa-solid fa-download"></i>

                Export Laporan

            </button>

        </div>

        <!-- STATS -->
        <div class="stats-grid">

            <div class="card">

                <div class="card-top">

                    <div class="mini-icon blue">
                        <i class="fa-solid fa-square-parking"></i>
                    </div>

                    <span class="status stable">
                        Stabil
                    </span>

                </div>

                <p class="label">TOTAL SLOT</p>

                <h2>12,482</h2>

            </div>

            <div class="card">

                <p class="label">SLOT TERISI</p>

                <div class="flex-row">

                    <h2>8,194</h2>

                    <span class="green">
                        +4.2%
                    </span>

                </div>

                <div class="progress">
                    <div class="fill"></div>
                </div>

                <small>65.6% Tingkat Penggunaan</small>

            </div>

            <div class="card">

                <div class="mini-icon green-bg">
                    <i class="fa-solid fa-wifi"></i>
                </div>

                <p class="label">SENSOR AKTIF</p>

                <h2>99.8%</h2>

                <small>Dari 14.000 Sensor</small>

            </div>

            <div class="card">

                <div class="card-top">

                    <div class="mini-icon red-bg">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>

                    <span class="status danger">
                        PERLU TINDAKAN
                    </span>

                </div>

                <p class="label">LAPORAN MASUK</p>

                <h2 id="statLaporan">0</h2>

            </div>

        </div>

        <!-- GRAFIK PENGGUNAAN PARKIR -->
        <section class="chart-section">

            <div class="chart-header">

                <div>
                    <h2>Grafik Penggunaan Parkir</h2>
                    <p id="chartSub">Jumlah laporan per jam (24 jam terakhir)</p>
                </div>

                <div class="toggle">
                    <button id="btnHarian" class="active-btn" onclick="setPeriode('harian', this)">
                        Harian
                    </button>
                    <button id="btnMingguan" onclick="setPeriode('mingguan', this)">
                        Mingguan
                    </button>
                </div>

            </div>

            <div class="bar-chart" id="barChart">
                <div class="bar-empty">Memuat grafik...</div>
            </div>

        </section>

        <!-- STATUS SLOT + LIST LAPORAN -->
        <div class="content-grid">

            <!-- STATUS SLOT PARKIR -->
            <section class="panel">

                <h2 class="panel-title">Status Slot Parkir</h2>

                <div id="slotStatus" class="slot-status">
                    <div class="bar-empty">Memuat...</div>
                </div>

            </section>

            <!-- LIST LAPORAN -->
            <aside class="panel">

                <div class="panel-head">
                    <h2 class="panel-title">Laporan Masuk</h2>
                    <select id="filterLokasi" class="filter-lokasi" onchange="renderLaporanFiltered()">
                        <option value="">Semua Lokasi</option>
                    </select>
                </div>

                <div id="laporanList" class="laporan-list">
                    <div class="bar-empty">Memuat...</div>
                </div>

            </aside>

        </div>

    </main>

</div>

<script>

let statsData = null;
let semuaLaporan = [];

async function loadStats() {
  try {
    const res = await fetch('api/stats.php');
    const json = await res.json();
    if (!json.success) return;

    statsData = json;

    const periodeAktif = document.getElementById('btnMingguan').classList.contains('active-btn')
      ? 'mingguan' : 'harian';

    renderChart(periodeAktif);
    renderSlot(json.slot);

    semuaLaporan = json.laporan;
    populateFilter(json.slot);
    renderLaporanFiltered();

    document.getElementById('statLaporan').textContent = json.laporan.length;
  } catch (e) {
    console.error(e);
  }
}

function setPeriode(p, btn) {
  document.querySelectorAll('.chart-header .toggle button')
    .forEach(b => b.classList.remove('active-btn'));
  btn.classList.add('active-btn');

  document.getElementById('chartSub').textContent =
    p === 'harian'
      ? 'Jumlah laporan per jam (24 jam terakhir)'
      : 'Jumlah laporan per hari (7 hari terakhir)';

  renderChart(p);
}

function renderChart(p) {
  if (!statsData) return;

  const data = statsData[p];
  const box = document.getElementById('barChart');

  const ada = data.some(d => d.jumlah > 0);
  if (!ada) {
    box.innerHTML = '<div class="bar-empty">Belum ada data penggunaan pada periode ini</div>';
    return;
  }

  const max = Math.max(...data.map(d => d.jumlah));

  box.innerHTML = data.map(d => `
    <div class="bar-col">
      <div class="bar-wrap">
        <div class="bar" style="height:${(d.jumlah / max * 100).toFixed(1)}%" title="${d.jumlah} laporan"></div>
      </div>
      <span class="bar-label">${d.label}</span>
    </div>
  `).join('');
}

function renderSlot(slot) {
  const box = document.getElementById('slotStatus');

  if (!slot || !slot.length) {
    box.innerHTML = '<div class="bar-empty">Tidak ada data slot</div>';
    return;
  }

  box.innerHTML = slot.map(s => {
    const kosong = +s.kosong, terisi = +s.terisi, rusak = +s.rusak, total = +s.total;
    const pct = total ? Math.round(terisi / total * 100) : 0;

    return `
      <div class="slot-row">
        <div class="slot-row-top">
          <strong>${esc(s.lokasi)}</strong>
          <span class="slot-pct">${pct}% terisi</span>
        </div>
        <div class="slot-bar">
          <div class="slot-bar-fill" style="width:${pct}%"></div>
        </div>
        <div class="slot-legend">
          <span><i class="dot kosong"></i>${kosong} kosong</span>
          <span><i class="dot terisi"></i>${terisi} terisi</span>
          <span><i class="dot rusak"></i>${rusak} rusak</span>
        </div>
      </div>`;
  }).join('');
}

function populateFilter(slot) {
  const sel = document.getElementById('filterLokasi');
  if (sel.dataset.filled) return; // isi sekali saja, jangan reset pilihan saat auto-refresh

  sel.innerHTML = '<option value="">Semua Lokasi</option>' +
    slot.map(s => `<option value="${esc(s.lokasi)}">${esc(s.lokasi)}</option>`).join('');

  sel.dataset.filled = '1';
}

function renderLaporanFiltered() {
  const lok = document.getElementById('filterLokasi').value;
  const list = lok ? semuaLaporan.filter(l => l.lokasi === lok) : semuaLaporan;
  renderLaporan(list);
}

function renderLaporan(list) {
  const box = document.getElementById('laporanList');

  if (!list || !list.length) {
    box.innerHTML = '<div class="bar-empty">Belum ada laporan</div>';
    return;
  }

  box.innerHTML = list.map(l => `
    <div class="lap-row">
      <div class="lap-main">
        <strong>${esc(l.lokasi)} - Slot ${esc(l.nomor_slot)}</strong>
        <span class="lap-badge ${l.kondisi_dilaporkan}">${esc(l.kondisi_dilaporkan)}</span>
      </div>
      <p class="lap-desc">${esc(l.deskripsi || '-')}</p>
      <small class="lap-meta">oleh ${esc(l.pelapor)} · ${fmtTanggal(l.waktu_laporan)}</small>
    </div>
  `).join('');
}

function esc(s) {
  return (s == null ? '' : String(s)).replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[m]));
}

function fmtTanggal(w) {
  if (!w) return '-';
  const d = new Date(w.replace(' ', 'T'));
  if (isNaN(d)) return w;
  return d.toLocaleString('id-ID', {
    day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit'
  });
}

loadStats();
setInterval(loadStats, 30000);

</script>

</body>
</html>
