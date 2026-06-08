<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Laporan Saya</title>

<link rel="stylesheet" href="css/laporan.css?v=8">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">

    <div class="header">

        <div>

            <h1>Laporan Saya</h1>

            <p>
                Riwayat semua laporan yang pernah kamu buat
            </p>

        </div>

        <button
            class="btn-buat"
            onclick="openForm()"
        >
            <span class="plus">+</span>
            Buat Laporan
        </button>

    </div>

    <div id="list-laporan">

        <div class="loading">
            Memuat laporan...
        </div>

    </div>

    <!-- NAVBAR -->
    <div class="navbar">

        <a href="dashboard.php">
            Beranda
        </a>

        <a href="laporan.php" class="active">
            Laporan Saya
        </a>

    </div>

</div>

<!-- MODAL -->

<div
    class="modal-overlay"
    id="modal-overlay"
    onclick="closeForm()"
>

    <div
        class="modal"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header">

            <h2 id="modal-title">
                Buat Laporan
            </h2>

            <button
                class="close-btn"
                onclick="closeForm()"
            >
                ×
            </button>

        </div>

        <div class="group">

            <label>Area Parkir</label>

            <select
                id="selArea"
                onchange="loadSlot()"
            >
                <option value="">
                    Pilih Area
                </option>
            </select>

        </div>

        <div class="group">

            <label>Nomor Slot</label>

            <div class="dropdown" id="slotDropdown">

                <button
                    type="button"
                    class="dropdown-trigger"
                    onclick="toggleSlot()"
                >
                    <span id="slotLabel" class="placeholder">
                        Pilih Area Dulu
                    </span>
                    <span class="dropdown-arrow">▾</span>
                </button>

                <div class="dropdown-menu" id="slotMenu"></div>

            </div>

            <input type="hidden" id="selSlot" value="">

        </div>

        <div class="group">

            <label>Kondisi</label>

            <select id="selKondisi">

                <option value="kosong">
                    Kosong
                </option>

                <option value="terisi">
                    Terisi
                </option>

                <option value="rusak">
                    Rusak
                </option>

            </select>

        </div>

        <div class="group">

            <label>Deskripsi</label>

            <textarea
                id="txtDeskripsi"
                placeholder="Tulis detail laporan..."
            ></textarea>

        </div>

        <div
            id="form-error"
            class="error"
        ></div>

        <button
            id="btn-submit"
            class="submit-btn"
            onclick="submitLaporan()"
        >
            Kirim Laporan
        </button>

    </div>

</div>

<script src="js/laporan.js?v=3"></script>

</body>
</html>