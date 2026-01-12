<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dbaduan");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$noIC       = $data['noIC'];
$nama       = $data['namaUser'];
$jawatan    = $data['jawatan'];
$idJabatan  = (int)$data['jabatan'];
$noOffice   = $data['noOffice'];
$emel       = $data['emel'];
$kataLaluan = $data['kataLaluan'];
$isKetua    = $data['isKetua'];

if ($isKetua === 'yes') {

    // 👉 KETUA UNIT
    $sql = "INSERT INTO ketuaunit
    (noICKetua, namaKetua, jawatanKetua, noOfficeKetua, emelKetua, kataLaluanKetua, idJabatan)
    VALUES
    ('$noIC','$nama','$jawatan','$noOffice','$emel','$kataLaluan',$idJabatan)";

    $redirect = "ketuaunitdashboard.php";

} else {

    // 👉 USER BIASA
    $sql = "INSERT INTO user
    (noIC, namaUser, jawatan, noOffice, emel, kataLaluan, idJabatan)
    VALUES
    ('$noIC','$nama','$jawatan','$noOffice','$emel','$kataLaluan',$idJabatan)";

    $redirect = "Status_Aduan_User.php";
}

if ($conn->query($sql)) {
    echo json_encode([
        "success" => true,
        "redirect" => $redirect
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
}

$conn->close();
