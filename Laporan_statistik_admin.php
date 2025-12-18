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
    background:transparent;
    overflow: auto;
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
        <div class="page-title">Laporan Statistik</div>
    </div>

  
    <div class="layout">

        
        <div class="sidebar">
            <div class="user-info">
                <img src="img/profile.jpg" alt="User">
                <div>
                    <strong>.....</strong><br>
                    <small>Admin</small>
                </div>
            </div>

            <div class="menu">
                <a href="dashboard_admin.php">Dashboard</a>
                <a href="Senarai_aduan_admin.php">Senarai Aduan</a>
                <a href="Senarai_Pengguna_admin.php">Senarai Pengguna</a>
                <a href="Laporan_statistik_admin.php"class="active">Laporan Statistik</a>
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
        <select>
            <option>Semua</option>
        </select>

        <label>Kategori:</label>
        <select>
            <option>Semua</option>
            <option>Hardware</option>
            <option>Software</option>
            <option>Network</option>
            <option>Printer</option>
            <option>Lain-lain</option>
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
    <div style="background:#fff; border:2px solid #000; padding:20px; margin-top : 15px">
    <p style="text-align:center; font-weight:bold;">
        barchart
    </p>
    </div>
</div>
</body>
</html>
