<?php 
// api/confirm.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/db.php';

$token = $_GET['token'] ?? '';
$result = [];

if (!$token) {
    $result = ['error' => 'Token Tidak Ditemukan'];
} else {
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = true WHERE token = ? AND is_verified = false RETURNING name, email");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $result = [
                'success' => 'Akun Berhasil Diverifikasi!',
                'user' => [
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ];
        } else {
            $result = ['error'=>'Token Tidak Valid atau Sudah Digunakan'];
        }
    } catch (PDOException $e) {
        $result = ['error'=>'Terjadi Kesalahan: '.$e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konfirmasi Akun - Cloud Computing 2025</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
<style>
html, body {
    height: 100%;
    margin:0;
    padding:0;
    font-family: 'Orbitron', sans-serif;
    background: radial-gradient(circle at top, #0f0f1e, #1a1a2e);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
body::-webkit-scrollbar { display: none; }
body { -ms-overflow-style: none; scrollbar-width: none; }

.container {
    position: relative;
    background: rgba(20,20,40,0.7);
    padding: 40px;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 0 20px #8a6cff, 0 0 40px #6c8aff inset;
    overflow: hidden;
}

.container::before {
    content:'';
    position:absolute;
    top:-2px; left:-2px; right:-2px; bottom:-2px;
    border: 2px solid #000000;
    border-radius: 20px;
    pointer-events: none;
    box-shadow: 0 0 20px #680707, 0 0 40px #6c8aff;
}

h1 { font-size:2em; margin-bottom:15px; }
p { color:#ccc; margin-bottom:20px; }
.button {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 12px;
    background: linear-gradient(45deg,#ff00ff,#00ffff);
    color: #000;
    font-weight:700;
    text-decoration: none;
    transition: all 0.3s;
}
.button:hover { transform: translateY(-2px); filter: brightness(1.2); }
.success { color:#4caf50; font-weight:700; }
.error { color:#ff6b6b; font-weight:700; }

/* Responsif */
@media (max-width: 480px) {
    .container { padding: 25px; }
    h1 { font-size:1.6em; }
    p { font-size:0.9em; }
    .button { padding: 10px 20px; font-size:0.95em; }
}
</style>
</head>
<body>
<div class="container">
<?php if(isset($result['success'])): ?>
    <h1 class="success"><?= htmlspecialchars($result['success']) ?></h1>
    <p>Nama: <strong><?= htmlspecialchars($result['user']['name']) ?></strong><br>Email: <strong><?= htmlspecialchars($result['user']['email']) ?></strong></p>
    <a class="button" href="../index.html">← Kembali ke Halaman Registrasi</a>
<?php else: ?>
    <h1 class="error"><?= htmlspecialchars($result['error']) ?></h1>
    <p>Silakan cek kembali link konfirmasi Anda atau registrasi ulang jika belum menerima email konfirmasi.</p>
    <a class="button" href="../index.html">← Kembali ke Halaman Registrasi</a>
<?php endif; ?>
</div>
</body>
</html>
