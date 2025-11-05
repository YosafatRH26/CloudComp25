<?php
// api/register.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/send_mail.php';

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) send_json(["error" => "Invalid request"]);

$name = trim($data['fullName'] ?? '');
$email = trim($data['studentEmail'] ?? '');
$passwordPlain = $data['password'] ?? '';
$birthdate = $data['birthdate'] ?? '';

if (!$name || !$email || !$passwordPlain || !$birthdate) {
    send_json(["error" => "Semua field wajib diisi."]);
}

$password = password_hash($passwordPlain, PASSWORD_BCRYPT);
$token = generate_token();
$registration_date = date('Y-m-d H:i:s');

try {
    // Simpan ke database
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, birthdate, token, is_verified, created_at)
        VALUES (?, ?, ?, ?, ?, false, NOW())
        RETURNING id
    ");
    $stmt->execute([$name, $email, $password, $birthdate, $token]);
    $userId = $stmt->fetchColumn();

    // **Persiapkan data user untuk email**
    $userData = [
        'name' => $name,
        'email' => $email,
        'birthdate' => $birthdate,
        'registration_date' => $registration_date
    ];

    // Kirim email konfirmasi dengan data user
    $emailSent = send_confirmation_email($email, $token, $userData);

    send_json([
        "success" => "Pendaftaran berhasil." . 
                     ($emailSent ? " Email konfirmasi telah dikirim ke $email." : " Namun email konfirmasi gagal dikirim."),
        "redirectUrl" => "/ComGraph/success.html?id={$userId}"
    ]);

} catch (PDOException $e) {
    send_json(["error" => "Database error: " . $e->getMessage()]);
}
