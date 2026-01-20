<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['noIC']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$namaUser   = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Unknown';
$idJabatan  = isset($_SESSION['idJabatan']) ? $_SESSION['idJabatan'] : '';
$perananNama = "Admin";

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate   = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$jabatanFilter = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';

$sql = "SELECT 
            a.idAduan,

            /* Nama pengadu: user atau ketuaunit */
            COALESCE(u.namaUser, k.namaKetua, '-') AS namaPengadu,

            /* Unit/Jabatan ikut siapa yang wujud */
            COALESCE(ju.namaJabatan, jk.namaJabatan, '-') AS namaJabatan,

            a.jenisMasalah,
            a.tarikhAduan,
            s.namaStatus,
            a.idStatus,

            COALESCE(t.namaTechnician, '-') AS namaTechnician

        FROM aduan a
        LEFT JOIN user u ON a.noIC = u.noIC
        LEFT JOIN jabatan ju ON u.idJabatan = ju.idJabatan

        LEFT JOIN ketuaunit k ON a.noICKetua = k.noICKetua
        LEFT JOIN jabatan jk ON k.idJabatan = jk.idJabatan

        LEFT JOIN status s ON a.idStatus = s.idStatus
        LEFT JOIN technician t ON a.noICTechnician = t.noICTechnician
        WHERE 1=1";


if ($statusFilter !== '') {
    $sql .= " AND a.idStatus = '".intval($statusFilter)."'";
}

if ($fromDate !== '' && $toDate !== '') {
    $sql .= " AND DATE(a.tarikhAduan) BETWEEN '$fromDate' AND '$toDate'";
} elseif ($fromDate !== '') {
    $sql .= " AND DATE(a.tarikhAduan) >= '$fromDate'";
} elseif ($toDate !== '') {
    $sql .= " AND DATE(a.tarikhAduan) <= '$toDate'";
}   

if ($jabatanFilter !== '') {
    $jabatanFilterEsc = mysqli_real_escape_string($conn, $jabatanFilter);
    $sql .= " AND (ju.namaJabatan = '$jabatanFilterEsc' OR jk.namaJabatan = '$jabatanFilterEsc')";
}


$sql .= " ORDER BY a.tarikhAduan DESC, a.masaAduan DESC";


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
                background-image: url("WhatsApp Image 2026-01-15 at 14.49.03.jpeg");
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

           .sidebar{
            width:240px;
            background:#e6e6e6;
            flex-shrink:0;
            display:flex;
            flex-direction:column;
}

            .menu{ flex:1; }

            .sidebar-logout{
            padding:15px;
            margin-top:auto;
            }
    

            .logout-btn {
                width: 100%;
                padding: 8px;
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
                        <a href="dashboard_admin.php" >Dashboard</a>
                        <a href="Senarai_aduan_admin.php" class="active">Senarai Aduan</a>
                        <a href="Senarai_Pengguna_admin.php">Senarai Pengguna</a>
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
                                <form method="GET" action="">
                                    <label>Jabatan:</label>
                                        <select name="jabatan" style="width: 10rem; padding: 0.2rem;">
                                            <option value="">Semua</option>
                                            <option value="Bedah Mulut" <?= ($jabatanFilter=='Bedah Mulut')?'selected':'' ?>>Bedah Mulut</option>
                                            <option value="Bilik PA Pengarah" <?= ($jabatanFilter=='Bilik PA Pengarah')?'selected':'' ?>>Bilik PA Pengarah</option>
                                            <option value="CSSU" <?= ($jabatanFilter=='CSSU')?'selected':'' ?>>CSSU</option>
                                            <option value="Daycare" <?= ($jabatanFilter=='Daycare')?'selected':'' ?>>Daycare</option>
                                            <option value="Dewan Bedah" <?= ($jabatanFilter=='Dewan Bedah')?'selected':'' ?>>Dewan Bedah</option>
                                            <option value="Dewan Bersalin" <?= ($jabatanFilter=='Dewan Bersalin')?'selected':'' ?>>Dewan Bersalin</option>
                                            <option value="ENT" <?= ($jabatanFilter=='ENT')?'selected':'' ?>>ENT</option>
                                            <option value="Farmasi Bekalan Wad" <?= ($jabatanFilter=='Farmasi Bekalan Wad')?'selected':'' ?>>Farmasi Bekalan Wad</option>
                                            <option value="Farmasi DICE" <?= ($jabatanFilter=='Farmasi DICE')?'selected':'' ?>>Farmasi DICE</option>
                                            <option value="Farmasi Klinik Pakar" <?= ($jabatanFilter=='Farmasi Klinik Pakar')?'selected':'' ?>>Farmasi Klinik Pakar</option>
                                            <option value="Farmasi Logistik" <?= ($jabatanFilter=='Farmasi Logistik')?'selected':'' ?>>Farmasi Logistik</option>
                                            <option value="Farmasi Pengeluaran" <?= ($jabatanFilter=='Farmasi Pengeluaran')?'selected':'' ?>>Farmasi Pengeluaran</option>
                                            <option value="Farmasi Wad" <?= ($jabatanFilter=='Farmasi Wad')?'selected':'' ?>>Farmasi Wad</option>
                                            <option value="Forensik" <?= ($jabatanFilter=='Forensik')?'selected':'' ?>>Forensik</option>
                                            <option value="Hemodialisis" <?= ($jabatanFilter=='Hemodialisis')?'selected':'' ?>>Hemodialisis</option>
                                            <option value="ICU" <?= ($jabatanFilter=='ICU')?'selected':'' ?>>ICU</option>
                                            <option value="Jabatan Dietetik dan Sajian" <?= ($jabatanFilter=='Jabatan Dietetik dan Sajian')?'selected':'' ?>>Jabatan Dietetik dan Sajian</option>
                                            <option value="Jabatan Pergigian Pediatrik" <?= ($jabatanFilter=='Jabatan Pergigian Pediatrik')?'selected':'' ?>>Jabatan Pergigian Pediatrik</option>
                                            <option value="Kawalan Infeksi" <?= ($jabatanFilter=='Kawalan Infeksi')?'selected':'' ?>>Kawalan Infeksi</option>
                                            <option value="Kecemasan" <?= ($jabatanFilter=='Kecemasan')?'selected':'' ?>>Kecemasan</option>
                                            <option value="Klinik Pakar Obstetrik" <?= ($jabatanFilter=='Klinik Pakar Obstetrik')?'selected':'' ?>>Klinik Pakar Obstetrik</option>
                                            <option value="Klinik Pakar Ortopedik" <?= ($jabatanFilter=='Klinik Pakar Ortopedik')?'selected':'' ?>>Klinik Pakar Ortopedik</option>
                                            <option value="Klinik Pakar Pediatrik" <?= ($jabatanFilter=='Klinik Pakar Pediatrik')?'selected':'' ?>>Klinik Pakar Pediatrik</option>
                                            <option value="Klinik Pakar Psikiatri" <?= ($jabatanFilter=='Klinik Pakar Psikiatri')?'selected':'' ?>>Klinik Pakar Psikiatri</option>
                                            <option value="Methadone" <?= ($jabatanFilter=='Methadone')?'selected':'' ?>>Methadone</option>
                                            <option value="MOPD" <?= ($jabatanFilter=='MOPD')?'selected':'' ?>>MOPD</option>
                                            <option value="Oftalmologi" <?= ($jabatanFilter=='Oftalmologi')?'selected':'' ?>>Oftalmologi</option>
                                            <option value="Pejabat Pengarah" <?= ($jabatanFilter=='Pejabat Pengarah')?'selected':'' ?>>Pejabat Pengarah</option>
                                            <option value="Penyeliaan Kejururawatan" <?= ($jabatanFilter=='Penyeliaan Kejururawatan')?'selected':'' ?>>Penyeliaan Kejururawatan</option>
                                            <option value="Perpustakaan" <?= ($jabatanFilter=='Perpustakaan')?'selected':'' ?>>Perpustakaan</option>
                                            <option value="Porter" <?= ($jabatanFilter=='Porter')?'selected':'' ?>>Porter</option>
                                            <option value="SCN/NICU" <?= ($jabatanFilter=='SCN/NICU')?'selected':'' ?>>SCN/NICU</option>
                                            <option value="SOPD" <?= ($jabatanFilter=='SOPD')?'selected':'' ?>>SOPD</option>
                                            <option value="Unit Fisioterapi" <?= ($jabatanFilter=='Unit Fisioterapi')?'selected':'' ?>>Unit Fisioterapi</option>
                                            <option value="Unit Hal Ehwal Islam" <?= ($jabatanFilter=='Unit Hal Ehwal Islam')?'selected':'' ?>>Unit Hal Ehwal Islam</option>
                                            <option value="Unit IT" <?= ($jabatanFilter=='Unit IT')?'selected':'' ?>>Unit IT</option>
                                            <option value="Unit Kejuruteraan" <?= ($jabatanFilter=='Unit Kejuruteraan')?'selected':'' ?>>Unit Kejuruteraan</option>
                                            <option value="Unit Kerja Sosial" <?= ($jabatanFilter=='Unit Kerja Sosial')?'selected':'' ?>>Unit Kerja Sosial</option>
                                            <option value="Unit Keselamatan" <?= ($jabatanFilter=='Unit Keselamatan')?'selected':'' ?>>Unit Keselamatan</option>
                                            <option value="Unit Keselamatan dan Kesihatan" <?= ($jabatanFilter=='Unit Keselamatan dan Kesihatan')?'selected':'' ?>>Unit Keselamatan dan Kesihatan</option>
                                            <option value="Unit Kewangan dan Hasil" <?= ($jabatanFilter=='Unit Kewangan dan Hasil')?'selected':'' ?>>Unit Kewangan dan Hasil</option>
                                            <option value="Unit Khidmat Pengurusan" <?= ($jabatanFilter=='Unit Khidmat Pengurusan')?'selected':'' ?>>Unit Khidmat Pengurusan</option>
                                            <option value="Unit Kualiti" <?= ($jabatanFilter=='Unit Kualiti')?'selected':'' ?>>Unit Kualiti</option>
                                            <option value="Unit Patologi dan Tabung Darah" <?= ($jabatanFilter=='Unit Patologi dan Tabung Darah')?'selected':'' ?>>Unit Patologi dan Tabung Darah</option>
                                            <option value="Unit Pembangunan dan Perumahan" <?= ($jabatanFilter=='Unit Pembangunan dan Perumahan')?'selected':'' ?>>Unit Pembangunan dan Perumahan</option>
                                            <option value="Unit Pemulihan Carakerja" <?= ($jabatanFilter=='Unit Pemulihan Carakerja')?'selected':'' ?>>Unit Pemulihan Carakerja</option>
                                            <option value="Unit Pendidikan Kesihatan" <?= ($jabatanFilter=='Unit Pendidikan Kesihatan')?'selected':'' ?>>Unit Pendidikan Kesihatan</option>
                                            <option value="Unit Pengurusan Aset dan Stor" <?= ($jabatanFilter=='Unit Pengurusan Aset dan Stor')?'selected':'' ?>>Unit Pengurusan Aset dan Stor</option>
                                            <option value="Unit Perhubungan Awam" <?= ($jabatanFilter=='Unit Perhubungan Awam')?'selected':'' ?>>Unit Perhubungan Awam</option>
                                            <option value="Unit Psikologi" <?= ($jabatanFilter=='Unit Psikologi')?'selected':'' ?>>Unit Psikologi</option>
                                            <option value="Unit Radiologi" <?= ($jabatanFilter=='Unit Radiologi')?'selected':'' ?>>Unit Radiologi</option>
                                            <option value="Unit Rekod Perubatan" <?= ($jabatanFilter=='Unit Rekod Perubatan')?'selected':'' ?>>Unit Rekod Perubatan</option>
                                            <option value="Unit Sumber Manusia" <?= ($jabatanFilter=='Unit Sumber Manusia')?'selected':'' ?>>Unit Sumber Manusia</option>
                                            <option value="Wad 10" <?= ($jabatanFilter=='Wad 10')?'selected':'' ?>>Wad 10</option>
                                            <option value="Wad 2" <?= ($jabatanFilter=='Wad 2')?'selected':'' ?>>Wad 2</option>
                                            <option value="Wad 3" <?= ($jabatanFilter=='Wad 3')?'selected':'' ?>>Wad 3</option>
                                            <option value="Wad 4" <?= ($jabatanFilter=='Wad 4')?'selected':'' ?>>Wad 4</option>
                                            <option value="Wad 5" <?= ($jabatanFilter=='Wad 5')?'selected':'' ?>>Wad 5</option>
                                            <option value="Wad 6" <?= ($jabatanFilter=='Wad 6')?'selected':'' ?>>Wad 6</option>
                                            <option value="Wad 8" <?= ($jabatanFilter=='Wad 8')?'selected':'' ?>>Wad 8</option>
                                            <option value="Wad 9" <?= ($jabatanFilter=='Wad 9')?'selected':'' ?>>Wad 9</option>
                                        </select>

                                    <label>From:</label>
                                    <input type="date" name="from_date" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">

                                    <label>To:</label>
                                    <input type="date" name="to_date" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">

                                    <label>Status:</label>
                                    <select name="status">
                                        <option value="">Semua</option>
                                        <option value="1" <?= (isset($_GET['status']) && $_GET['status']=='1')?'selected':'' ?>>Baru</option>
                                        <option value="2" <?= (isset($_GET['status']) && $_GET['status']=='2')?'selected':'' ?>>Dalam Tindakan</option>
                                        <option value="3" <?= (isset($_GET['status']) && $_GET['status']=='3')?'selected':'' ?>>Selesai</option>
                                        <option value="4" <?= (isset($_GET['status']) && $_GET['status']=='4')?'selected':'' ?>>Hantar Kedai</option>
                                    </select>

                                    <button type="submit">Cari</button>
                                </form>
                            </div>
                                <table class="aduan-table">
                                    <thead>
                                        <tr>
                                            <th>Bil</th>
                                            <th>Id Aduan</th>
                                            <th>Nama Pengadu</th>
                                            <th>Jenis Masalah</th>
                                            <th>Unit</th>
                                            <th>Nama Technician</th>
                                            <th>Tarikh Aduan</th>
                                            <th>Status</th>
                                            <th>Tindakan</th>
                                        </tr>
                                    </thead> 
                                <tbody>
                                <?php
                                $bil = 1;

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {

                                        switch ($row['idStatus']) {
                                            case 1: $status = "Baru"; break;
                                            case 2: $status = "Dalam Tindakan"; break;
                                            case 3: $status = "Selesai"; break;
                                            case 4: $status = "Hantar Kedai"; break;
                                            default: $status = "Tidak Diketahui";
                                        }

                                        echo "<tr>
                                                <td>{$bil}</td>
                                                <td>{$row['idAduan']}</td>
                                                <td>".htmlspecialchars($row['namaPengadu'])."</td>
                                                <td>{$row['jenisMasalah']}</td>
                                                <td>".htmlspecialchars($row['namaJabatan'])."</td>
                                                <td>{$row['namaTechnician']}</td>
                                                <td>{$row['tarikhAduan']}</td>
                                                <td>{$status}</td>
                                                <td>
                                                    <button 
                                                        class='lihat-btn'
                                                        data-id='{$row['idAduan']}'
                                                        style='
                                                            padding:4px 10px;
                                                            background:#0306a0ff;
                                                            color:white;
                                                            border:none;
                                                            border-radius:5px;
                                                            cursor:pointer;
                                                        '>
                                                        Lihat
                                                    </button>
                                                </td>
                                            </tr>";


                                        $bil++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>Tiada rekod aduan</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                    </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>

            let currentAduanId = null;

            $(document).ready(function () {
                $('#jenis_masalah').select2({
                    placeholder: "-- Pilih --",
                    allowClear: true
                });
            });
            
            function confirmLogout() {
                return confirm("Anda Pasti Untuk Log Keluar?");
            }

            $(document).on('click', '.lihat-btn', function () {
                const idAduan = $(this).data('id');

                $.ajax({
                    url: 'get_aduan.php',
                    type: 'POST',
                    data: { idAduan: idAduan },
                    dataType: 'json',
                    success: function (data) {
                        currentAduanId = data.idAduan;

                        $('#m_idAduan').text(data.idAduan);
                        $('#m_jenisMasalah').text(data.jenisMasalah);
                        $('#m_tarikhAduan').text(data.tarikhAduan);
                        $('#m_masaAduan').text(data.masaAduan);
                        $('#m_namaUser').text(data.namaPengadu);
                        $('#m_noIC').text(data.noIC ? data.noIC : data.noICKetua);
                        $('#m_lokasi').text(data.lokasi);
                        $('#m_peneranganMasalah').text(data.peneranganMasalah);
                        $('#m_idStatus').text(data.idStatus);
                        $.getJSON('get_technician.php', function (techs) {

                            const select = $('#m_noICTechnician');
                            select.empty();
                            select.append('<option value="">-- Pilih Technician --</option>');

                            techs.forEach(function (tech) {
                                const selected = tech.noICTechnician === data.noICTechnician ? 'selected' : '';
                                select.append(
                                    `<option value="${tech.noICTechnician}" ${selected}>
                                        ${tech.namaTechnician}
                                    </option>`
                                );
                            });
                        });

                        if (data.attachment && data.attachment.trim() !== "") {
                            const url = data.attachment.startsWith("uploads/")
                                ? data.attachment
                                : "uploads/" + data.attachment;

                            $('#m_attachment').attr('href', url).show();
                            } else {
                            $('#m_attachment').hide();
                            }

                        $('#aduanModal').css('display', 'flex');
                    },
                    error: function () {
                        alert('Gagal ambil data aduan');
                    }
                });
            });


            function closeModal() {
                $('#aduanModal').hide();
            }

            $(document).on('click', '#btnAssignTech', function (e) {
    e.preventDefault();

    if (!currentAduanId) {
        alert('ID Aduan tidak dijumpai');
        return;
    }

    const techIC = $('#m_noICTechnician').val();

    if (!techIC) {
        alert('Sila pilih technician');
        return;
    }

    $.ajax({
        url: 'tetapkan_technician.php',
        type: 'POST',
        dataType: 'json',
        data: {
            idAduan: currentAduanId,
            noICTechnician: techIC
        },
        success: function (res) {
            console.log(res);

            if (res.success) {
                alert('Technician berjaya ditetapkan');
                location.reload();
            } else {
                alert(res.msg || 'Gagal simpan');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('AJAX error');
        }
    });
});


        </script>

        <div id="aduanModal" style="
            display:none;
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            background:rgba(0,0,0,0.5);
            justify-content:center;
            align-items:center;
            z-index:9999;
        ">
            <div style="
                background:#fff;
                padding:20px;
                width:600px;
                max-height:90vh;
                overflow:auto;
                border-radius:8px;
                position:relative;
            ">
                <h3>Maklumat Aduan</h3>

                <table style="width:100%; border-collapse:collapse;">
                    <tr><td><b>ID Aduan</b></td><td id="m_idAduan"></td></tr>
                    <tr><td><b>Jenis Masalah</b></td><td id="m_jenisMasalah"></td></tr>
                    <tr><td><b>Tarikh Aduan</b></td><td id="m_tarikhAduan"></td></tr>
                    <tr><td><b>Masa Aduan</b></td><td id="m_masaAduan"></td></tr>
                    <tr>
                        <td><b>Nama Pengadu</b></td>
                        <td id="m_namaUser"></td>
                    </tr>
                    <tr>
                        <td><b>No IC</b></td>
                        <td id="m_noIC"></td>
                    </tr>
                    <tr><td><b>Lokasi</b></td><td id="m_lokasi"></td></tr>
                    <tr><td><b>Penerangan Aduan</b></td><td id="m_peneranganMasalah"></td></tr>
                    <tr><td><b>Status</b></td><td id="m_idStatus"></td></tr>
                    <tr>
                        <td><b>Tetapkan Technician</b></td>
                        <td>
                            <select id="m_noICTechnician" style="width:100%; padding:6px;">
                                <option value="">-- Pilih Technician --</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td><b>Attachment Aduan</b></td>
                        <td><a id="m_attachment" target="_blank">Lihat</a></td>
                    </tr>
                </table>

                <br>
                <button onclick="closeModal()" style="
                    padding:6px 20px;
                    background:#999;
                    border:none;
                    color:white;
                    border-radius:5px;
                    cursor:pointer;
                ">Tutup</button>

                <button id="btnAssignTech" style="
                    padding:6px 20px;
                    background:#0306a0ff;
                    border:none;
                    color:white;
                    border-radius:5px;
                    cursor:pointer;
                ">
                    Simpan
                </button>

            </div>
        </div>
    </body>
</html>
