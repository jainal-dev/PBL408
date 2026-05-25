<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

<div class="container">

  <!-- HEADER -->
  <div class="header">

    <h2>Parkir Cerdas</h2>

    <a href="profil.php" class="profile">
      BU
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

    <a href="dashboard.php">
      Beranda
    </a>

    <a href="validasi.php">
      Validasi
    </a>

    <a href="laporan.php">
      Laporan
    </a>

  </div>

</div>

<script>

let mode = "cv";

/* DATA CV */
const dataCV = [

  {slot:"A1", kondisi:"Kosong"},
  {slot:"A2", kondisi:"Terisi"},
  {slot:"A3", kondisi:"Rusak"},
  {slot:"A4", kondisi:"Kosong"},
  {slot:"A5", kondisi:"Terisi"},
  {slot:"A6", kondisi:"Kosong"}

];

/* DUMMY LAPORAN */
function initDummy(){

  if(!localStorage.getItem("laporan")){

    let dummy = [

      {
        area:"TA",
        slot:"A1",
        kondisi:"Kosong",
        user:"Andi",
        desc:"Terlihat kosong sejak pagi",
        waktu:"14-04-2026 08:10"
      },

      {
        area:"GU",
        slot:"B1",
        kondisi:"Terisi",
        user:"Budi",
        desc:"Area cukup padat",
        waktu:"14-04-2026 09:15"
      },

      {
        area:"Techno",
        slot:"C1",
        kondisi:"Rusak",
        user:"Sinta",
        desc:"Sensor tidak terbaca",
        waktu:"14-04-2026 10:30"
      }

    ];

    localStorage.setItem(
      "laporan",
      JSON.stringify(dummy)
    );
  }
}

/* RENDER */
function render(){

  let content =
    document.getElementById("content");

  content.innerHTML = "";

  /* MODE CV */
  if(mode === "cv"){

    content.innerHTML =
      `<div class="grid"></div>`;

    let grid =
      content.querySelector(".grid");

    dataCV.forEach(d=>{

      let cls =

        d.kondisi === "Kosong"
        ? "available"

        : d.kondisi === "Terisi"
        ? "filled"

        : "problem";

      grid.innerHTML += `

        <div class="card ${cls}">

          <b>${d.slot}</b>

          <small>
            ${d.kondisi}
          </small>

        </div>

      `;

    });

  }

  /* MODE LAPORAN */
  else{

    let data =

      JSON.parse(
        localStorage.getItem("laporan")
      ) || [];

    data.forEach(d=>{

      let warna =

        d.kondisi === "Kosong"
        ? "#22c55e"

        : d.kondisi === "Terisi"
        ? "#ef4444"

        : "#f59e0b";

      content.innerHTML += `

        <div class="report-card">

          <div class="report-top">

            <div>

              <b>
                ${d.area} - ${d.slot}
              </b>

              <div class="report-user">
                oleh ${d.user}
              </div>

            </div>

            <span
              class="status"
              style="background:${warna}"
            >
              ${d.kondisi}
            </span>

          </div>

          <div class="report-desc">
            ${d.desc}
          </div>

          <div class="report-time">
            🕒 ${d.waktu}
          </div>

        </div>

      `;
    });

  }

}

/* MODE */
function setMode(m, btn){

  mode = m;

  document
    .querySelectorAll(".toggle button")
    .forEach(b=>{
      b.classList.remove("active");
    });

  btn.classList.add("active");

  render();
}

/* AREA */
function setArea(btn){

  document
    .querySelectorAll(".area button")
    .forEach(b=>{
      b.classList.remove("active-area");
    });

  btn.classList.add("active-area");
}

initDummy();
render();

</script>

</body>
</html>