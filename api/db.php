<?php
// api/db.php
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    http_response_code(500);
    echo json_encode(["error" => "File .env tidak ditemukan"]);
    exit;
}

$config = parse_ini_file($envPath, false, INI_SCANNER_RAW);

try {
    $pdo = new PDO(
        "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}",
        $config['DB_USER'],
        $config['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal koneksi ke database PostgreSQL: " . $e->getMessage()]);
    exit;
}
?>
