<?php
include "db_connect.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['idAduan'])) {
    echo json_encode(['error' => 'No ID']);
    exit;
}

$idAduan = (int)$_POST['idAduan'];

$sql = "
    SELECT 
        a.idAduan,
        a.jenisMasalah,
        a.tarikhAduan,
        a.masaAduan,
        a.noIC,
        a.noICKetua,

        COALESCE(u.namaUser, k.namaKetua, '-') AS namaPengadu,
        COALESCE(ju.namaJabatan, jk.namaJabatan, '-') AS namaJabatan,

        a.lokasi,
        COALESCE(a.peneranganMasalah, '-') AS peneranganMasalah,
        COALESCE(a.attachment, '') AS attachment,
        a.idStatus,
        COALESCE(s.namaStatus, '-') AS namaStatus,
        a.noICTechnician,

        a.notaTechnician,
        a.attachmentTechnician,
        a.tarikhmasaSiap


    FROM aduan a
    LEFT JOIN user u ON a.noIC = u.noIC
    LEFT JOIN jabatan ju ON u.idJabatan = ju.idJabatan

    LEFT JOIN ketuaunit k ON a.noICKetua = k.noICKetua
    LEFT JOIN jabatan jk ON k.idJabatan = jk.idJabatan

    LEFT JOIN status s ON a.idStatus = s.idStatus

    WHERE a.idAduan = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAduan);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode($row ? $row : ['error' => 'Not found']);
exit;
?>
