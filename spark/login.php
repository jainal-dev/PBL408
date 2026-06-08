<?php
  session_start();
  if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/login.css?v=2">
</head>

<body>

<div class="wrapper">

  <div class="header">
    <h1>Smart <span>Parking</span></h1>
    <p>Login untuk melanjutkan 🚗</p>
  </div>

  <div class="form-container">

    <div class="input-group">
      <label>Email</label>
      <input type="email" id="email" placeholder="Masukkan email">
    </div>

    <div class="input-group">
      <label>Password</label>
      <input type="password" id="password" placeholder="Masukkan password">
    </div>

    <div id="error-msg" class="error-msg"></div>

    <button id="btn-login" onclick="login()">Masuk</button>

    <div class="footer">
      <a href="daftar.php">Belum punya akun? Daftar</a>
    </div>

  </div>

</div>

<script>

async function login() {

  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const errorBox = document.getElementById("error-msg");
  const btn = document.getElementById("btn-login");
  const wrapper = document.querySelector(".wrapper");

  const email = emailInput.value.trim();
  const password = passwordInput.value;

  errorBox.textContent = "";

  /* Validasi sederhana */
  if (!email || !password) {
    errorBox.textContent = "Email dan password wajib diisi";
    return;
  }

  /* Disable tombol selama proses */
  btn.disabled = true;
  btn.textContent = "Memproses...";

  try {

    const res = await fetch("api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ email, password })
    });

    const data = await res.json();

    if (!data.success) {
      errorBox.textContent = data.message || "Login gagal";
      btn.disabled = false;
      btn.textContent = "Masuk";
      return;
    }

    /* Simpan user di localStorage */
    localStorage.setItem(
      "user",
      JSON.stringify(data.user)
    );

    /* Animasi transisi */
    wrapper.style.transform = "scale(0.96)";
    wrapper.style.opacity = "0.6";

    setTimeout(() => {
      window.location.href = (data.user.role === "admin")
        ? "../halaman-admin/dashboard.php"
        : "dashboard.php";
    }, 300);

  } catch (err) {

    errorBox.textContent = "Tidak bisa konek ke server";
    btn.disabled = false;
    btn.textContent = "Masuk";
  }
}

/* Submit dengan tombol Enter */
document.addEventListener("keydown", e => {
  if (e.key === "Enter") login();
});

</script>

</body>
</html>