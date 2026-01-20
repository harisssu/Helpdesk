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

        /* noIC boleh NULL sebab ketua unit guna noICKetua */
        a.noIC,
        a.noICKetua,

        /* Nama pengadu & jabatan */
        COALESCE(u.namaUser, k.namaKetua, '-') AS namaPengadu,
        COALESCE(ju.namaJabatan, jk.namaJabatan, '-') AS namaJabatan,

        a.lokasi,
        a.peneranganMasalah,
        a.attachment,
        a.idStatus,
        a.noICTechnician
    FROM aduan a
    LEFT JOIN user u ON a.noIC = u.noIC
    LEFT JOIN jabatan ju ON u.idJabatan = ju.idJabatan

    LEFT JOIN ketuaunit k ON a.noICKetua = k.noICKetua
    LEFT JOIN jabatan jk ON k.idJabatan = jk.idJabatan

    WHERE a.idAduan = ?
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAduan);
$stmt->execute();

$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
exit;
