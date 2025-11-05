<?php
// api/get_user.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/send_mail.php';


$userId = $_GET['id'] ?? null;
if (!$userId || !ctype_digit((string)$userId)) {
    http_response_code(400);
    send_json(["error" => "ID pengguna tidak valid"]);
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, birthdate, created_at AS registration_date FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        send_json(["error" => "Pengguna tidak ditemukan"]);
    }

    // Ubah key menjadi kompatibel dengan success.html
    $user['full_name'] = $user['name'];
    send_json($user);

} catch (PDOException $e) {
    http_response_code(500);
    send_json(["error" => "Gagal mengambil data pengguna: " . $e->getMessage()]);
}
?>
