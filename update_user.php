<?php
include "db_connect.php";

if(isset($_POST['noIC'])){
    $noIC = $_POST['noIC'];
    $namaUser = $_POST['namaUser'];
    $emel = $_POST['emel'];
    $idJabatan = $_POST['idJabatan'];
    $idPeranan = $_POST['idPeranan'];
    $kataLaluan = $_POST['kataLaluan'];

    $sql = "UPDATE user SET 
                namaUser='$namaUser', 
                emel='$emel', 
                idJabatan='$idJabatan',
                kataLaluan='$kataLaluan',
                idPeranan='$idPeranan' 
            WHERE noIC='$noIC'";

    if(mysqli_query($conn, $sql)){
        echo "Maklumat pengguna berjaya dikemaskini!";
    } else {
        echo "Ralat: " . mysqli_error($conn);
    }
}
?>
