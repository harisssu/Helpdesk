<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['noIC']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$idJabatan = isset($_POST['idJabatan']) ? trim($_POST['idJabatan']) : '';
$search = isset($_POST['search']) ? trim($_POST['search']) : '';

$searchSafe = mysqli_real_escape_string($conn, $search);

/* Filter jabatan guna outer query field (allUsers.idJabatan) */
$jabatanFilterSql = "";
if ($idJabatan !== '') {
    $jabatanId = intval($idJabatan);
    $jabatanFilterSql = " AND allUsers.idJabatan = $jabatanId";
}

$sql = "
SELECT * FROM (
    SELECT 
        u.noIC AS noIC,
        u.namaUser AS namaUser,
        u.emel AS emel,
        j.namaJabatan AS namaJabatan,
        p.namaPeranan AS namaPeranan,
        u.idJabatan AS idJabatan,
        u.idPeranan AS idPeranan,
        'user' AS userType
    FROM user u
    LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON u.idPeranan = p.idPeranan

    UNION ALL

    SELECT 
        a.noICAdmin AS noIC,
        a.namaAdmin AS namaUser,
        a.emelAdmin AS emel,
        j.namaJabatan AS namaJabatan,
        p.namaPeranan AS namaPeranan,
        a.idJabatan AS idJabatan,
        a.idPeranan AS idPeranan,
        'admin' AS userType
    FROM admin a
    LEFT JOIN jabatan j ON a.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON a.idPeranan = p.idPeranan

    UNION ALL

    SELECT 
        t.noICTechnician AS noIC,
        t.namaTechnician AS namaUser,
        t.emelTechnician AS emel,
        j.namaJabatan AS namaJabatan,
        p.namaPeranan AS namaPeranan,
        t.idJabatan AS idJabatan,
        t.idPeranan AS idPeranan,
        'technician' AS userType
    FROM technician t
    LEFT JOIN jabatan j ON t.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON t.idPeranan = p.idPeranan

    UNION ALL

    SELECT 
        k.noICKetua AS noIC,
        k.namaKetua AS namaUser,
        k.emelKetua AS emel,
        j.namaJabatan AS namaJabatan,
        p.namaPeranan AS namaPeranan,
        k.idJabatan AS idJabatan,
        k.idPeranan AS idPeranan,
        'ketuaunit' AS userType
    FROM ketuaunit k
    LEFT JOIN jabatan j ON k.idJabatan = j.idJabatan
    LEFT JOIN peranan p ON k.idPeranan = p.idPeranan
) AS allUsers
WHERE allUsers.namaUser LIKE '%$searchSafe%'
$jabatanFilterSql
ORDER BY allUsers.namaUser ASC
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'SQL Error',
        'message' => mysqli_error($conn)
    ]);
    exit;
}

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
exit;
