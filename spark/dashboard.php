<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/dashboard.css?v=12">
</head>

<body>

<div class="container">

  <!-- HEADER -->
  <div class="header">

    <h2>Parkir Cerdas</h2>

    <a href="profil.php" class="profile" id="profile">
      U
    </a>

  </div>

  <!-- TOGGLE -->
  <div class="toggle">

    <button
      class="active"
      onclick="setMode('cv', this)"
    >
      CV
    </button>

    <button
      onclick="setMode('laporan', this)"
    >
      Laporan
    </button>

  </div>

  <!-- AREA -->
  <div class="area">

    <button
      class="active-area"
      onclick="setArea(this)"
    >
      TA
    </button>

    <button onclick="setArea(this)">
      GU
    </button>

    <button onclick="setArea(this)">
      Techno
    </button>

    <button onclick="setArea(this)">
      RTM
    </button>

  </div>

  <!-- CONTENT -->
  <div id="content"></div>

  <!-- NAVBAR -->
  <div class="navbar">

    <a href="dashboard.php" class="active">
      Beranda
    </a>

    <a href="laporan.php">
      Laporan Saya
    </a>

  </div>

</div>

<script>

let mode = "cv";
let activeArea = "TA";

/* user yang login (untuk validasi) */
let user = null;
try {
  user = JSON.parse(localStorage.getItem("user"));
} catch (e) {
  user = null;
}

/* DATA CV (dummy sementara) */
const dataCV = [
  { slot: "A1", kondisi: "Kosong" },
  { slot: "A2", kondisi: "Terisi" },
  { slot: "A3", kondisi: "Rusak" },
  { slot: "A4", kondisi: "Kosong" },
  { slot: "A5", kondisi: "Terisi" },
  { slot: "A6", kondisi: "Kosong" }
];

/* warna badge sesuai konvensi app */
function warnaKondisi(k) {
  k = (k || "").toLowerCase();
  if (k === "kosong") return "#f59e0b"; // oren
  if (k === "terisi") return "#22c55e"; // hijau
  return "#ef4444"; // rusak - merah
}

/* format tanggal dari DB */
function formatTanggal(waktu) {
  if (!waktu) return "-";
  const d = new Date(waktu.replace(" ", "T"));
  if (isNaN(d)) return waktu;
  return d.toLocaleString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  });
}

/* escape input user biar aman dari HTML */
function escapeHtml(s) {
  return (s == null ? "" : String(s))
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

/* ROUTER RENDER */
function render() {
  if (mode === "cv") {
    renderCV();
  } else {
    renderLaporan();
  }
}

/* MODE CV */
function renderCV() {
  const content = document.getElementById("content");

  content.innerHTML = `<div class="grid"></div>`;

  const grid = content.querySelector(".grid");

  dataCV.forEach(d => {
    const cls =
      d.kondisi === "Kosong" ? "available" :
      d.kondisi === "Terisi" ? "filled" : "problem";

    grid.innerHTML += `
      <div class="card ${cls}">
        <b>${d.slot}</b>
        <small>${d.kondisi}</small>
      </div>
    `;
  });
}

/* MODE LAPORAN (semua laporan + validasi langsung) */
async function renderLaporan() {
  const content = document.getElementById("content");

  content.innerHTML = `<div class="lap-info">Memuat laporan...</div>`;

  try {
    const uid = user ? user.user_id : 0;

    const res = await fetch(
      `api/laporan_all.php?user_id=${uid}&lokasi=${encodeURIComponent(activeArea)}`
    );

    const json = await res.json();

    if (!json.success) {
      content.innerHTML = `<div class="lap-info">Gagal memuat laporan</div>`;
      return;
    }

    if (json.data.length === 0) {
      content.innerHTML = `<div class="lap-info">Belum ada laporan di area ${activeArea}</div>`;
      return;
    }

    content.innerHTML = "";

    json.data.forEach(d => {
      const milikSendiri = user && d.user_id == user.user_id;

      let aksi = "";

      if (!user) {
        aksi = `<div class="lap-note">Login untuk memvalidasi</div>`;
      } else if (milikSendiri) {
        aksi = `<div class="lap-note">Laporan kamu sendiri</div>`;
      } else if (d.sudah_validasi == 1) {
        aksi = `<div class="lap-note ok">✓ Sudah kamu validasi</div>`;
      } else {
        aksi = `
          <div class="validasi-form">
            <textarea
              id="komentar-${d.laporan_id}"
              class="komentar-input"
              rows="2"
              placeholder="Tulis pendapat (opsional)..."
            ></textarea>
            <div class="pilih-verdict" id="verdict-${d.laporan_id}">
              <button type="button" class="chip chip-setuju" onclick="pilihVerdict(${d.laporan_id},'setuju',this)">
                👍 Setuju
              </button>
              <button type="button" class="chip chip-tolak" onclick="pilihVerdict(${d.laporan_id},'ditolak',this)">
                👎 Tolak
              </button>
            </div>
            <button class="btn-kirim" id="kirim-${d.laporan_id}" onclick="kirimValidasi(${d.laporan_id})">
              Kirim Validasi
            </button>
          </div>`;
      }

      /* utas komentar validator */
      let komentarHtml = "";
      if (d.komentar && d.komentar.length) {
        komentarHtml = `<div class="komentar-list">` +
          d.komentar.map(k => `
            <div class="komentar-item">
              <div class="komentar-head">
                <span class="komentar-nama">${escapeHtml(k.nama)}</span>
                <span class="komentar-badge ${k.status}">
                  ${k.status === "setuju" ? "👍 setuju" : "👎 tolak"}
                </span>
              </div>
              <div class="komentar-teks">${escapeHtml(k.komentar)}</div>
            </div>
          `).join("") +
          `</div>`;
      }

      content.innerHTML += `
        <div class="report-card">

          <div class="report-top">
            <div>
              <b>${escapeHtml(d.lokasi)} - Slot ${escapeHtml(d.nomor_slot)}</b>
              <div class="report-user">oleh ${escapeHtml(d.pelapor)}</div>
            </div>
            <span class="status" style="background:${warnaKondisi(d.kondisi_dilaporkan)}">
              ${escapeHtml(d.kondisi_dilaporkan)}
            </span>
          </div>

          <div class="report-desc">${escapeHtml(d.deskripsi) || "-"}</div>

          <div class="report-meta">
            <span class="report-time">🕒 ${formatTanggal(d.waktu_laporan)}</span>
            <span class="report-count">👍 ${d.setuju} · 👎 ${d.ditolak}</span>
          </div>

          ${komentarHtml}

          ${aksi}

        </div>
      `;
    });

  } catch (err) {
    console.error(err);
    content.innerHTML = `<div class="lap-info">Tidak dapat terhubung ke server</div>`;
  }
}

/* PILIH SETUJU/TOLAK (belum terkirim) */
let pilihan = {}; // laporan_id -> 'setuju' | 'ditolak'

function pilihVerdict(laporanId, status, btn) {
  pilihan[laporanId] = status;

  const group = document.getElementById("verdict-" + laporanId);
  group.querySelectorAll(".chip").forEach(c => c.classList.remove("selected"));
  btn.classList.add("selected");
}

/* KIRIM VALIDASI */
async function kirimValidasi(laporanId) {
  if (!user) {
    alert("Silakan login dulu untuk memvalidasi");
    return;
  }

  const status = pilihan[laporanId];
  if (!status) {
    alert("Pilih Setuju atau Tolak dulu");
    return;
  }

  const box = document.getElementById("komentar-" + laporanId);
  const komentar = box ? box.value.trim() : "";

  const btn = document.getElementById("kirim-" + laporanId);
  btn.disabled = true;

  try {
    const res = await fetch("api/validasi_create.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        laporan_id: laporanId,
        validator_id: user.user_id,
        status: status,
        komentar: komentar
      })
    });

    const json = await res.json();

    if (!json.success) {
      alert(json.message || "Gagal menyimpan validasi");
      btn.disabled = false;
      return;
    }

    delete pilihan[laporanId];
    renderLaporan(); // refresh daftar

  } catch (err) {
    console.error(err);
    alert("Tidak dapat terhubung ke server");
    btn.disabled = false;
  }
}

/* TOGGLE MODE */
function setMode(m, btn) {
  mode = m;

  document
    .querySelectorAll(".toggle button")
    .forEach(b => b.classList.remove("active"));

  btn.classList.add("active");

  render();
}

/* PILIH AREA */
function setArea(btn) {
  activeArea = btn.textContent.trim();

  document
    .querySelectorAll(".area button")
    .forEach(b => b.classList.remove("active-area"));

  btn.classList.add("active-area");

  render();
}

/* AUTO-REFRESH real-time untuk tab Laporan,
   dilewati kalau user sedang memilih/mengetik biar tidak terganggu */
function adaInteraksi() {
  if (Object.keys(pilihan).length) return true;

  const inputs = document.querySelectorAll(".komentar-input");
  for (const i of inputs) {
    if (i.value.trim()) return true;
  }
  return false;
}

setInterval(() => {
  if (mode === "laporan" && !adaInteraksi()) {
    renderLaporan();
  }
}, 20000);

/* avatar profil = inisial (huruf depan nama) user yang login */
if (user && user.nama) {
  document.getElementById("profile").innerText =
    user.nama.trim().charAt(0).toUpperCase();
}

render();

</script>

</body>
</html>
