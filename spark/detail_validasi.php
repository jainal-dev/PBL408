<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0"
>

<title>Detail Validasi</title>

<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
  rel="stylesheet"
>

<link rel="stylesheet" href="css/detail-validasi.css">

</head>

<body>

<div class="container">

  <!-- HEADER -->
  <div class="header">

    <h3>Detail Validasi</h3>

  </div>

  <!-- CARD -->
  <div class="card">

    <!-- DETAIL -->
    <div id="detail"></div>

    <!-- KOMENTAR -->
    <div id="komentar"></div>

    <!-- INPUT -->
    <textarea
      id="text"
      placeholder="Tulis pendapat kamu..."
    ></textarea>

    <!-- SELECT -->
    <select id="status">

      <option value="">
        -- Pilih Validasi --
      </option>

      <option value="benar">
        Benar
      </option>

      <option value="salah">
        Salah
      </option>

    </select>

    <!-- BUTTON -->
    <button
      onclick="kirim()"
      class="send"
    >
      Kirim Validasi
    </button>

  </div>

  <!-- BACK -->
  <a href="validasi.php" class="back">
    Kembali
  </a>

</div>

<script>

/* DATA */
let data = [

  {
    id:1,

    area:"TA",

    slot:"A1",

    kondisi:"Kosong",

    desc:"Slot kosong sejak pagi",

    komentar:[

      {
        user:"Andi",
        text:"Valid kosong dari pagi",
        status:"benar",
        time:"14 April 2026 - 08:10:21"
      },

      {
        user:"Budi",
        text:"Saya cek langsung kosong",
        status:"benar",
        time:"14 April 2026 - 08:12:10"
      },

      {
        user:"Sinta",
        text:"Tidak ada kendaraan",
        status:"benar",
        time:"14 April 2026 - 08:15:44"
      }

    ]
  }

];

let id =
  localStorage.getItem("pilihValidasi");

let item =
  data.find(d => d.id == id);

/* DETAIL */
function renderDetail(){

  document.getElementById("detail").innerHTML = `

    <div class="title">
      Area ${item.area} - Slot ${item.slot}
    </div>

    <div class="sub">
      Status: ${item.kondisi}
    </div>

    <div class="desc">
      ${item.desc}
    </div>

  `;
}

/* KOMENTAR */
function renderKomentar(){

  let box =
    document.getElementById("komentar");

  box.innerHTML = "";

  item.komentar.forEach(k=>{

    box.innerHTML += `

      <div class="comment">

        <div class="comment-header">

          <div class="user">
            ${k.user}
          </div>

          <div class="time">
            ${k.time}
          </div>

        </div>

        <div class="text">
          ${k.text}
        </div>

        <span class="badge ${k.status}">
          ${k.status}
        </span>

      </div>

    `;

  });

}

/* TIME */
function getTimeNow(){

  const now = new Date();

  const date =
    now.toLocaleDateString(
      'id-ID',
      {
        day:'2-digit',
        month:'long',
        year:'numeric'
      }
    );

  const time =
    now.toLocaleTimeString(
      'id-ID',
      {
        hour:'2-digit',
        minute:'2-digit',
        second:'2-digit'
      }
    );

  return `${date} - ${time}`;
}

/* KIRIM */
function kirim(){

  let text =
    document.getElementById("text").value;

  let status =
    document.getElementById("status").value;

  if(!status){

    alert("Pilih status dulu!");

    return;
  }

  item.komentar.push({

    user:"User",

    text:text,

    status:status,

    time:getTimeNow()

  });

  renderKomentar();

  document.getElementById("text").value = "";

  document.getElementById("status").value = "";
}

/* INIT */
renderDetail();
renderKomentar();

</script>

</body>
</html>