<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
include "db_connect.php";

if (!isset($_SESSION['noIC']) || $_SESSION['idPeranan'] !== '00') {
    header("Location: login.html");
    exit;
}

$namaUser = $_SESSION['namaUser'];
$noIC     = $_SESSION['noIC'];
$idPeranan = $_SESSION['idPeranan'];
$jabatan  = $_SESSION['jabatan'] ; 
$idPeranan = $_SESSION['idPeranan']; // Contoh: '00', '01', dll


$tarikhAduan = date("Y-m-d");
$masaAduan   = date("H:i:s");
$namaStatus = "Pending";
$attachment = "";
$msg = "";
$perananNama = "";
$sqlPeranan = "SELECT namaPeranan FROM peranan WHERE idPeranan = '$idPeranan'";
$resultPeranan = mysqli_query($conn, $sqlPeranan);

if ($row = mysqli_fetch_assoc($resultPeranan)) {
    $perananNama = $row['namaPeranan']; // Contoh: 'Pengguna', 'Admin'
} else {
    $perananNama = "Unknown";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $jenisMasalah = mysqli_real_escape_string($conn, $_POST['jenis_masalah']);
    $penerangan = mysqli_real_escape_string($conn, $_POST['penerangan']);

    // Handle optional file upload
    if (!empty($_FILES['attachment']['name'])) {
        $filename = basename($_FILES['attachment']['name']);
        $target = "uploads/" . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
            $attachment = $filename;
        }
    }

    $sql = "INSERT INTO aduan 
        (namaUser, noIC, idPeranan, jabatan, lokasi, jenisMasalah, peneranganMasalah, idStatus, tarikhAduan, masaAduan, attachment)
        VALUES (
            '$namaUser',
            '$noIC',
            '$idPeranan',
            '$jabatan',
            '$lokasi',
            '$jenisMasalah',
            '$penerangan',
             1,
            '$tarikhAduan',
            '$masaAduan',
            '$attachment'
        )";

    if (mysqli_query($conn, $sql)) {
        header("Location: Status_Aduan_User.php");
        exit;
    } else {
        echo "Gagal hantar aduan: " . mysqli_error($conn);
    }
}
?>




<html>
    <head>
<title>e-ICT Aduan</title>

<style>
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url("img/bg.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    
    position: relative;
    overflow: hidden;
}
.app {
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: transparent;
}

.topbar {
    height: 50px;
    background: #00bcd4;
    display: flex;
    align-items: center;
    padding: 0 20px;
    border-bottom: none;
    flex-shrink: 0;
}

.topbar .system {
    font-weight: bold;
    color: #fff;
    margin-right: 35px;
    font-size: 30px;
}

.topbar .page-title {
    font-weight: bold;
    color: #000;
    margin-left:30px; 
    font-size: 25px;
}

.layout {
    display: flex;
    flex: 1;
    overflow: hidden;
}

.sidebar {
    width: 240px;
    background: #e6e6e6;
    border-right: none;
    flex-shrink: 0;

    display: flex;
    flex-direction: column;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border-bottom: 1px solid #000;
}

.user-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.menu a {
    display: block;
    padding: 15px 18px;
    text-decoration: none;
    color: #000;
    border-bottom: 1px solid #000;
}

.menu a:hover {
    background: #d9d9d9;
}

.menu a.active {
    color: #1e40ff;
    font-weight: bold;
    background: transparent;
}

.content {
    flex: 1;
    padding: 30px;
    background: transparent;
    overflow: auto;
}

.aduan-form {
    background: #e0e0e0;
    padding: 20px;
    border: 2px solid #000;
    max-width: 900px;
}

.aduan-form label {
    width: 150px;
    font-weight: bold;
}

.form-row {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.form-row input[type="text"],
.form-row select,
.form-row textarea {
    padding: 6px;
    width: 250px;
}

.form-row textarea {
    width: 500px;
}

.form-submit {
    text-align: right;
    margin-top: 20px;
}

.form-submit button {
    padding: 8px 25px;
    border-radius: 10px;
    border: none;
    background: #fff;
    font-weight: bold;
    cursor: pointer;
}

.menu {
    flex: 1; 
}

.sidebar-logout {
    padding: 15px;
}

.logout-btn {
    width: 100%;
    padding: 8px 0;
    border: none;
    border-radius: 8px;
    background: #4dedffff;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="app">
    <div class="topbar">
        <div class="system">e-ICT Aduan</div>
        <div class="page-title">Status Aduan</div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="user-info">
                <img src="img/profile.jpg" alt="User">
                <div>
                    <strong><?= htmlspecialchars($namaUser); ?></strong><br>
                    <small><?= htmlspecialchars($perananNama); ?></small>
                </div>
            </div>

            <div class="menu">
                <a href="Status_Aduan_User.php">Status Aduan</a>
                <a href="Hantar_Aduan_User.php" class="active">Hantar Aduan</a>
            </div>

            <div class="sidebar-logout">
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <div class="content">
            <div class="aduan-form">
                <?php if ($msg != ""): ?>
                    <div class="msg"><?= $msg; ?></div>
                <?php endif; ?>

                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-row">
                        <label>Nama:</label>
                        <input type="text" value="<?= htmlspecialchars($namaUser); ?>" readonly>

                        <label>Bahagian / Unit:</label>
                        <input type="text" value="<?= htmlspecialchars($jabatan); ?>" readonly>
                    </div>

                    <div class="form-row">
                        <label>Lokasi:</label>
                        <input type="text" name="lokasi" placeholder="Contoh: Unit Sumber Manusia">
                    </div>

                    <div class="form-row">
                        <label>Jenis Masalah:</label>
                        <select name="jenis_masalah">
                            <option value="">-- Pilih --</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Printer">Printer</option>
                            <option value="Software">Software</option>
                            <option value="Network">Network</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label>Penerangan Masalah:</label>
                        <textarea name="penerangan" rows="5" placeholder="Terangkan masalah anda di sini"></textarea>
                    </div>

                    <div class="form-row">
                        <label>Attachment (Pilihan):</label>
                        <input type="file" id="attachment" name="attachment">
                        <button type="button" class="remove-btn" onclick="document.getElementById('attachment').value = ''">Buang Fail</button>
                    </div>

                    <div class="form-submit">
                        <button type="submit">Hantar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById("removeFile").addEventListener("click", function () {
    const fileInput = document.getElementById("attachment");
    fileInput.value = ""; // buang file

    alert("Attachment telah dibuang");
});
</script>


</body>
</html>
 
