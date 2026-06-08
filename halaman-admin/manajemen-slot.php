<?php require_once __DIR__ . '/admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen Slot</title>

<link rel="stylesheet" href="css/dashboard.css?v=3">

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
                <a href="userreports.php">
                    <i class="fa-solid fa-clipboard-list"></i> Laporan
                </a>
                <a href="manajemen-slot.php" class="active">
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
                <input type="text" placeholder="Cari zona parkir, sensor, atau laporan...">
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
                <h1>Manajemen Slot</h1>
                <p>Kelola status tiap slot parkir per area.</p>
            </div>
        </div>

        <!-- KONTEN -->
        <div id="slotArea" class="slot-area">
            <div class="bar-empty">Memuat slot...</div>
        </div>

    </main>

</div>

<script>

const WARNA = { kosong: 'kosong', terisi: 'terisi', rusak: 'rusak' };

async function loadSlot() {
  try {
    const res = await fetch('api/slots.php');
    const json = await res.json();
    if (!json.success) return;

    /* kelompokkan per lokasi */
    const grup = {};
    json.data.forEach(s => {
      (grup[s.lokasi] = grup[s.lokasi] || []).push(s);
    });

    const box = document.getElementById('slotArea');
    box.innerHTML = Object.keys(grup).map(lokasi => {
      const slots = grup[lokasi];
      const terisi = slots.filter(s => s.status_slot === 'terisi').length;

      const cards = slots.map(s => `
        <div class="mslot-card">
          <div class="mslot-head">
            <strong>Slot ${esc(s.nomor_slot)}</strong>
            <span class="mslot-pill ${s.status_slot}" id="pill-${s.slot_id}">${esc(s.status_slot)}</span>
          </div>
          <select class="mslot-select" onchange="ubahStatus(${s.slot_id}, this.value)">
            <option value="kosong" ${s.status_slot==='kosong'?'selected':''}>Kosong</option>
            <option value="terisi" ${s.status_slot==='terisi'?'selected':''}>Terisi</option>
            <option value="rusak"  ${s.status_slot==='rusak' ?'selected':''}>Rusak</option>
          </select>
        </div>
      `).join('');

      return `
        <section class="panel area-block">
          <div class="area-head">
            <h2 class="panel-title">Area ${esc(lokasi)}</h2>
            <span class="area-sub">${terisi}/${slots.length} terisi</span>
          </div>
          <div class="mslot-grid">${cards}</div>
        </section>`;
    }).join('');

  } catch (e) {
    console.error(e);
    document.getElementById('slotArea').innerHTML =
      '<div class="bar-empty">Tidak dapat terhubung ke server</div>';
  }
}

async function ubahStatus(slotId, status) {
  try {
    const res = await fetch('api/slot_update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ slot_id: slotId, status })
    });
    const json = await res.json();
    if (!json.success) { alert(json.message || 'Gagal'); return; }

    const pill = document.getElementById('pill-' + slotId);
    pill.className = 'mslot-pill ' + status;
    pill.textContent = status;
  } catch (e) {
    alert('Tidak dapat terhubung ke server');
  }
}

function esc(s) {
  return (s == null ? '' : String(s)).replace(/[&<>"']/g, m => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[m]));
}

loadSlot();

</script>

</body>
</html>
