<?php
/* API LOGIN */

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

$email = trim($input["email"] ?? "");

$password = $input["password"] ?? "";

/* Validasi input */
if ($email === "" || $password === "") {

  http_response_code(400);

  echo json_encode([
    "success" => false,
    "message" => "Email dan password wajib diisi"
  ]);

  exit;
}

/* Cek user di database */
try {

  $stmt = $pdo->prepare(
    "SELECT user_id, nama, email, password, role
     FROM user
     WHERE email = :email
     LIMIT 1"
  );

  $stmt->execute([
    ":email" => $email
  ]);

  $user = $stmt->fetch();

  /* Cek user ada DAN password cocok (bcrypt verify) */
  if (!$user || !password_verify($password, $user["password"])) {

    http_response_code(401);

    echo json_encode([
      "success" => false,
      "message" => "Email atau password salah"
    ]);

    exit;
  }

  /* Jangan ikutkan password di response */
  unset($user["password"]);

  /* Login berhasil */
  echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "user"    => $user
  ]);

} catch (PDOException $e) {

  http_response_code(500);

  echo json_encode([
    "success" => false,
    "message" => "Error server: " . $e->getMessage()
  ]);
}
