<?php
include "db_connect.php";
header('Content-Type: application/json');

if (!isset($_POST['idAduan'])) {
    echo json_encode(['error' => 'No ID']);
    exit;
}

$idAduan = $_POST['idAduan'];

$sql = "
    SELECT 
        a.idAduan,
        a.jenisMasalah,
        a.tarikhAduan,
        a.masaAduan,
        a.noIC,
        u.namaUser,
        a.lokasi,
        a.peneranganMasalah,
        a.attachment,
        a.idStatus,
        a.noICTechnician
    FROM aduan a
    LEFT JOIN user u ON a.noIC = u.noIC
    WHERE a.idAduan = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idAduan);
$stmt->execute();

$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
