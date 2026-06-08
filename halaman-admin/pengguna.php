<?php require_once __DIR__ . '/admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen Pengguna</title>

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
                <a href="manajemen-slot.php">
                    <i class="fa-solid fa-square-parking"></i> Manajemen Slot
                </a>
                <a href="pengguna.php" class="active">
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
                <input type="text" placeholder="Cari pengguna...">
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
                <h1>Manajemen Pengguna</h1>
                <p>Kelola akun pengguna terdaftar.</p>
            </div>
        </div>

        <!-- KONTEN -->
        <section class="panel">
            <div class="table-wrap">
                <table class="user-table" id="userTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userBody">
                        <tr><td colspan="5" class="bar-empty">Memuat pengguna...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</div>

<script>

async function loadUsers() {
  try {
    const res = await fetch('api/users.php');
    const json = await res.json();
    if (!json.success) return;

    const body = document.getElementById('userBody');

    if (!json.data.length) {
      body.innerHTML = '<tr><td colspan="5" class="bar-empty">Belum ada pengguna</td></tr>';
      return;
    }

    body.innerHTML = json.data.map(u => `
      <tr id="row-${u.user_id}">
        <td>
          <div class="user-cell">
            <span class="user-avatar">${esc((u.nama||'?').charAt(0).toUpperCase())}</span>
            <strong>${esc(u.nama)}</strong>
          </div>
        </td>
        <td class="user-email">${esc(u.email)}</td>
        <td>
          <select class="role-select" onchange="ubahRole(${u.user_id}, this.value)">
            <option value="pengguna" ${u.role==='pengguna'?'selected':''}>Pengguna</option>
            <option value="admin" ${u.role==='admin'?'selected':''}>Admin</option>
          </select>
        </td>
        <td><span class="lap-count">${esc(u.jml_laporan)}</span></td>
        <td>
          <button class="btn-hapus-user" onclick="hapusUser(${u.user_id}, '${esc(u.nama)}')">
            <i class="fa-solid fa-trash"></i> Hapus
          </button>
        </td>
      </tr>
    `).join('');

  } catch (e) {
    console.error(e);
    document.getElementById('userBody').innerHTML =
      '<tr><td colspan="5" class="bar-empty">Tidak dapat terhubung ke server</td></tr>';
  }
}

async function ubahRole(userId, role) {
  try {
    const res = await fetch('api/user_role.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId, role })
    });
    const json = await res.json();
    if (!json.success) alert(json.message || 'Gagal mengubah role');
  } catch (e) {
    alert('Tidak dapat terhubung ke server');
  }
}

async function hapusUser(userId, nama) {
  if (!confirm('Hapus pengguna "' + nama + '" beserta semua laporannya? Tindakan ini permanen.')) return;

  try {
    const res = await fetch('api/user_delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId })
    });
    const json = await res.json();
    if (!json.success) { alert(json.message || 'Gagal menghapus'); return; }

    const row = document.getElementById('row-' + userId);
    if (row) row.remove();
  } catch (e) {
    alert('Tidak dapat terhubung ke server');
  }
}

function esc(s) {
  return (s == null ? '' : String(s)).replace(/[&<>"']/g, m => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[m]));
}

loadUsers();

</script>

</body>
</html>
