<?php
include "db_connect.php";
header('Content-Type: application/json');

if (!isset($_POST['noIC'], $_POST['type'])) {
    echo json_encode([]);
    exit;
}

$noIC = $_POST['noIC'];
$type = $_POST['type'];

switch ($type) {
    case 'user':
        $sql = "SELECT noIC, namaUser, emel, idJabatan, idPeranan, kataLaluan FROM user WHERE noIC=?";
        break;

    case 'admin':
        $sql = "SELECT noICAdmin AS noIC, namaAdmin AS namaUser, emelAdmin AS emel, 
                       idJabatan, idPeranan, kataLaluanAdmin AS kataLaluan
                FROM admin WHERE noICAdmin=?";
        break;

    case 'technician':
        $sql = "SELECT noICTechnician AS noIC, namaTechnician AS namaUser, emelTechnician AS emel, 
                       idJabatan, idPeranan, kataLaluanTechnician AS kataLaluan
                FROM technician WHERE noICTechnician=?";
        break;

    case 'ketuaunit':
        $sql = "SELECT noICKetua AS noIC, namaKetua AS namaUser, emelKetua AS emel, 
                       idJabatan, idPeranan, kataLaluanKetua AS kataLaluan
                FROM ketuaunit WHERE noICKetua=?";
        break;
}


$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'error' => 'Prepare failed',
        'mysql_error' => $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $noIC);
$stmt->execute();

$stmt->bind_result(
    $r_noIC,
    $r_nama,
    $r_emel,
    $r_jabatan,
    $r_peranan,
    $r_pass
);

if ($stmt->fetch()) {
    echo json_encode([
        'noIC' => $r_noIC,
        'namaUser' => $r_nama,
        'emel' => $r_emel,
        'idJabatan' => $r_jabatan,
        'idPeranan' => $r_peranan,
        'kataLaluan' => $r_pass
    ]);
} else {
    echo json_encode([]);
}

$stmt->close();
