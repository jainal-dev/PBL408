<?php

header("Content-Type: application/json");

require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id  = $data["user_id"]  ?? 0;
$nama     = trim($data["nama"]  ?? "");
$email    = trim($data["email"] ?? "");
$password = $data["password"] ?? ""; // opsional

/* validasi */
if (!$user_id || $nama === "" || $email === "") {
    echo json_encode([
        "success" => false,
        "message" => "Nama dan email wajib diisi"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Format email tidak valid"
    ]);
    exit;
}

if ($password !== "" && strlen($password) < 6) {
    echo json_encode([
        "success" => false,
        "message" => "Password minimal 6 karakter"
    ]);
    exit;
}

try {

    /* email tidak boleh dipakai user lain */
    $cek = $pdo->prepare("
        SELECT user_id
        FROM user
        WHERE email = ?
        AND user_id != ?
        LIMIT 1
    ");
    $cek->execute([$email, $user_id]);

    if ($cek->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Email sudah dipakai pengguna lain"
        ]);
        exit;
    }

    /* update (password hanya jika diisi) */
    if ($password !== "") {

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            UPDATE user
            SET nama = ?, email = ?, password = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$nama, $email, $hash, $user_id]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE user
            SET nama = ?, email = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$nama, $email, $user_id]);
    }

    /* ambil data terbaru (tanpa password) */
    $get = $pdo->prepare("
        SELECT user_id, nama, email, role
        FROM user
        WHERE user_id = ?
    ");
    $get->execute([$user_id]);

    echo json_encode([
        "success" => true,
        "message" => "Profil berhasil diperbarui",
        "user" => $get->fetch()
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
