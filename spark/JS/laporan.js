/* =========================
   AUTH
========================= */

const userRaw = localStorage.getItem('user');

if (!userRaw) {
  window.location.href = 'login.php';
}

const user = JSON.parse(userRaw);

/* =========================
   STATE
========================= */

let editId = null;

/* =========================
   MODAL
========================= */

function openForm() {
  document.getElementById('modal-overlay').classList.add('show');
}

function closeForm(){

document
.getElementById("modal-overlay")
.classList.remove("show");

resetForm();
}

function resetForm() {
  editId = null;

  document.getElementById('modal-title').textContent = 'Buat Laporan';

  document.getElementById('selArea').value = '';

  setSlot('', 'Pilih Area Dulu');
  document.getElementById('slotMenu').innerHTML = '';
  tutupSlot();

  document.getElementById('selKondisi').value = 'kosong';

  document.getElementById('txtDeskripsi').value = '';

  document.getElementById('form-error').textContent = '';

  document.getElementById('btn-submit').textContent = 'Kirim Laporan';

  document.getElementById('btn-submit').disabled = false;
}

/* =========================
   LOAD PARKIR
========================= */

async function loadParkir() {
  try {
    const res = await fetch('api/parkir_list.php');

    const json = await res.json();

    if (!json.success) return;

    const sel = document.getElementById('selArea');

    sel.innerHTML = "<option value=''>-- Pilih Area --</option>";

    json.data.forEach((item) => {
      sel.innerHTML += `
                <option value="${item.parkir_id}">
                    ${item.lokasi}
                </option>
            `;
    });
  } catch (err) {
    console.error(err);
  }
}

/* =========================
   LOAD SLOT
========================= */

async function loadSlot() {
  const parkirId = document.getElementById('selArea').value;

  const menu = document.getElementById('slotMenu');

  // reset pilihan slot tiap ganti area
  setSlot('', 'Pilih Slot');
  tutupSlot();
  menu.innerHTML = '';

  if (!parkirId) {
    setSlot('', 'Pilih Area Dulu');
    return;
  }

  try {
    const res = await fetch('api/slot_list.php?parkir_id=' + parkirId);

    const json = await res.json();

    if (!json.success) {
      setSlot('', 'Gagal Memuat Slot');
      return;
    }

    json.data.forEach((slot) => {
      const teks = 'Slot ' + slot.nomor_slot;

      const item = document.createElement('div');
      item.className = 'dropdown-item';
      item.textContent = teks;
      item.onclick = () => {
        setSlot(slot.slot_id, teks);
        tutupSlot();
      };

      menu.appendChild(item);
    });
  } catch (err) {
    console.error(err);
    setSlot('', 'Tidak dapat memuat slot');
  }
}

/* =========================
   DROPDOWN SLOT (kustom)
========================= */

function setSlot(id, teks) {
  document.getElementById('selSlot').value = id || '';

  const label = document.getElementById('slotLabel');
  label.textContent = teks;
  label.classList.toggle('placeholder', !id);
}

function toggleSlot() {
  // jangan buka kalau belum ada slot (area belum dipilih)
  if (!document.getElementById('slotMenu').children.length) return;

  document.getElementById('slotDropdown').classList.toggle('open');
}

function tutupSlot() {
  document.getElementById('slotDropdown').classList.remove('open');
}

/* tutup dropdown saat klik di luar */
document.addEventListener('click', (e) => {
  const dd = document.getElementById('slotDropdown');
  if (dd && !dd.contains(e.target)) tutupSlot();
});

/* =========================
   LOAD LAPORAN
========================= */

async function loadLaporan() {
  const list = document.getElementById('list-laporan');

  list.innerHTML = `
        <div class="loading">
            Memuat laporan...
        </div>
    `;

  try {
    const res = await fetch('api/laporan_list.php?user_id=' + user.user_id);

    const json = await res.json();

    if (!json.success) {
      list.innerHTML = `
                <div class="empty">
                    Gagal memuat laporan
                </div>
            `;

      return;
    }

    if (json.data.length === 0) {
      list.innerHTML = `
                <div class="empty">
                    Belum ada laporan yang dibuat
                </div>
            `;

      return;
    }

    list.innerHTML = '';

    json.data.forEach((item) => {
      let editSection = '';

      if (item.sudah_edit == 0) {
        editSection = `
                    <button
                        class="btn-edit"
                        onclick='startEdit(${JSON.stringify(item)})'
                    >
                        Edit
                    </button>
                `;
      } else {
        editSection = `
                    <span class="edited">
                        ✓ Sudah Diedit
                    </span>
                `;
      }

      list.innerHTML += `
                <div class="laporan-card">

                    <div class="card-top">

                        <div>

                            <div class="card-title">
                                ${item.lokasi}
                            </div>

                            <div class="card-slot">
                                Slot ${item.nomor_slot}
                            </div>

                        </div>

                        <span class="badge ${item.kondisi_dilaporkan}">
                            ${item.kondisi_dilaporkan}
                        </span>

                    </div>

                    <div class="card-desc">
                        ${item.deskripsi || '-'}
                    </div>

                    <div class="card-time">
                        🕒 ${formatTanggal(item.waktu_laporan)}
                    </div>

                    <div class="card-actions">

                        ${editSection}

                        <button
                            class="btn-delete"
                            onclick="hapusLaporan(${item.laporan_id})"
                        >
                            Hapus
                        </button>

                    </div>

                </div>
            `;
    });
  } catch (err) {
    console.error(err);

    list.innerHTML = `
            <div class="empty">
                Tidak dapat terhubung ke server
            </div>
        `;
  }
}

/* =========================
   EDIT
========================= */

async function startEdit(data) {
  editId = data.laporan_id;

  openForm();

  document.getElementById('modal-title').textContent = 'Edit Laporan';

  document.getElementById('btn-submit').textContent = 'Update Laporan';

  document.getElementById('selKondisi').value = data.kondisi_dilaporkan;

  document.getElementById('txtDeskripsi').value = data.deskripsi || '';

  document.getElementById('selArea').value = data.parkir_id;

  await loadSlot();

  setSlot(data.slot_id, 'Slot ' + data.nomor_slot);
}

/* =========================
   SUBMIT
========================= */

async function submitLaporan() {
  const area = document.getElementById('selArea').value;

  const slotId = document.getElementById('selSlot').value;

  const kondisi = document.getElementById('selKondisi').value;

  const deskripsi = document.getElementById('txtDeskripsi').value.trim();

  const error = document.getElementById('form-error');

  const btn = document.getElementById('btn-submit');

  error.textContent = '';

  if (!area) {
    error.textContent = 'Pilih area parkir';

    return;
  }

  if (!slotId) {
    error.textContent = 'Pilih slot parkir';

    return;
  }

  btn.disabled = true;

  let url = '';
  let body = {};

  if (editId) {
    url = 'api/laporan_update.php';

    body = {
      laporan_id: editId,
      user_id: user.user_id,
      slot_id: slotId,
      kondisi: kondisi,
      deskripsi: deskripsi,
    };
  } else {
    url = 'api/laporan_create.php';

    body = {
      user_id: user.user_id,
      slot_id: slotId,
      kondisi: kondisi,
      deskripsi: deskripsi,
    };
  }

  try {
    const res = await fetch(url, {
      method: 'POST',

      headers: {
        'Content-Type': 'application/json',
      },

      body: JSON.stringify(body),
    });

    const json = await res.json();

    if (!json.success) {
      error.textContent = json.message || 'Terjadi kesalahan';

      btn.disabled = false;

      return;
    }

    closeForm();

    loadLaporan();
  } catch (err) {
    console.error(err);

    error.textContent = 'Tidak dapat terhubung ke server';

    btn.disabled = false;
  }
}

/* =========================
   DELETE
========================= */

async function hapusLaporan(id) {
  const konfirmasi = confirm('Yakin ingin menghapus laporan ini?');

  if (!konfirmasi) return;

  try {
    const res = await fetch('api/laporan_delete.php', {
      method: 'POST',

      headers: {
        'Content-Type': 'application/json',
      },

      body: JSON.stringify({
        laporan_id: id,
        user_id: user.user_id,
      }),
    });

    const json = await res.json();

    if (!json.success) {
      alert(json.message || 'Gagal menghapus laporan');

      return;
    }

    loadLaporan();
  } catch (err) {
    console.error(err);

    alert('Tidak dapat terhubung ke server');
  }
}

/* =========================
   FORMAT TANGGAL
========================= */

function formatTanggal(waktu) {
  if (!waktu) return '-';

  const date = new Date(waktu);

  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

/* =========================
   INIT
========================= */

document.addEventListener('DOMContentLoaded', () => {
  loadParkir();
  loadLaporan();
});
