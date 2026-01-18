<?php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$conn = new mysqli("localhost", "root", "", "dbaduan");
if ($conn->connect_error) {
    echo json_encode(array("success"=>false,"error"=>"Database tidak boleh dihubungi"));
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$noIC = isset($data['noIC']) ? trim($data['noIC']) : '';
$pass = isset($data['kataLaluan']) ? trim($data['kataLaluan']) : '';

if ($noIC === '' || $pass === '') {
    echo json_encode(array("success"=>false,"error"=>"Sila isi No IC dan Kata Laluan"));
    exit;
}

$stmt = $conn->prepare("SELECT noICAdmin, namaAdmin, kataLaluanAdmin FROM admin WHERE noICAdmin = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $noIC);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($pass === $user['kataLaluanAdmin']) {
            $_SESSION['noIC'] = $user['noICAdmin'];
            $_SESSION['nama'] = $user['namaAdmin'];
            $_SESSION['role'] = 'admin';
            $redirect = "dashboard_admin.php";
            echo json_encode(array("success"=>true,"redirect"=>$redirect));
            exit;
        } else {
            echo json_encode(array("success"=>false,"error"=>"Kata laluan salah"));
            exit;
        }
    }
}

$stmt = $conn->prepare("SELECT noICKetua, namaKetua, kataLaluanKetua FROM ketuaunit WHERE noICKetua = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $noIC);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($pass === $user['kataLaluanKetua']) {
            $_SESSION['noIC'] = $user['noICKetua'];
            $_SESSION['nama'] = $user['namaKetua'];
            $_SESSION['idJabatan'] = $row['idJabatan'];
            $_SESSION['role'] = 'ketua unit';
            $redirect = "ketuaunitdashboard.php";
            echo json_encode(array("success"=>true,"redirect"=>$redirect));
            exit;
        } else {
            echo json_encode(array("success"=>false,"error"=>"Kata laluan salah"));
            exit;
        }
    }
}


$stmt = $conn->prepare("SELECT noICTechnician, namaTechnician, kataLaluanTechnician FROM technician WHERE noICTechnician = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $noIC);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($pass === $user['kataLaluanTechnician']) {
            $_SESSION['noIC'] = $user['noICTechnician'];
            $_SESSION['nama'] = $user['namaTechnician'];
            $_SESSION['role'] = 'technician';
            $redirect = "senarai_aduan_technician.php";
            echo json_encode(array("success"=>true,"redirect"=>$redirect));
            exit;
        } else {
            echo json_encode(array("success"=>false,"error"=>"Kata laluan salah"));
            exit;
        }
    }
}

$stmt = $conn->prepare("SELECT noIC, namaUser, kataLaluan, idJabatan FROM user WHERE noIC = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $noIC);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($pass === $user['kataLaluan']) {
            $_SESSION['noIC'] = $user['noIC'];
            $_SESSION['nama'] = $user['namaUser'];
            $_SESSION['idJabatan'] = $user['idJabatan'];
            $_SESSION['role'] = 'user';
            $redirect = "Status_Aduan_User.php";
            echo json_encode(array("success"=>true,"redirect"=>$redirect));
            exit;
        } else {
            echo json_encode(array("success"=>false,"error"=>"Kata laluan salah"));
            exit;
        }
    }
}

echo json_encode(array("success"=>false,"error"=>"No IC tidak dijumpai"));
exit;
