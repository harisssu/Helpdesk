<?php
session_start();
include "db_connect.php";

$noIC = isset($_POST['noIC']) ? $_POST['noIC'] : '';
$userType = isset($_POST['userType']) ? $_POST['userType'] : '';

if(empty($noIC) || empty($userType)){
    die("Data tidak lengkap");
}

switch($userType){
    case 'user':
        $table = 'user';
        $colIC = 'noIC';
        break;
    case 'admin':
        $table = 'admin';
        $colIC = 'noICAdmin';
        break;
    case 'technician':
        $table = 'technician';
        $colIC = 'noICTechnician';
        break;
    case 'ketuaunit':
        $table = 'ketuaunit';
        $colIC = 'noICKetua';
        break;
    default:
        die("Jenis pengguna tidak sah");
}

$noIC_escaped = mysqli_real_escape_string($conn, $noIC);
$sql = "DELETE FROM $table WHERE $colIC = '$noIC_escaped'";

if(mysqli_query($conn, $sql)){
    echo "Pengguna berjaya dibuang";
} else {
    echo "Ralat: " . mysqli_error($conn);
}
