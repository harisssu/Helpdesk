<?php
session_start();
include "db_connect.php";

$noIC = $_POST['noIC'];
$namaUser = $_POST['namaUser'];
$kataLaluan = $_POST['kataLaluan'];
$idJabatan = $_POST['idJabatan'];
$emel = $_POST['emel'];
$perananType = $_POST['perananType'];

$idPeranan = 0;
$perRes = mysqli_query($conn, "SELECT idPeranan FROM peranan WHERE namaPeranan='".mysqli_real_escape_string($conn, ucfirst($perananType))."'");
if($perRow = mysqli_fetch_assoc($perRes)){
    $idPeranan = $perRow['idPeranan'];
}

switch($perananType){
    case 'user':
        $table = 'user';
        $noICField = 'noIC';
        $nameField = 'namaUser';
        $emailField = 'emel';
        $kataLaluanField = 'kataLaluan';
        break;
    case 'admin':
        $table = 'admin';
        $noICField = 'noICAdmin';
        $nameField = 'namaAdmin';
        $emailField = 'emelAdmin';
        $kataLaluanField = 'kataLaluanAdmin';
        break;
    case 'technician':
        $table = 'technician';
        $noICField = 'noICTechnician';
        $nameField = 'namaTechnician';
        $emailField = 'emelTechnician';
        $kataLaluanField = 'kataLaluanTechnician';
        break;
    case 'ketuaunit':
        $table = 'ketuaunit';
        $noICField = 'noICKetua';
        $nameField = 'namaKetua';
        $emailField = 'emelKetua';
        $kataLaluanField = 'kataLaluanKetua';
        break;
    default:
        die("Peranan tidak sah.");
}

$sql = "INSERT INTO $table 
        ($noICField, $nameField, $emailField, idJabatan, idPeranan, $kataLaluanField)
        VALUES (
            '".mysqli_real_escape_string($conn,$noIC)."',
            '".mysqli_real_escape_string($conn,$namaUser)."',
            '".mysqli_real_escape_string($conn,$emel)."',
            '".mysqli_real_escape_string($conn,$idJabatan)."',
            '".mysqli_real_escape_string($conn,$idPeranan)."',
            '".mysqli_real_escape_string($conn,$kataLaluan)."'
        )";

if(mysqli_query($conn, $sql)){
    echo "Pengguna berjaya ditambah.";
}else{
    echo "Ralat: " . mysqli_error($conn);
}
?>
