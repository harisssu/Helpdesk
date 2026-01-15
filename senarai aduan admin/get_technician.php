<?php
include "db_connect.php";
header('Content-Type: application/json');

$sql = "SELECT noICTechnician, namaTechnician FROM technician ORDER BY namaTechnician";
$result = mysqli_query($conn, $sql);

$techs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $techs[] = $row;
}

echo json_encode($techs);
