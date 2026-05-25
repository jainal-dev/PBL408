<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/daftar.css">
</head>

<body>

<div class="wrapper">

  <div class="header">
    <h1>Smart <span>Parking</span></h1>
    <p>Buat akun baru 🚗</p>
  </div>

  <div class="form-container">

    <div class="input-group">
      <label>Nama</label>
      <input type="text" id="nama" placeholder="Masukkan nama">
    </div>

    <div class="input-group">
      <label>Email</label>
      <input type="email" id="email" placeholder="Masukkan email">
    </div>

    <div class="input-group">
      <label>Password</label>
      <input type="password" id="password" placeholder="Buat password">
    </div>

    <div class="input-group">
      <label>Konfirmasi Password</label>
      <input type="password" id="konfirmasi" placeholder="Ulangi password">
    </div>

    <div id="error-msg" class="error-msg"></div>

    <button id="btn-register" onclick="register()">Daftar</button>

    <div class="footer">
      <a href="login.php">Sudah punya akun? Login</a>
    </div>

  </div>

</div>

<script>

async function register() {

  const nama       = document.getElementById("nama").value.trim();
  const email      = document.getElementById("email").value.trim();
  const password   = document.getElementById("password").value;
  const konfirmasi = document.getElementById("konfirmasi").value;

  const errorBox = document.getElementById("error-msg");
  const btn      = document.getElementById("btn-register");
  const wrapper  = document.querySelector(".wrapper");

  errorBox.textContent = "";

  /* Validasi sederhana di frontend */
  if (!nama || !email || !password || !konfirmasi) {
    errorBox.textContent = "Semua field wajib diisi";
    return;
  }

  if (password.length < 6) {
    errorBox.textContent = "Password minimal 6 karakter";
    return;
  }

  if (password !== konfirmasi) {
    errorBox.textContent = "Konfirmasi password tidak cocok";
    return;
  }

  /* Disable tombol selama proses */
  btn.disabled = true;
  btn.textContent = "Mendaftarkan...";

  try {

    const res = await fetch("api/register.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        nama,
        email,
        password,
        konfirmasi
      })
    });

    const data = await res.json();

    if (!data.success) {
      errorBox.textContent = data.message || "Pendaftaran gagal";
      btn.disabled = false;
      btn.textContent = "Daftar";
      return;
    }

    /* Sukses → langsung login otomatis, simpan user */
    localStorage.setItem(
      "user",
      JSON.stringify(data.user)
    );

    /* Animasi transisi */
    wrapper.style.transform = "scale(0.96)";
    wrapper.style.opacity = "0.6";

    setTimeout(() => {
      window.location.href = "dashboard.php";
    }, 300);

  } catch (err) {

    errorBox.textContent = "Tidak bisa konek ke server";
    btn.disabled = false;
    btn.textContent = "Daftar";
  }
}

/* Submit dengan tombol Enter */
document.addEventListener("keydown", e => {
  if (e.key === "Enter") register();
});

</script>

</body>
</html>