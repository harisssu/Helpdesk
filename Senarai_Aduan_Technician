<!DOCTYPE html>
<html>
<head>
    <title>e-ICT Aduan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            background-image: url("background.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .app {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 50px;
            background: #58385e;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .system {
            color: white;
            font-size: 30px;
            font-weight: bold;
            margin-right: 40px;
        }

        .page-title {
            font-size: 25px;
            font-weight: bold;
        }

        .layout {
            display: flex;
            flex: 1;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #e6e6e6;
            display: flex;
            flex-direction: column;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-bottom: 1px solid black;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .menu a {
            display: block;
            padding: 15px;
            border-bottom: 1px solid black;
            text-decoration: none;
            color: black;
        }

        .menu a.active {
            color: blue;
            font-weight: bold;
        }

        .sidebar-logout {
            margin-top: auto;
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

        .content {
            flex: 1;
            padding: 30px;
            overflow: auto;
        }

        /* FILTER STYLE */
        select, input {
            padding: 5px;
            margin-right: 8px;
        }

        .aduan-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 15px;
        }

        .aduan-table th,
        .aduan-table td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        .aduan-table th {
            background: #e6e6e6;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #555;
            font-style: italic;
        }
    </style>
</head>

<body>
<div class="app">

    <!-- TOP BAR -->
    <div class="topbar">
        <div class="system">e-ICT Aduan</div>
        <div class="page-title">Senarai Aduan</div>
    </div>

    <div class="layout">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="user-info">
                <img src="profile.jpg">
                <div>
                    <strong>Fuyu</strong><br>
                    <small>Technician</small>
                </div>
            </div>

            <div class="menu">
                <a href="#" class="active">Senarai Aduan</a>
            </div>

            <div class="sidebar-logout">
                <button class="logout-btn">Logout</button>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <!-- FILTER (UI ONLY FOR NOW) -->
            <form>
                Status:
                <select disabled>
                    <option>All</option>
                </select>

                Unit:
                <select disabled>
                    <option>All</option>
                </select>

                Tarikh:
                <input type="date" disabled>

                Cari:
                <input type="text" placeholder="Cari..." disabled>
            </form>

            <!-- TABLE -->
            <table class="aduan-table">
                <thead>
                <tr>
                    <th>Bil</th>
                    <th>Id Aduan</th>
                    <th>Nama Pengadu</th>
                    <th>Masalah</th>
                    <th>Unit</th>
                    <th>Keutamaan</th>
                    <th>Status</th>
                    <th>Tarikh Aduan</th>
                    <th>Tindakan</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="9" class="no-data">
                        Tiada data aduan buat masa ini
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
</body>
</html>
