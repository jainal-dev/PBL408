<?php
require_once __DIR__ . '/admin_auth.php';
$namaAdmin  = $_SESSION['user']['nama']  ?? '';
$emailAdmin = $_SESSION['user']['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pengaturan</title>

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
                <a href="userreports.php">
                    <i class="fa-solid fa-clipboard-list"></i> Laporan
                </a>
                <a href="manajemen-slot.php">
                    <i class="fa-solid fa-square-parking"></i> Manajemen Slot
                </a>
                <a href="pengguna.php">
                    <i class="fa-solid fa-users"></i> Pengguna
                </a>
                <a href="pengaturan.php" class="active">
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
                <input type="text" placeholder="Cari pengaturan...">
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
                <h1>Pengaturan</h1>
                <p>Kelola preferensi akun dan keamanan admin.</p>
            </div>
        </div>

        <div class="settings-grid">

            <!-- DO NOT DISTURB -->
            <section class="panel setting-card">
                <div class="setting-row">
                    <div>
                        <h2 class="panel-title">Do Not Disturb</h2>
                        <p class="setting-hint">Sembunyikan notifikasi sistem & peringatan sementara.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="dndToggle" onchange="simpanDnd()">
                        <span class="slider-toggle"></span>
                    </label>
                </div>
            </section>

            <!-- CHANGE USERNAME -->
            <section class="panel setting-card">
                <h2 class="panel-title">Change Username</h2>
                <p class="setting-hint">Perbarui nama tampilan akun admin.</p>

                <form onsubmit="updateUsername(event)">
                    <div class="form-group">
                        <label>Current Username</label>
                        <input type="text" id="curUsername" value="<?= htmlspecialchars($namaAdmin) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>New Username</label>
                        <input type="text" id="newUsername" placeholder="Username baru" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Username</label>
                        <input type="text" id="confUsername" placeholder="Ulangi username baru" required>
                    </div>
                    <button type="submit" class="btn-primary">Update Username</button>
                    <span class="form-msg" id="msgUsername"></span>
                </form>
            </section>

            <!-- PASSWORD & API KEY -->
            <section class="panel setting-card">
                <h2 class="panel-title">Password &amp; API Key</h2>
                <p class="setting-hint">Ubah password login dan kelola API Key.</p>

                <form onsubmit="updatePassword(event)">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" id="curPassword" placeholder="Password saat ini" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" id="newPassword" placeholder="Password baru (min. 6 karakter)" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" id="confPassword" placeholder="Ulangi password baru" required>
                    </div>
                    <button type="submit" class="btn-primary">Update Password</button>
                    <span class="form-msg" id="msgPassword"></span>
                </form>

                <div class="setting-divider"></div>

                <div class="form-group">
                    <label>API Key</label>
                    <div class="apikey-row">
                        <input type="text" id="apiKey" readonly>
                        <button type="button" class="btn-ghost" onclick="copyApiKey()" title="Salin">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                        <button type="button" class="btn-ghost" onclick="generateApiKey()" title="Generate ulang">
                            <i class="fa-solid fa-rotate"></i>
                        </button>
                    </div>
                    <span class="form-msg" id="msgApiKey"></span>
                </div>
            </section>

        </div>

    </main>

</div>

<script>

/* ===== DO NOT DISTURB (preferensi lokal) ===== */
function initDnd() {
  document.getElementById('dndToggle').checked = localStorage.getItem('spark_dnd') === '1';
}
function simpanDnd() {
  localStorage.setItem('spark_dnd', document.getElementById('dndToggle').checked ? '1' : '0');
}

/* ===== CHANGE USERNAME ===== */
async function updateUsername(e) {
  e.preventDefault();
  const baru = document.getElementById('newUsername').value.trim();
  const konf = document.getElementById('confUsername').value.trim();
  const msg  = document.getElementById('msgUsername');

  if (baru !== konf) return showMsg(msg, 'Konfirmasi username tidak cocok', false);
  if (baru.length < 3) return showMsg(msg, 'Username minimal 3 karakter', false);

  try {
    const res = await fetch('api/akun_username.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: baru })
    });
    const json = await res.json();
    showMsg(msg, json.message, json.success);

    if (json.success) {
      document.getElementById('curUsername').value = json.username;
      document.getElementById('newUsername').value = '';
      document.getElementById('confUsername').value = '';
    }
  } catch (err) {
    showMsg(msg, 'Tidak dapat terhubung ke server', false);
  }
}

/* ===== CHANGE PASSWORD ===== */
async function updatePassword(e) {
  e.preventDefault();
  const cur  = document.getElementById('curPassword').value;
  const baru = document.getElementById('newPassword').value;
  const konf = document.getElementById('confPassword').value;
  const msg  = document.getElementById('msgPassword');

  if (baru !== konf) return showMsg(msg, 'Konfirmasi password tidak cocok', false);
  if (baru.length < 6) return showMsg(msg, 'Password baru minimal 6 karakter', false);

  try {
    const res = await fetch('api/akun_password.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ current: cur, baru: baru })
    });
    const json = await res.json();
    showMsg(msg, json.message, json.success);

    if (json.success) {
      document.getElementById('curPassword').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confPassword').value = '';
    }
  } catch (err) {
    showMsg(msg, 'Tidak dapat terhubung ke server', false);
  }
}

/* ===== API KEY (preferensi lokal / demo) ===== */
function initApiKey() {
  let key = localStorage.getItem('spark_api_key');
  if (!key) { key = buatKey(); localStorage.setItem('spark_api_key', key); }
  document.getElementById('apiKey').value = key;
}
function buatKey() {
  const a = new Uint8Array(20);
  crypto.getRandomValues(a);
  return 'sk_' + [...a].map(b => b.toString(16).padStart(2, '0')).join('');
}
function generateApiKey() {
  const key = buatKey();
  localStorage.setItem('spark_api_key', key);
  document.getElementById('apiKey').value = key;
  showMsg(document.getElementById('msgApiKey'), 'API Key baru dibuat', true);
}
function copyApiKey() {
  const inp = document.getElementById('apiKey');
  navigator.clipboard?.writeText(inp.value);
  showMsg(document.getElementById('msgApiKey'), 'API Key disalin', true);
}

/* ===== util ===== */
function showMsg(el, text, ok) {
  el.textContent = text || '';
  el.className = 'form-msg ' + (ok ? 'ok' : 'err');
}

initDnd();
initApiKey();

</script>

</body>
</html>
