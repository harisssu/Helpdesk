<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dbaduan");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['noIC']) || empty($data['kataLaluan'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "No IC dan Kata Laluan diperlukan"
    ]);
    exit;
}

$noIC = trim($data['noIC']);
$password = $data['kataLaluan'];

$sql = "SELECT noIC, namaUser, kataLaluan, idPeranan, jabatan 
        FROM user 
        WHERE noIC = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $noIC);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode([
        "success" => false,
        "error" => "No IC atau Kata Laluan salah"
    ]);
    exit;
}

$user = $result->fetch_assoc();

if ($password !== $user['kataLaluan']) {
    echo json_encode([
        "success" => false,
        "error" => "No IC atau Kata Laluan salah"
    ]);
    exit;
}

$_SESSION['noIC'] = $user['noIC'];
$_SESSION['namaUser'] = $user['namaUser'];
$_SESSION['idPeranan'] = $user['idPeranan'];
$_SESSION['jabatan'] = $user['jabatan'];

$redirect = "";

switch ($user['idPeranan']) {
    case '00':
        $redirect = "Status_Aduan_User.php";
        break;

    case '01':
        $redirect = "dashboard_admin.php";
        break;

    case '02': 
        $redirect = "senarai_aduan_technician.php";
        break;

    case '03':
        $redirect = "1.php";
        break;

    default:
        $redirect = "login.html";
}

echo json_encode([
    "success" => true,
    "redirect" => $redirect
]);

$stmt->close();
$conn->close();

