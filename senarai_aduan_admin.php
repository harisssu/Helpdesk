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
                <div class="page-title">Senarai Aduan</div>
            </div>

            <div class="layout">
                <div class="sidebar">
                    <div class="user-info">
                        <img src="profile.jpg" alt="User">
                        <div>
                            <strong>.....</strong><br>
                            <small>Admin</small>
                        </div>
                    </div>

                    <div class="menu">
                        <a href="dashboard_admin.php">Dashboard</a>
                        <a href="Senarai_Aduan_admin.php" class="active">Senarai Aduan</a>
                        <a href="Senarai_Pengguna_admin.php">Senarai Pengguna</a>
                        <a href="Laporan_statistik_admin.php">Laporan Statistik</a>
                    </div>

                    <div class="sidebar-logout">
                <button class="logout-btn">Logout</button>
            </div>
                </div>

                <div class="content">
                    <div class="filter-box">
                        <div class="filter-row">
                            <label>From:</label>
                            <input type="date">

                            <label>To:</label>
                            <input type="date">

                            <label>Jabatan:</label>
                                <select name="jabatan" id="jabatan" style="width: 10rem; padding: 0.2rem;">
                                    <option value="">-- Pilih --</option>
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
                                    <option value="Unit IT">Unit IT</option>
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

                            <label>Status:</label>
                                <select>
                                    <option>Semua</option>
                                    <option>Baru</option>
                                    <option>Dalam Tindakan</option>
                                    <option>Selesai</option>
                                    <option>Hantar Kedai</option>
                                </select>
                            <button>Cari</button>
                    </div>
                    <table class="aduan-table">
                    <thead>
                        <tr>
                            <th>Bil</th>
                            <th>Id Aduan</th>
                            <th>Nama Pengadu</th>
                            <th>Jenis Masalah</th>
                            <th>Unit</th>
                            <th>Keutamaan</th>
                            <th>Nama Technician</th>
                            <th>Tarikh Aduan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </div>
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
        </script>

    </body>
</html>