<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/profil.css?v=1">
</head>

<body>

<div class="card">

  <div class="avatar" id="avatar">U</div>

  <!-- VIEW MODE -->
  <div id="view">
    <div class="name" id="name"></div>
    <div class="email" id="email"></div>

    <div class="detail">
      <div class="label">Nama</div>
      <div class="value" id="namaText"></div>
    </div>

    <div class="detail">
      <div class="label">Email</div>
      <div class="value" id="emailText"></div>
    </div>

    <div class="detail">
      <div class="label">Role</div>
      <div class="value" id="roleText"></div>
    </div>

    <button onclick="showEdit()">Edit Profil</button>
    <button class="logout" onclick="logout()">Keluar</button>
  </div>

  <!-- EDIT MODE -->
  <div id="edit" class="edit">

    <div class="label">Nama</div>
    <input id="namaInput">

    <div class="label">Email</div>
    <input id="emailInput" type="email">

    <div class="label">Password Baru</div>
    <input id="passwordInput" type="password" placeholder="••••••">
    <div class="hint">Kosongkan jika tidak ingin mengubah password</div>

    <div id="msg" class="msg"></div>

    <button id="btnSimpan" onclick="simpan()">Simpan Perubahan</button>
    <a class="back" onclick="batal()">Batal</a>
  </div>

  <a href="dashboard.php" class="back">Kembali</a>

</div>

<script>

/* AUTH */
let user = null;
try {
  user = JSON.parse(localStorage.getItem("user"));
} catch (e) {
  user = null;
}

if (!user || !user.user_id) {
  window.location.href = "login.php";
}

/* TAMPILKAN DATA */
function tampilkan() {
  document.getElementById("name").innerText  = user.nama || "-";
  document.getElementById("email").innerText = user.email || "-";

  document.getElementById("namaText").innerText  = user.nama || "-";
  document.getElementById("emailText").innerText = user.email || "-";
  document.getElementById("roleText").innerText  = user.role || "-";

  document.getElementById("avatar").innerText =
    (user.nama || "U").charAt(0).toUpperCase();

  document.getElementById("namaInput").value  = user.nama || "";
  document.getElementById("emailInput").value = user.email || "";
}

/* AMBIL DATA TERBARU DARI DB */
async function muatProfil() {
  try {
    const res = await fetch("api/profil_get.php?user_id=" + user.user_id);
    const json = await res.json();

    if (json.success) {
      user = json.user;
      localStorage.setItem("user", JSON.stringify(user));
      tampilkan();
    }
  } catch (e) {
    console.error(e);
    // tetap pakai data localStorage kalau server tidak terjangkau
  }
}

/* MODE EDIT */
function showEdit() {
  document.getElementById("passwordInput").value = "";
  document.getElementById("msg").innerText = "";
  document.getElementById("msg").className = "msg";

  document.getElementById("view").style.display = "none";
  document.getElementById("edit").style.display = "block";
}

function batal() {
  document.getElementById("edit").style.display = "none";
  document.getElementById("view").style.display = "block";
}

/* SIMPAN KE DB */
async function simpan() {
  const nama     = document.getElementById("namaInput").value.trim();
  const email    = document.getElementById("emailInput").value.trim();
  const password = document.getElementById("passwordInput").value;

  const msg = document.getElementById("msg");
  const btn = document.getElementById("btnSimpan");

  msg.className = "msg";

  if (!nama || !email) {
    msg.className = "msg error";
    msg.innerText = "Nama dan email wajib diisi";
    return;
  }

  btn.disabled = true;

  try {
    const res = await fetch("api/profil_update.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: user.user_id,
        nama: nama,
        email: email,
        password: password
      })
    });

    const json = await res.json();

    if (!json.success) {
      msg.className = "msg error";
      msg.innerText = json.message || "Gagal menyimpan";
      btn.disabled = false;
      return;
    }

    /* simpan data terbaru */
    user = json.user;
    localStorage.setItem("user", JSON.stringify(user));

    tampilkan();

    btn.disabled = false;
    batal();

  } catch (e) {
    console.error(e);
    msg.className = "msg error";
    msg.innerText = "Tidak dapat terhubung ke server";
    btn.disabled = false;
  }
}

/* LOGOUT */
async function logout() {
  try {
    await fetch("api/logout.php");
  } catch (e) {
    /* abaikan; tetap lanjut keluar */
  }
  localStorage.removeItem("user");
  window.location.href = "login.php";
}

/* INIT */
tampilkan();   // langsung dari localStorage (tanpa kedip)
muatProfil();  // lalu segarkan dari DB

</script>

</body>
</html>
