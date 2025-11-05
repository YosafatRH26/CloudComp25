<?php
// api/utils.php
function send_json($data) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}
?>
