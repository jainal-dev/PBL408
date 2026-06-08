<?php require_once __DIR__ . '/admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan Pengguna</title>

<link rel="stylesheet" href="css/dashboard.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-top">

            <div class="logo">
                <div class="logo-icon"><i class="fa-solid fa-car"></i></div>
                <div>
                    <h2>SPARK</h2>
                    <p>SISTEM MONITORING PARKIR</p>
                </div>
            </div>

            <nav class="menu">
                <a href="dashboard.php">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="userreports.php" class="active">
                    <i class="fa-solid fa-clipboard-list"></i> Laporan
                </a>
                <a href="manajemen-slot.php">
                    <i class="fa-solid fa-square-parking"></i> Manajemen Slot
                </a>
                <a href="pengguna.php">
                    <i class="fa-solid fa-users"></i> Pengguna
                </a>
                <a href="pengaturan.php">
                    <i class="fa-solid fa-gear"></i> Pengaturan
                </a>
            </nav>

        </div>

        <div class="sidebar-bottom">
            <div class="alert-box">
                <p>PERINGATAN SISTEM</p>
                <h3>2 Sensor Offline di Zona B</h3>
                <button>CEK SEKARANG</button>
            </div>
            <button class="logout-btn"
                onclick="fetch('../spark/api/logout.php').finally(() => location.href = '../spark/login.php')">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </button>
        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari pelapor, lokasi, atau deskripsi..."
                    oninput="renderFiltered()">
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
                <h1>Laporan Pengguna</h1>
                <p>Monitoring laporan crowdsourcing beserta hasil validasi antar pengguna.</p>
            </div>
            <span class="lap-total" id="lapTotal">0 laporan</span>
        </div>

        <!-- FILTER -->
        <div class="lap-filter-bar">

            <div class="lap-tabs" id="lapTabs">
                <button class="lap-tab active-btn" data-kondisi=""       onclick="setKondisi('', this)">Semua</button>
                <button class="lap-tab"            data-kondisi="kosong" onclick="setKondisi('kosong', this)">Kosong</button>
                <button class="lap-tab"            data-kondisi="terisi" onclick="setKondisi('terisi', this)">Terisi</button>
                <button class="lap-tab"            data-kondisi="rusak"  onclick="setKondisi('rusak', this)">Rusak</button>
            </div>

            <select id="filterLokasi" class="filter-lokasi" onchange="renderFiltered()">
                <option value="">Semua Lokasi</option>
            </select>

        </div>

        <!-- TABEL -->
        <section class="panel">
            <div class="table-wrap">
                <table class="user-table" id="lapTable">
                    <thead>
                        <tr>
                            <th>Pelapor</th>
                            <th>Lokasi &middot; Slot</th>
                            <th>Kondisi</th>
                            <th>Deskripsi</th>
                            <th>Validasi</th>
                        </tr>
                    </thead>
                    <tbody id="lapBody">
                        <tr><td colspan="5" class="bar-empty">Memuat laporan...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</div>

<script>

let semuaLaporan = [];
let kondisiAktif = '';

async function loadLaporan() {
  try {
    const res = await fetch('api/laporan.php');
    const json = await res.json();
    if (!json.success) {
      document.getElementById('lapBody').innerHTML =
        `<tr><td colspan="5" class="bar-empty">${esc(json.message || 'Gagal memuat')}</td></tr>`;
      return;
    }

    semuaLaporan = json.data;
    populateFilter(json.lokasi || []);
    renderFiltered();

  } catch (e) {
    console.error(e);
    document.getElementById('lapBody').innerHTML =
      '<tr><td colspan="5" class="bar-empty">Tidak dapat terhubung ke server</td></tr>';
  }
}

function populateFilter(lokasi) {
  const sel = document.getElementById('filterLokasi');
  if (sel.dataset.filled) return;

  sel.innerHTML = '<option value="">Semua Lokasi</option>' +
    lokasi.map(l => `<option value="${esc(l)}">${esc(l)}</option>`).join('');

  sel.dataset.filled = '1';
}

function setKondisi(k, btn) {
  kondisiAktif = k;
  document.querySelectorAll('#lapTabs .lap-tab').forEach(b => b.classList.remove('active-btn'));
  btn.classList.add('active-btn');
  renderFiltered();
}

function renderFiltered() {
  const lok = document.getElementById('filterLokasi').value;
  const q   = document.getElementById('searchInput').value.trim().toLowerCase();

  let list = semuaLaporan;
  if (kondisiAktif) list = list.filter(l => l.kondisi_dilaporkan === kondisiAktif);
  if (lok)          list = list.filter(l => l.lokasi === lok);
  if (q) list = list.filter(l =>
    (l.pelapor   || '').toLowerCase().includes(q) ||
    (l.lokasi    || '').toLowerCase().includes(q) ||
    (l.deskripsi || '').toLowerCase().includes(q)
  );

  document.getElementById('lapTotal').textContent = list.length + ' laporan';
  renderTabel(list);
}

function renderTabel(list) {
  const body = document.getElementById('lapBody');

  if (!list.length) {
    body.innerHTML = '<tr><td colspan="5" class="bar-empty">Tidak ada laporan</td></tr>';
    return;
  }

  body.innerHTML = list.map(l => `
    <tr>
      <td>
        <div class="user-cell">
          <span class="user-avatar">${esc((l.pelapor || '?').charAt(0).toUpperCase())}</span>
          <div>
            <strong>${esc(l.pelapor)}</strong>
            <span class="lap-time">${fmtTanggal(l.waktu_laporan)}</span>
          </div>
        </div>
      </td>
      <td>${esc(l.lokasi)} &middot; Slot ${esc(l.nomor_slot)}</td>
      <td><span class="lap-badge ${l.kondisi_dilaporkan}">${esc(l.kondisi_dilaporkan)}</span></td>
      <td class="lap-desc-cell">${esc(l.deskripsi || '-')}</td>
      <td>
        <div class="vote-cell">
          <span class="vote setuju"><i class="fa-solid fa-thumbs-up"></i> ${esc(l.setuju)}</span>
          <span class="vote ditolak"><i class="fa-solid fa-thumbs-down"></i> ${esc(l.ditolak)}</span>
        </div>
      </td>
    </tr>
  `).join('');
}

function esc(s) {
  return (s == null ? '' : String(s)).replace(/[&<>"']/g, m => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
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

loadLaporan();
setInterval(loadLaporan, 30000);

</script>

</body>
</html>
