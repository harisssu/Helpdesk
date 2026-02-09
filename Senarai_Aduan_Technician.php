<?php
session_start();
include "db_connect.php";

// Ensure the user is logged in and has the correct role (technician)
if (!isset($_SESSION['noIC']) || $_SESSION['role'] !== 'technician') {
    header("Location: login.html");
    exit;
}

// Get session variables
$noICTechnician = $_SESSION['noIC'];  // This is the technician's IC number (unique for each technician)
$namaUser       = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Unknown';
$idJabatan      = isset($_SESSION['idJabatan']) ? $_SESSION['idJabatan'] : '';
$perananNama    = "Technician";

// Query to fetch the complaints assigned to the technician
$sql = "SELECT 
            a.idAduan,
            u.namaUser,
            j.namaJabatan,
            a.jenisMasalah,
            a.tarikhAduan,
            a.masaAduan,
            s.namaStatus,
            a.idStatus,
            t.namaTechnician
        FROM aduan a
        LEFT JOIN user u ON a.noIC = u.noIC
        LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
        LEFT JOIN status s ON a.idStatus = s.idStatus
        LEFT JOIN technician t ON a.noICTechnician = t.noICTechnician
        WHERE a.noICTechnician = ?  -- Only get complaints assigned to the logged-in technician
        ORDER BY a.tarikhAduan DESC, a.masaAduan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $noICTechnician);  // Bind the technician's IC number to the query
$stmt->execute();
$result = $stmt->get_result();  // Get the result of the query

// Check if complaints are found
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<html>
<head>
    <title>e-ICT Aduan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;           /*set the font style of all text*/
            margin: 0;                                  /*make bg touch edges of screen*/
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("background.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;               /*bg stays still while scrolling*/
            position: relative;
            overflow: hidden;                           /*hide anythings outside the screen, prevent unwanted scrollbars*/
            }
            
            .app {
            width: 100%;                /*make element take 100% of screen width*/
            height: 100vh;              /*make element as tall as screen height (viewpoint height)*/
            display: flex;              /*allows easy layout control*/
            flex-direction: column;     /*arrange items top to bottom*/
            background: transparent;    /*bg is see-through*/
            }

            .topbar {
            height: 50px;
            background: #58385eff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: none;
            flex-shrink: 0;
            }

            .topbar .system {       /*e-ICT Aduan*/
            font-weight: bold;
            color: #fff;
            margin-right: 35px;
            font-size: 30px;
            }

            .topbar .page-title {   /*Senarai Aduan*/
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

            .aduan-table th,            /*table header cell. titles at the top of table. comma (,) means apply the same style to both th td*/
            .aduan-table td {           /*table data cell. normal cells inside table rows*/
            border: 1px solid #000;   /*black border around evey cell*/
            padding: 10px;              /*spcae inside each cell*/
            text-align: center;         /*text is centered*/
            }

            .aduan-table th {
            background: #e6e6e6;
            font-weight: bold;
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
            background: #58385eff;
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
            <div class="page-title">Senarai Aduan</div>
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
                    <a href="Senarai_Aduan_Technician.php" class="active">Senarai Aduan</a>
                </div>

                <div class="sidebar-logout">
                    <form action="logout.php" method="post">
                        <button type="submit" onclick="return confirmLogout()" class="logout-btn">Log Keluar</button>
                    </form>
                </div>
            </div>

            <div class="content">
                <!-- Filter section -->
             <div style="margin-botto,:15px;">
                Status:
                <select id="statusFilter" onchange="filterTable()">     <!--Status option-->
                    <option value="">All</option>
                    <option value="1">Dalam Tindakan</option>
                    <option value="2">Selesai</option>
                    <option value="3">Hantar Kedai</option>
                </select>

                Jabatan:
                <select id="unitFilter" onchange="filterTable()">
                    <option value="">All</option>               <!--Add more option later-->
                    <option value="Bedah Mulut">Bedah Mulut</option>
                    <option value="Bilik PA Pengarah">Bilik PA Pengarah</option>
                    <option value="CSSU">CSSU</option>
                    <option value="Daycare">Daycare</option>
                    <option value="Dewan Bedah">Dewan Bedah</option>
                    <option value="Dewan Bersalin">Dewan Bersalin</option>
                    <option value="ENT">ENT</option>
                    <option value="Farmasi Bekalan Wad">Farmasi Bekalan Wad</option>
                    <option value="Farmasi DICE">Farmasi DICE</option>
                    <option value="Farmasi Klinik Pakar">Farmasi Klinik Pakar</option>
                    <option value="Farmasi Logistik">Farmasi Logistik</option>
                    <option value="Farmasi Pengeluaran">Farmasi Pengeluaran</option>
                    <option value="Farmasi Wad">Farmasi Wad</option>
                    <option value="Forensik">Forensik</option>
                    <option value="Hemodialisis">Hemodialisis</option>
                    <option value="ICU">ICU</option>
                    <option value="Jabatan Dietetik dan Sajian">Jabatan Dietetik dan Sajian</option>
                    <option value="Jabatan Pergigian Pediatrik">Jabatan Pergigian Pediatrik</option>
                    <option value="Kawalan Infeksi">Kawalan Infeksi</option>
                    <option value="Kecemasan">Kecemasan</option>
                    <option value="Klinik Pakar Obstetrik">Klinik Pakar Obstetrik</option>
                    <option value="Klinik Pakar Ortopedik">Klinik Pakar Ortopedik</option>
                    <option value="Klinik Pakar Pediatrik">Klinik Pakar Pediatrik</option>
                    <option value="Klinik Pakar Psikiatri">Klinik Pakar Psikiatri</option>
                    <option value="Methadone">Methadone</option>
                    <option value="MOPD">MOPD</option>
                    <option value="Oftalmologi">Oftalmologi</option>
                    <option value="Pejabat Pengarah">Pejabat Pengarah</option>
                    <option value="Penyeliaan Kejururawatan">Penyeliaan Kejururawatan</option>
                    <option value="Perpustakaan">Perpustakaan</option>
                    <option value="Porter">Porter</option>
                    <option value="SCN/NICU">SCN/NICU</option>
                    <option value="SOPD">SOPD</option>
                    <option value="Unit Fisioterapi">Unit Fisioterapi</option>
                    <option value="Unit Hal Ehwal Islam">Unit Hal Ehwal Islam</option>
                    <option value="Unit Teknologi Maklumat">Unit Teknologi Maklumat</option>
                    <option value="Unit Kejuruteraan">Unit Kejuruteraan</option>
                    <option value="Unit Kerja Sosial">Unit Kerja Sosial</option>
                    <option value="Unit Keselamatan">Unit Keselamatan</option>
                    <option value="Unit Keselamatan dan Kesihatan">Unit Keselamatan dan Kesihatan</option>
                    <option value="Unit Kewangan dan Hasil">Unit Kewangan dan Hasil</option>
                    <option value="Unit Khidmat Pengurusan">Unit Khidmat Pengurusan</option>
                    <option value="Unit Kualiti">Unit Kualiti</option>
                    <option value="Unit Patologi dan Tabung Darah">Unit Patologi dan Tabung Darah</option>
                    <option value="Unit Pembangunan dan Perumahan">Unit Pembangunan dan Perumahan</option>
                    <option value="Unit Pemulihan Carakerja">Unit Pemulihan Carakerja</option>
                    <option value="Unit Pendidikan Kesihatan">Unit Pendidikan Kesihatan</option>
                    <option value="Unit Pengurusan Aset dan Stor">Unit Pengurusan Aset dan Stor</option>
                    <option value="Unit Perhubungan Awam">Unit Perhubungan Awam</option>
                    <option value="Unit Psikologi">Unit Psikologi</option>
                    <option value="Unit Radiologi">Unit Radiologi</option>
                    <option value="Unit Rekod Perubatan">Unit Rekod Perubatan</option>
                    <option value="Unit Sumber Manusia">Unit Sumber Manusia</option>
                    <option value="Wad 10">Wad 10</option>
                    <option value="Wad 2">Wad 2</option>
                    <option value="Wad 3">Wad 3</option>
                    <option value="Wad 4">Wad 4</option>
                    <option value="Wad 5">Wad 5</option>
                    <option value="Wad 6">Wad 6</option>
                    <option value="Wad 8">Wad 8</option>
                    <option value="Wad 9">Wad 9</option>
                </select>

                Tarikh:
                <input type="date" id="dateFilter" onchange="filterTable()">

                Cari:
                <input type="text" id="searchInput" placeholder="Cari..." onkeyup="filterTable()">
            </div>
        <!-- End of filter section -->

                <table class="aduan-table">
                    <thead>
                        <tr>
                            <th>Bil</th>
                            <th>Id Aduan</th>
                            <th>Nama Pengadu</th>
                            <th>Jenis Masalah</th>
                            <th>Unit</th>
                            <th>Tarikh dan Masa Aduan</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bil = 1;
                        // Loop through the results and display the complaints in the table
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusText = $row['namaStatus']; // Get the readable status
                                echo "<tr>
                                    <td>{$bil}</td>
                                    <td>{$row['idAduan']}</td>
                                    <td>{$row['namaUser']}</td>
                                    <td>{$row['jenisMasalah']}</td>
                                    <td>{$row['namaJabatan']}</td>
                                    <td>{$row['tarikhAduan']}</td>
                                    <td>{$statusText}</td>
                                    <td>
                                        <a href='Lihat_Technician.php?idAduan={$row['idAduan']}'>
                                            <button>Lihat</button>
                                        </a>
                                    </td>
                                </tr>";
                                $bil++;
                            }
                        } else {
                            echo "<tr><td colspan='8'>No complaints found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm("Anda Pasti Untuk Log Keluar?");
        }
    </script>
</body>
</html>
.
.
