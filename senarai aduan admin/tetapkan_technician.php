<?php
include "db_connect.php";
header('Content-Type: application/json');

if (!isset($_POST['idAduan'], $_POST['noICTechnician'])) {
    echo json_encode(['success' => false, 'msg' => 'Data tidak lengkap']);
    exit;
}

$idAduan = $_POST['idAduan'];
$noICTechnician = $_POST['noICTechnician'];

$idStatus = 2;

$sql = "
    UPDATE aduan 
    SET noICTechnician = ?, idStatus = ?
    WHERE idAduan = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $noICTechnician, $idStatus, $idAduan);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Gagal kemaskini']);
}
