<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['noIC']) || $_SESSION['idPeranan'] !== '01') {
    header("Location: login.html");
    exit;
}

$namaUser = $_SESSION['namaUser'];
$noIC     = $_SESSION['noIC'];
$idPeranan = $_SESSION['idPeranan'];
$idJabatan = $_SESSION['idJabatan'];

// Fetch current user's peranan
$perananNama = "";
$sqlPeranan = "SELECT namaPeranan FROM peranan WHERE idPeranan = '$idPeranan'";
$resultPeranan = mysqli_query($conn, $sqlPeranan);
if ($row = mysqli_fetch_assoc($resultPeranan)) {
    $perananNama = $row['namaPeranan']; 
} else {
    $perananNama = "Unknown";
}

// Fetch current user's jabatan
$jabatanNama = "";
$sqlJabatan = "SELECT namaJabatan FROM jabatan WHERE idJabatan = '$idJabatan'";
$resultJabatan = mysqli_query($conn, $sqlJabatan);
if ($row = mysqli_fetch_assoc($resultJabatan)) {
    $jabatanNama = $row['namaJabatan'];
} else {
    $jabatanNama = "Unknown";
}

// SQL to fetch all users along with their jabatan and peranan
$sql = "SELECT 
            u.noIC,
            u.namaUser,
            u.emel,
            j.namaJabatan,
            p.namaPeranan
        FROM user u
        LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
        LEFT JOIN peranan p ON u.idPeranan = p.idPeranan
        ORDER BY u.namaUser ASC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>


<html>
    <head>
        <title>E-ICT Aduan</title>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-image: url("background.jpg");
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
                background: #0306a0ff;
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
                color: #ffffffff;
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
                background:transparent;
                overflow: auto;
            }

            .aduan-table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
            }

            .aduan-table th,
            .aduan-table td {
                border: 1px solid #000;
                padding: 10px;
                text-align: center;
            }

            .aduan-table th {
                background: #383ab4ff;
                font-weight: bold;
                color: white;
                font-size: 0.9rem;
            }

            .sidebar {
                width: 240px;
                background: #e6e6e6;
                border-right: none;
                flex-shrink: 0;

                display: flex;
                flex-direction: column;
            }

            .menu {
                flex: 1; /
            }

            .sidebar-logout {
                padding: 15px;
            }

            .logout-btn {
                width: 100%;
                padding: 8px 0;
                border: none;
                border-radius: 8px;
                background: #0306a0ff;
                color: #fff;
                font-weight: bold;
                cursor: pointer;
            }

            .filter-box {
                background: #e6e6e6;
                border: 2px solid #000;
                padding: 15px;
                margin-bottom: 30px;
            }

            .filter-row {
                display: flex;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
                margin-bottom: 1rem;
            }

            .filter-row label {
                font-weight: bold;
                font-size: 14px;
            }

            .filter-row input,
            .filter-row select {
                padding: 6px;
                width: 160px;
            }

            .filter-row button {
                padding: 4px 20px;
                border: none;
                background: #0306a0ff;
                color: #fff;
                font-weight: bold;
                border-radius: 6px;
                cursor: pointer;
            }
            
        </style>
    </head>
    <body>
        <div class="app">
            <div class="topbar">
                <div class="system">e-ICT Aduan</div>
                <div class="page-title">Senarai Pengguna</div>
            </div>

            <div class="layout">
                <div class="sidebar">
                    <div class="user-info">
                        <img src="profile.jpg" alt="User">
                        <div>
                            <strong><?= htmlspecialchars($namaUser); ?></strong><br>
                            <small><?= htmlspecialchars($perananNama); ?></small>
                        </div>
                    </div>

                    <div class="menu">
                        <a href="dashboard_admin.php">Dashboard</a>
                        <a href="Senarai_Aduan_admin.php">Senarai Aduan</a>
                        <a href="Senarai_Pengguna_admin.php" class="active">Senarai Pengguna</a>
                        <a href="Laporan_statistik_admin.php">Laporan Statistik</a>
                    </div>

                    <div class="sidebar-logout">
                        <form action="logout.php" method="post">
                            <button type="submit" onclick="return confirmLogout()" class="logout-btn">Log Keluar</button>

                        </form>
                    </div>
                </div>

                <div class="content">
                    <div class="filter-box">
                        <div class="filter-row">

                            <label>Status:</label>
                                <select>
                                    <option>Semua</option>
                                    <option>Baru</option>
                                    <option>Dalam Tindakan</option>
                                    <option>Selesai</option>
                                    <option>Hantar Kedai</option>
                                </select>
                            <input type="text" placeholder="Search..">
                            <button>Cari</button>
                    </div>

                    <table class="aduan-table">
                    <thead>
                        <tr>
                            <th>Bil</th>
                            <th>Nama Pengadu</th>
                            <th>Jabatan</th>
                            <th>Emel Pengadu</th>
                            <th>Peranan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $bil = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$bil++."</td>";
                                echo "<td>".htmlspecialchars($row['namaUser'])."</td>";
                                echo "<td>".htmlspecialchars($row['namaJabatan'])."</td>";
                                echo "<td>".htmlspecialchars($row['emel'])."</td>";
                                echo "<td>".htmlspecialchars($row['namaPeranan'])."</td>";
                                echo "<td><button class='lihat-btn' data-noic='".$row['noIC']."'>Lihat</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>Tiada aduan direkodkan</td></tr>";
                        }
                        ?>
                        </tbody>
                </div>
            </div>
        </div>

        <div id="editUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
            background: rgba(0,0,0,0.5); justify-content:center; align-items:center;">
            <div style="background:#fff; padding:20px; border-radius:8px; width:400px; position:relative;">
                <h3>Edit User</h3>
                <form id="editUserForm">
                    <label>No IC:</label>
                    <input type="text" name="noIC" id="modalNoIC"><br><br>
                    <label>Nama:</label>
                    <input type="text" name="namaUser" id="modalNamaUser"><br><br>
                    <label>Kata Laluan:</label>
                    <input type="text" name="kataLaluan" id="modalKatalaluan"><br><br>
                    <label>Jabatan:</label>
                    <select name="idJabatan" id="modalJabatan">
                        <?php
                        $jabatanRes = mysqli_query($conn, "SELECT * FROM jabatan");
                        while($jab = mysqli_fetch_assoc($jabatanRes)){
                            echo "<option value='".$jab['idJabatan']."'>".$jab['namaJabatan']."</option>";
                        }
                        ?>
                    </select><br><br>
                    <label>Emel:</label>
                    <input type="email" name="emel" id="modalEmel"><br><br>
                    <label>Peranan:</label>
                    <select name="idPeranan" id="modalPeranan">
                        <?php
                        $perananRes = mysqli_query($conn, "SELECT * FROM peranan");
                        while($per = mysqli_fetch_assoc($perananRes)){
                            echo "<option value='".$per['idPeranan']."'>".$per['namaPeranan']."</option>";
                        }
                        ?>
                    </select><br><br>
                    <button type="submit">Simpan</button>
                    <button type="button" onclick="closeModal()">Tutup</button>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#jenis_masalah').select2({
                    placeholder: "-- Pilih --",
                    allowClear: true
                });
            });

            function confirmLogout() {
                return confirm("Anda Pasti Untuk Log Keluar?");
            }

            function closeModal(){
                $('#editUserModal').hide();
            }

            $('.lihat-btn').on('click', function(){
                var noIC = $(this).data('noic');
            
                $.ajax({
                    url: 'fetch_user.php',
                    type: 'POST',
                    data: { noIC: noIC },
                    dataType: 'json',
                    success: function(data){
                        $('#modalNoIC').val(data.noIC);
                        $('#modalNamaUser').val(data.namaUser);
                        $('#modalEmel').val(data.emel);
                        $('#modalJabatan').val(data.idJabatan);
                        $('#modalPeranan').val(data.idPeranan);
                        $('#modalKatalaluan').val(data.kataLaluan);
                        $('#editUserModal').css('display', 'flex');
                    }
                });
            });

            $('#editUserForm').on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'update_user.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response){
                        alert(response);
                        location.reload();
                    }
                });
            });
        </script>

    </body>
</html>
