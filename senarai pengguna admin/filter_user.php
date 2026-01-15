<?php
session_start();
include "db_connect.php";

$idJabatan = isset($_POST['idJabatan']) ? $_POST['idJabatan'] : '';
$search = isset($_POST['search']) ? $_POST['search'] : '';

$search = mysqli_real_escape_string($conn, $search);

$jabatanFilter = ($idJabatan !== '') ? "AND idJabatan = " . intval($idJabatan) : "";

$sql = "
SELECT * FROM (
    SELECT u.noIC AS noIC, u.namaUser, u.emel, j.namaJabatan, p.namaPeranan, u.idJabatan, u.idPeranan, 'user' AS userType
    FROM user u
    LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON u.idPeranan = p.idPeranan

    UNION ALL

    SELECT a.noICAdmin, a.namaAdmin AS namaUser, a.emelAdmin AS emel, j.namaJabatan, p.namaPeranan, a.idJabatan, a.idPeranan, 'admin'
    FROM admin a
    LEFT JOIN jabatan j ON a.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON a.idPeranan = p.idPeranan

    UNION ALL

    SELECT t.noICTechnician, t.namaTechnician AS namaUser, t.emelTechnician AS emel, j.namaJabatan, p.namaPeranan, t.idJabatan, t.idPeranan, 'technician'
    FROM technician t
    LEFT JOIN jabatan j ON t.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON t.idPeranan = p.idPeranan

    UNION ALL

    SELECT k.noICKetua, k.namaKetua AS namaUser, k.emelKetua AS emel, j.namaJabatan, p.namaPeranan, k.idJabatan, k.idPeranan, 'ketuaunit'
    FROM ketuaunit k
    LEFT JOIN jabatan j ON k.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON k.idPeranan = p.idPeranan
) AS allUsers
WHERE namaUser LIKE '%$search%' $jabatanFilter
ORDER BY namaUser ASC
";

$result = mysqli_query($conn, $sql);

$users = [];
while($row = mysqli_fetch_assoc($result)){
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
