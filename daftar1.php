<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dbaduan");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ['noIC','namaUser','jawatan','jabatan','noOffice','emel','kataLaluan','isAdmin'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "$field is required"]);
        exit;
    }
}

$noIC = $conn->real_escape_string($data['noIC']);
$namaUser = $conn->real_escape_string($data['namaUser']);
$jawatan = $conn->real_escape_string($data['jawatan']);
$jabatan = $conn->real_escape_string($data['jabatan']);
$noOffice = $conn->real_escape_string($data['noOffice']);
$emel = $conn->real_escape_string($data['emel']);
$kataLaluan = $conn->real_escape_string($data['kataLaluan']);

$isAdmin = $data['isAdmin'];
$idPeranan = ($isAdmin === 'yes') ? '01' : '00';

$sql = "INSERT INTO `user`
(noIC, namaUser, jawatan, jabatan, noOffice, emel, kataLaluan, idPeranan)
VALUES
('$noIC', '$namaUser', '$jawatan', '$jabatan', '$noOffice', '$emel', '$kataLaluan', '$idPeranan')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Akaun berjaya didaftar"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
