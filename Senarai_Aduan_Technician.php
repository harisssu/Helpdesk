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

//filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate   = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$jabatanFilter = isset($_GET['jabatan']) ? intval($_GET['jabatan']) : 0;
$masalahFilter = isset($_GET['masalah']) ? $_GET['masalah'] : '';

// Ambil list jabatan untuk dropdown
$jabatanList = [];
$jabatanRes = mysqli_query($conn, "SELECT idJabatan, namaJabatan FROM jabatan ORDER BY namaJabatan ASC");
if ($jabatanRes) {
    while ($j = mysqli_fetch_assoc($jabatanRes)) {
        $jabatanList[] = $j;
    }
}

// Query to fetch the complaints assigned to the technician
// Construct SQL query with filters
$sql = "SELECT 
            a.idAduan, u.namaUser, j.namaJabatan, a.jenisMasalah, a.tarikhAduan,
            a.masaAduan, s.namaStatus, a.idStatus, t.namaTechnician
        FROM aduan a
        LEFT JOIN user u ON a.noIC = u.noIC
        LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
        LEFT JOIN status s ON a.idStatus = s.idStatus
        LEFT JOIN technician t ON a.noICTechnician = t.noICTechnician
        WHERE a.noICTechnician = ?";

// Initialize filterParams array
$filterParams = [];
$filterParams[] = $noICTechnician;  // Add technician's IC as the first parameter

// Add filters to SQL query
$filterConditions = [];
$filterParams = [$noICTechnician]; // Ensure technician IC is always part of the query

// Filter by status
if ($statusFilter !== '') {
    $filterConditions[] = "a.idStatus = ?";
    $filterParams[] = $statusFilter;
}

// Filter by date range
if ($fromDate !== '' && $toDate !== '') {
    $filterConditions[] = "DATE(a.tarikhAduan) BETWEEN ? AND ?";
    $filterParams[] = $fromDate;
    $filterParams[] = $toDate;
} elseif ($fromDate !== '') {
    $filterConditions[] = "DATE(a.tarikhAduan) >= ?";
    $filterParams[] = $fromDate;
} elseif ($toDate !== '') {
    $filterConditions[] = "DATE(a.tarikhAduan) <= ?";
    $filterParams[] = $toDate;
}

// Filter by jabatan
if ($jabatanFilter > 0) {
    $filterConditions[] = "(u.idJabatan = ? OR j.idJabatan = ?)";
    $filterParams[] = $jabatanFilter;
    $filterParams[] = $jabatanFilter;
}

// Filter by masalah
if ($masalahFilter !== '') {
    $filterConditions[] = "a.jenisMasalah LIKE ?";
    $filterParams[] = '%' . $masalahFilter . '%';
}

// Combine the WHERE clause and filters
if (count($filterConditions) > 0) {
    $sql .= " AND " . implode(" AND ", $filterConditions);
}

$sql .= " ORDER BY a.tarikhAduan DESC, a.masaAduan DESC";

// Prepare and execute the SQL statement
$stmt = $conn->prepare($sql);

// Dynamically determine the parameter types (e.g., 's' for string, 'i' for integer)
$paramTypes = str_repeat('s', count($filterParams));  // Assume all parameters are strings ('s') for now

$stmt->bind_param($paramTypes, ...$filterParams);  // Bind all parameters dynamically
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
                <div class="filter-box">
                    <div class="filter-row">
                        <form method="GET" action="">
                            <label>Jabatan:</label>
                            <select name="jabatan" style="width: 10rem; padding: 0.2rem;">
                                <option value="">Semua</option>
                                <?php foreach ($jabatanList as $j): ?>
                                    <option value="<?= (int)$j['idJabatan']; ?>"
                                        <?= ($jabatanFilter == (int)$j['idJabatan']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['namaJabatan']); ?>
                                    </option>
                                <?php endforeach; ?>                                </select>

                            <label>From:</label>
                            <input type="date" name="from_date" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">

                            <label>To:</label>
                            <input type="date" name="to_date" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">

                            <label>Status:</label>
                            <select name="status">
                            <option value="">Semua</option>
                                <option value="2" <?= (isset($_GET['status']) && $_GET['status']=='2')?'selected':'' ?>>Dalam Tindakan</option>
                                <option value="3" <?= (isset($_GET['status']) && $_GET['status']=='3')?'selected':'' ?>>Selesai</option>
                                <option value="4" <?= (isset($_GET['status']) && $_GET['status']=='4')?'selected':'' ?>>Hantar Kedai</option>
                            </select>

                            <label>Jenis Masalah:</label>
                            <select name="masalah" style="width: 10rem; padding: 0.2rem;">
                                <option value="">Semua</option>
                                <!-- Define predefined masalah options -->
                                <option value="Komputer" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Komputer')?'selected':'' ?>>Komputer</option>
                                <option value="Printer" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Printer')?'selected':'' ?>>Printer</option>
                                <option value="Software" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Software')?'selected':'' ?>>Software</option>
                                <option value="Internet" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Internet')?'selected':'' ?>>Internet</option>
                                <option value="Monitor" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Monitor')?'selected':'' ?>>Monitor</option>
                                <option value="Lain-lain" <?= (isset($_GET['masalah']) && $_GET['masalah']=='Lain-lain')?'selected':'' ?>>Lain-lain</option>
                            </select>

                            <button type="submit">Cari</button>
                        </form>
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
