<?php
include "db_connect.php";
header('Content-Type: application/json');

if(isset($_POST['noIC'], $_POST['namaUser'], $_POST['emel'], $_POST['idJabatan'], $_POST['idPeranan'], $_POST['kataLaluan'], $_POST['table'])) {

    $noIC = $_POST['noIC'];
    $table = $_POST['table'];
    $namaUser = $_POST['namaUser'];
    $emel = $_POST['emel'];
    $idJabatan = $_POST['idJabatan'];
    $idPeranan = $_POST['idPeranan'];
    $kataLaluan = $_POST['kataLaluan'];

    $tables = [
        'user' => ['noIC'=>'noIC', 'name'=>'namaUser', 'email'=>'emel', 'idJabatan'=>'idJabatan', 'idPeranan'=>'idPeranan', 'password'=>'kataLaluan'],
        'admin' => ['noIC'=>'noICAdmin', 'name'=>'namaAdmin', 'email'=>'emelAdmin', 'idJabatan'=>'idJabatan', 'idPeranan'=>'idPeranan', 'password'=>'kataLaluan'],
        'technician' => ['noIC'=>'noICTechnician', 'name'=>'namaTechnician', 'email'=>'emelTechnician', 'idJabatan'=>'idJabatan', 'idPeranan'=>'idPeranan', 'password'=>'kataLaluan'],
        'ketuaunit' => ['noIC'=>'noICKetua', 'name'=>'namaKetua', 'email'=>'emelKetua', 'idJabatan'=>'idJabatan', 'idPeranan'=>'idPeranan', 'password'=>'kataLaluan']
    ];

    if(!isset($tables[$table])){
        echo json_encode(['status'=>'error','message'=>'Ralat: Table tidak dikenali!']);
        exit;
    }

    $cols = $tables[$table];

    $sql = "UPDATE $table SET 
                ".$cols['name']." = ?,
                ".$cols['email']." = ?,
                ".$cols['idJabatan']." = ?,
                ".$cols['idPeranan']." = ?,
                ".$cols['password']." = ?
            WHERE ".$cols['noIC']." = ?";

    $stmt = $conn->prepare($sql);
    if(!$stmt){
        echo json_encode(['status'=>'error','message'=>'SQL Prepare Error: '.$conn->error]);
        exit;
    }

    $stmt->bind_param("ssiiis", $namaUser, $emel, $idJabatan, $idPeranan, $kataLaluan, $noIC);

    if($stmt->execute()){
        echo json_encode(['status'=>'success','message'=>'Maklumat pengguna berjaya dikemaskini!']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Ralat semasa kemaskini: '.$stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['status'=>'error','message'=>'Ralat: Data tidak lengkap!']);
exit;
?>
