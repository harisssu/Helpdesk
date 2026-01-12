<?php
$aduanId = $_GET['id'] ?? 'Unknown'; // get the ID from URL
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>e-ICT Aduan - Detail Aduan</title>
    <style>
        /* BODY + APP */
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
            background-attachment: fixed;
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

        /* TOPBAR */
        .topbar {
            height: 50px;
            background: #58385eff;
            display: flex;
            align-items: center;
            padding: 0 20px;
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
            margin-left: 30px;
            font-size: 25px;
        }

        /* LAYOUT */
        .layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #e6e6e6;
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

        .sidebar-logout {
            padding: 15px;
            margin-top: auto;  /* This pushes it to the bottom */
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

        /* CONTENT AREA */
        .content {
            flex: 1;
            padding: 30px;
            background: transparent;
            overflow: auto;
        }

        /* DETAIL ADUAN BOX */
        .aduan-detail {
            width: 700px;
            background-color: #d9d9d9;
            padding: 20px;
            border: 1px solid #000;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto; /* center box */
        }

        .aduan-detail h2 {
            margin-top: 0;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-row label {
            width: 180px;
            font-weight: bold;
        }

        .form-row input[type="text"],
        .form-row select,
        .form-row input[type="date"],
        .form-row textarea {
            flex: 1;
            padding: 5px 8px;
            border: 1px solid #999;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-row textarea {
            height: 60px;
            resize: none;
        }

        .file-input-wrapper {
            display: flex;
            align-items: center;
        }

        .file-input-wrapper input[type="file"] {
            flex: 1;
        }

        .file-input-wrapper button {
            margin-left: 10px;
            padding: 5px 8px;
            background-color: #a94442;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn {
            margin-left: 180px;
            padding: 8px 15px;
            background-color: #fff;
            border: 1px solid #000;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }

    </style>
</head>
<body>
    <div class="app">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="system">e-ICT Aduan</div>
            <div class="page-title">Senarai Aduan</div>
        </div>

        <!-- MAIN LAYOUT -->
        <div class="layout">
            <!-- SIDEBAR -->
            <div class="sidebar">
                <div class="user-info">
                    <img src="profile.jpg" alt="User">
                    <div>
                        <strong>Fuyu</strong><br>
                        <small>Technician</small>
                    </div>
                </div>

                <?php $currentPage = 'detail'; ?>
                <div class="menu">
                    <a href="Senarai_Aduan_Technician.php" class="<?php if($currentPage=='list') echo 'active'; ?>">Senarai Aduan</a>
                </div>

                <div class="sidebar-logout">
                    <button class="logout-btn">Logout</button>
                </div>
            </div>

            <!-- CONTENT AREA -->
            <div class="content">
                <div class="aduan-detail">
                    <h2>DETAIL ADUAN (ID)</h2>
                    <form>
                        <div class="form-row">
                            <label for="jenisMasalah">Jenis Masalah :</label>
                            <input type="text" id="jenisMasalah" value="Komputer" readonly>
                        </div>

                        <div class="form-row">
                            <label for="statusTerkini">Status Terkini :</label>
                            <select id="statusTerkini">
                                <option value="Dalam Tindakan">Dalam Tindakan</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Hantar Kedai">Hantar Kedai</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <label for="tarikhMula">Tarikh & Masa Mula :</label>
                            <input type="date" id="tarikhMula">
                        </div>

                        <div class="form-row">
                            <label for="tarikhSiap">Tarikh & Masa Siap :</label>
                            <input type="text" id="tarikhSiap" readonly>    <!-- readonly = user cannot manually edit -->
                        </div>

                        <div class="form-row">
                            <label>Attachment Bukti Pembaiakan :</label>
                            <div class="file-input-wrapper">
                                <input type="file">
                                <button type="button">🗑️</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="notaTeknikal">Nota Tindakan Teknikal :</label>
                            <textarea id="notaTeknikal" placeholder="Masukkan nota..."></textarea>
                        </div>

                        <div class="form-row">
                            <button type="submit" class="submit-btn">Hantar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- auto set tarikh and masa when status 'selesai' -->
    <script>
    const form = document.querySelector("form");
    const statusTerkini = document.getElementById("statusTerkini");
    const tarikhSiap = document.getElementById("tarikhSiap");

    form.addEventListener("submit", function () {
        if (statusTerkini.value === "Selesai") {
            const now = new Date();

            const dateTime =
                now.getFullYear() + "-" +
                String(now.getMonth() + 1).padStart(2, "0") + "-" +
                String(now.getDate()).padStart(2, "0") + " " +
                String(now.getHours()).padStart(2, "0") + ":" +
                String(now.getMinutes()).padStart(2, "0");

            tarikhSiap.value = dateTime;
        }
    });
</script>
</body>
</html>
