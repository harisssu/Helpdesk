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
    background: #cbcbcb;
    position: relative;
    overflow: hidden;
}
.app {
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f2f2f2;
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
    background: #f7f7f7;
    overflow: auto;
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
                    <strong>.....</strong><br>
                    <small>User</small>
                </div>
            </div>

            <div class="menu">
                <a href="Status_Aduan_User.php" class="active">Status Aduan</a>
                <a href="Hantar_Aduan_User.php">Hantar Aduan</a>
            </div>
        </div>

        
        <div class="content">
            
        </div>

    </div>
</div>

</body>
</html>
 