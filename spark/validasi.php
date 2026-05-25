<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0"
>

<title>Validasi Laporan</title>

<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
  rel="stylesheet"
>

<link rel="stylesheet" href="css/validasi.css">

</head>

<body>

<div class="container">

  <!-- HEADER -->
  <div class="header">

    <div>
      <a href="dashboard.php" class="back-btn">
        ← Kembali
      </a>
    </div>

    <div class="header-title">
      <h2>Validasi Laporan</h2>
      <p>Bantu validasi kondisi parkir pengguna lain 🚗</p>
    </div>

  </div>

  <!-- FILTER -->
  <div class="filter-box">

    <div class="topbar">

      <select id="filterArea" onchange="render()">
        <option value="all">Semua Lokasi</option>
        <option value="TA">TA</option>
        <option value="GU">GU</option>
        <option value="Techno">Techno</option>
        <option value="RTF">RTF</option>
      </select>

      <select id="filterStatus" onchange="render()">
        <option value="all">Semua Status</option>
        <option value="Kosong">Kosong</option>
        <option value="Terisi">Terisi</option>
        <option value="Rusak">Rusak</option>
      </select>

    </div>

    <input
      type="text"
      id="search"
      placeholder="Cari slot parkir..."
      oninput="render()"
    >

  </div>

  <!-- LIST -->
  <div id="list"></div>

</div>

<script>

let data = [

  {
    id:1,
    area:"TA",
    slot:"A1",
    kondisi:"Kosong",
    desc:"Slot kosong sejak pagi"
  },

  {
    id:2,
    area:"TA",
    slot:"A2",
    kondisi:"Terisi",
    desc:"Mobil merah parkir"
  },

  {
    id:3,
    area:"TA",
    slot:"A3",
    kondisi:"Rusak",
    desc:"Sensor error"
  },

  {
    id:4,
    area:"GU",
    slot:"B1",
    kondisi:"Kosong",
    desc:"Area aman"
  },

  {
    id:5,
    area:"Techno",
    slot:"C1",
    kondisi:"Rusak",
    desc:"Sensor mati total"
  },

  {
    id:6,
    area:"RTF",
    slot:"D2",
    kondisi:"Terisi",
    desc:"Weekend cukup padat"
  }

];

function render(){

  let area =
    document.getElementById("filterArea").value;

  let status =
    document.getElementById("filterStatus").value;

  let search =
    document
      .getElementById("search")
      .value
      .toLowerCase();

  let list =
    document.getElementById("list");

  list.innerHTML = "";

  let filtered = data.filter(d => {

    return (

      (area === "all" || d.area === area)

      &&

      (status === "all" || d.kondisi === status)

      &&

      d.slot.toLowerCase().includes(search)

    );

  });

  if(filtered.length === 0){

    list.innerHTML = `
      <div class="empty">
        Data tidak ditemukan 😢
      </div>
    `;

    return;
  }

  filtered.forEach(d => {

    let badgeClass =
      d.kondisi === "Kosong"
      ? "kosong"
      : d.kondisi === "Terisi"
      ? "terisi"
      : "rusak";

    list.innerHTML += `

      <div class="card">

        <div class="card-top">

          <div>

            <div class="title">
              Area ${d.area}
            </div>

            <div class="slot">
              Slot ${d.slot}
            </div>

          </div>

          <span class="badge ${badgeClass}">
            ${d.kondisi}
          </span>

        </div>

        <div class="desc">
          ${d.desc}
        </div>

        <button onclick="buka(${d.id})">
          Validasi Sekarang
        </button>

      </div>

    `;

  });

}

function buka(id){

  localStorage.setItem(
    "pilihValidasi",
    id
  );

  window.location.href =
    "detail-validasi.php";
}

render();

</script>

</body>
</html>