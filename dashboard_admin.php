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
    background: #0306a0ff;
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
        <div class="page-title">Dashboard</div>
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
                <a href="dashboard_admin.php" class="active">Dashboard</a>
                <a href="Senarai_Aduan_admin.php">Senarai Aduan</a>
                <a href="Senarai_Pengguna_admin.php">Senarai Pengguna</a>
                <a href="Laporan_statistik_admin.php">Laporan Statistik</a>
            </div>

            <div class="sidebar-logout">
        <button class="logout-btn">Logout</button>
    </div>
        </div>

        

        
        <div class="content">
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

</body>

</html>
