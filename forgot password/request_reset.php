<?php
session_start();
header('Content-Type: application/json');


date_default_timezone_set("Asia/Kuala_Lumpur");

require_once __DIR__ . "/db_connect.php";
require_once __DIR__ . "/send_mail.php";

$email = isset($_POST['email']) ? $_POST['email'] : '';
$email = trim($email);

// buang whitespace biasa
$email = str_replace(array(" ", "\t", "\r", "\n"), "", $email);

// buang invisible chars (bytes) yang selalu wujud bila copy paste
$email = str_replace(array(
    "\xC2\xA0",          // NBSP
    "\xE2\x80\x8B",      // zero-width space
    "\xE2\x80\x8C",      // zero-width non-joiner
    "\xE2\x80\x8D",      // zero-width joiner
    "\xEF\xBB\xBF"       // UTF-8 BOM
), "", $email);

// buang char pelik yang bukan email
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if ($email === '') {
    echo json_encode(array("success" => false, "error" => "Sila isi emel."));
    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array("success" => false, "error" => "Format emel tidak sah."));
    exit;
}



// cari akaun berdasarkan email (ikut table role)
function findAccountByEmail($conn, $email) {

    // admin
    $stmt = $conn->prepare("SELECT noICAdmin AS noIC, namaAdmin AS nama, emelAdmin AS emel, 'admin' AS role_type FROM admin WHERE emelAdmin=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows === 1) return $r->fetch_assoc();

    // ketuaunit
    $stmt = $conn->prepare("SELECT noICKetua AS noIC, namaKetua AS nama, emelKetua AS emel, 'ketuaunit' AS role_type FROM ketuaunit WHERE emelKetua=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows === 1) return $r->fetch_assoc();

    // technician
    $stmt = $conn->prepare("SELECT noICTechnician AS noIC, namaTechnician AS nama, emelTechnician AS emel, 'technician' AS role_type FROM technician WHERE emelTechnician=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows === 1) return $r->fetch_assoc();

    // user
    $stmt = $conn->prepare("SELECT noIC AS noIC, namaUser AS nama, emel AS emel, 'user' AS role_type FROM user WHERE emel=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows === 1) return $r->fetch_assoc();

    return null;
}

// security: bagi mesej sama (elak orang test email wujud/tak)
$genericMsg = "Jika emel wujud dalam sistem, link reset telah dihantar. Sila semak inbox/spam. Link sah selama 10 minit.";

$acc = findAccountByEmail($conn, $email);
if (!$acc) {
    echo json_encode(["success" => true, "message" => $genericMsg]);
    exit;
}

// generate token
// generate token (PHP 5 compatible)
$token = md5(uniqid(rand(), true)) . md5(uniqid(rand(), true));
$tokenHash = sha1($token);
$expiresAt = date("Y-m-d H:i:s", time() + 10*60);


// invalidate token lama (best practice)
$stmt = $conn->prepare("UPDATE password_reset SET used=1 WHERE email=? AND role_type=? AND used=0");
$stmt->bind_param("ss", $acc['emel'], $acc['role_type']);
$stmt->execute();

// simpan token baru
$stmt = $conn->prepare("INSERT INTO password_reset (email, role_type, noIC, token_hash, expires_at, used) VALUES (?,?,?,?,?,0)");
$stmt->bind_param("sssss", $acc['emel'], $acc['role_type'], $acc['noIC'], $tokenHash, $expiresAt);


if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "Gagal simpan reset token."]);
    exit;
}

// bina link (ikut folder project dalam htdocs)
$baseUrl = "http://localhost/" . rawurlencode("E-ICT ADUAN");
$resetLink = $baseUrl . "/reset_password.php?token=" . urlencode($token);


// hantar email
$ok = sendResetLink($acc['emel'], $acc['nama'], $resetLink);

// walaupun gagal hantar, dari segi security kita boleh still bagi generic msg
// tapi untuk assignment, kita boleh bagi error sebenar supaya senang debug
if (!$ok) {
    echo json_encode(["success" => false, "error" => "Gagal hantar emel. Semak setting SMTP / App Password."]);
    exit;
}

echo json_encode(["success" => true, "message" => $genericMsg]);
exit;
