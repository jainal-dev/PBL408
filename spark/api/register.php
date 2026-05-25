<?php
/* API REGISTER */

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

/* Hanya terima POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

  http_response_code(405);

  echo json_encode([
    "success" => false,
    "message" => "Method tidak diizinkan"
  ]);

  exit;
}

/* Baca input JSON */
$input = json_decode(
  file_get_contents("php://input"),
  true
);

$nama       = trim($input["nama"]       ?? "");
$email      = trim($input["email"]      ?? "");
$password   = $input["password"]        ?? "";
$konfirmasi = $input["konfirmasi"]      ?? "";

/* Validasi input */
if ($nama === "" || $email === "" || $password === "" || $konfirmasi === "") {

  http_response_code(400);

  echo json_encode([
    "success" => false,
    "message" => "Semua field wajib diisi"
  ]);

  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

  http_response_code(400);

  echo json_encode([
    "success" => false,
    "message" => "Format email tidak valid"
  ]);

  exit;
}

if (strlen($password) < 6) {

  http_response_code(400);

  echo json_encode([
    "success" => false,
    "message" => "Password minimal 6 karakter"
  ]);

  exit;
}

if ($password !== $konfirmasi) {

  http_response_code(400);

  echo json_encode([
    "success" => false,
    "message" => "Konfirmasi password tidak cocok"
  ]);

  exit;
}

/* Simpan ke database */
try {

  /* Cek apakah email sudah dipakai */
  $stmt = $pdo->prepare(
    "SELECT user_id FROM user WHERE email = :email LIMIT 1"
  );

  $stmt->execute([":email" => $email]);

  if ($stmt->fetch()) {

    http_response_code(409);

    echo json_encode([
      "success" => false,
      "message" => "Email sudah terdaftar"
    ]);

    exit;
  }

  /* Hash password dengan bcrypt */
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  /* Insert user baru */
  $stmt = $pdo->prepare(
    "INSERT INTO user (nama, email, password, role)
     VALUES (:nama, :email, :password, 'pengguna')"
  );

  $stmt->execute([
    ":nama"     => $nama,
    ":email"    => $email,
    ":password" => $hashedPassword
  ]);

  $userId = $pdo->lastInsertId();

  /* Sukses */
  echo json_encode([
    "success" => true,
    "message" => "Pendaftaran berhasil",
    "user" => [
      "user_id" => (int) $userId,
      "nama"    => $nama,
      "email"   => $email,
      "role"    => "pengguna"
    ]
  ]);

} catch (PDOException $e) {

  http_response_code(500);

  echo json_encode([
    "success" => false,
    "message" => "Error server: " . $e->getMessage()
  ]);
}
