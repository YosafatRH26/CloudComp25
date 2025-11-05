<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_confirmation_email(string $toEmail, string $token, array $userData = []): bool {
    $mailHost = $_ENV['MAIL_HOST'] ?? '';
    $mailPort = $_ENV['MAIL_PORT'] ?? 587;
    $mailUser = $_ENV['MAIL_USERNAME'] ?? '';
    $mailPass = $_ENV['MAIL_PASSWORD'] ?? '';
    $mailEncryption = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
    $mailFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
    $mailFromName = $_ENV['MAIL_FROM_NAME'] ?? 'Cloud Computing 2025';
    $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/ComGraph';

    $name = $userData['name'] ?? '-';
    $email = $userData['email'] ?? $toEmail;

    $birthdate = (!empty($userData['birthdate']) && strtotime($userData['birthdate'])) ? date('d F Y', strtotime($userData['birthdate'])) : '-';
    $regDate = (!empty($userData['registration_date']) && strtotime($userData['registration_date'])) ? date('d F Y', strtotime($userData['registration_date'])) : '-';

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $mailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $mailUser;
        $mail->Password = $mailPass;
        $mail->SMTPSecure = $mailEncryption;
        $mail->Port = $mailPort;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom($mailFrom, $mailFromName);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Konfirmasi Registrasi - Cloud Computing 2025';

        $mail->Body = "
        <div style='font-family:Orbitron, sans-serif; background: radial-gradient(circle,#0f0f1e,#1a1a2e); padding:20px;'>
            <div style='position:relative; max-width:600px; margin:auto; background: rgba(20,20,40,0.7); border-radius:20px; padding:30px; text-align:center; box-shadow:0 0 20px #ff00ff,0 0 40px #00ffff inset; overflow:hidden;'>
                <h2 style='color:#ff00ff; font-size:1.8em; margin-bottom:20px;'>Terima kasih telah mendaftar!</h2>
                <p style='color:#ccc; font-size:16px;'>Silakan klik tombol berikut untuk mengaktifkan akun Anda:</p>
                <a href='{$appUrl}/api/confirm.php?token={$token}' target='_blank' style='display:inline-block; margin-top:20px; padding:15px 25px; border-radius:12px; background: linear-gradient(45deg,#ff00ff,#00ffff); color:#000; font-weight:700; text-decoration:none; box-shadow:0 0 15px #ff00ff,0 0 25px #00ffff;'>Konfirmasi Akun</a>
                <h3 style='color:#00ffff; margin-top:25px;'>Detail Registrasi Anda</h3>
                <table style='margin:15px auto; color:#ccc; font-size:14px; text-align:left; border-collapse:collapse; width:100%;'>
                    <tr><td style='padding:5px 10px; font-weight:600; color:#aaa;'>Nama Lengkap:</td><td style='padding:5px 10px;'>{$name}</td></tr>
                    <tr><td style='padding:5px 10px; font-weight:600; color:#aaa;'>Email:</td><td>{$email}</td></tr>
                    <tr><td style='padding:5px 10px; font-weight:600; color:#aaa;'>Tanggal Lahir:</td><td>{$birthdate}</td></tr>
                    <tr><td style='padding:5px 10px; font-weight:600; color:#aaa;'>Tanggal Registrasi:</td><td>{$regDate}</td></tr>
                </table>
                <p style='margin-top:20px; font-size:14px; color:#888;'>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email gagal dikirim: {$mail->ErrorInfo}");
        return false;
    }
}
