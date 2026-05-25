<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0"
>

<title>Laporan</title>

<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
  rel="stylesheet"
>

<link rel="stylesheet" href="css/laporan.css">

</head>

<body>

<div class="container">

  <div class="card">

    <h3>Buat Laporan</h3>

    <div class="sub">
      Laporkan kondisi parkir secara real-time
    </div>

    <!-- AREA -->
    <div class="group">

      <label>
        Area / Slot Parkir
      </label>

      <select>

        <option>TA</option>
        <option>GU</option>
        <option>Techno</option>
        <option>RTM</option>

      </select>

    </div>

    <!-- KONDISI -->
    <div class="group">

      <label>
        Kondisi Parkir
      </label>

      <select>

        <option>Kosong</option>
        <option>Terisi</option>
        <option>Rusak</option>

      </select>

    </div>

    <!-- DESKRIPSI -->
    <div class="group">

      <label>
        Deskripsi (Opsional)
      </label>

      <textarea
        placeholder="Contoh: Slot rusak, ada motor parkir sembarangan, dll..."
      ></textarea>

    </div>

    <!-- BUTTON -->
    <button>
      Kirim Laporan
    </button>

    <!-- BACK -->
    <a href="dashboard.php" class="back">
      Kembali
    </a>

  </div>

</div>

</body>
</html>