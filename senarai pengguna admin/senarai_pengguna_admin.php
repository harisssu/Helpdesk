<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['noIC']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$namaUser = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Unknown';
$perananNama = "Admin";

$jabatanFilter = isset($_GET['jabatan']) ? $_GET['jabatan'] : ''; // untuk selected dropdown (optional)

/* Senarai jabatan untuk dropdown */
$sqlJabatanList = "SELECT idJabatan, namaJabatan FROM jabatan ORDER BY namaJabatan ASC";
$resultJabatanList = mysqli_query($conn, $sqlJabatanList);
if (!$resultJabatanList) {
    die("SQL Error Jabatan: " . mysqli_error($conn));
}

/* Default list semua pengguna (first load) */
$sql = "
SELECT 
    u.noIC AS noIC,
    u.namaUser AS namaUser,
    u.emel AS emel,
    j.namaJabatan AS namaJabatan,
    u.idJabatan AS idJabatan,
    p.namaPeranan AS namaPeranan,
    u.idPeranan AS idPeranan,
    'user' AS userType
FROM user u
LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
LEFT JOIN peranan p ON u.idPeranan = p.idPeranan

UNION ALL

SELECT 
    a.noICAdmin AS noIC,
    a.namaAdmin AS namaUser,
    a.emelAdmin AS emel,
    j.namaJabatan AS namaJabatan,
    a.idJabatan AS idJabatan,
    p.namaPeranan AS namaPeranan,
    a.idPeranan AS idPeranan,
    'admin' AS userType
FROM admin a
LEFT JOIN jabatan j ON a.idJabatan = j.idJabatan
LEFT JOIN peranan p ON a.idPeranan = p.idPeranan

UNION ALL

SELECT 
    t.noICTechnician AS noIC,
    t.namaTechnician AS namaUser,
    t.emelTechnician AS emel,
    j.namaJabatan AS namaJabatan,
    t.idJabatan AS idJabatan,
    p.namaPeranan AS namaPeranan,
    t.idPeranan AS idPeranan,
    'technician' AS userType
FROM technician t
LEFT JOIN jabatan j ON t.idJabatan = j.idJabatan
LEFT JOIN peranan p ON t.idPeranan = p.idPeranan

UNION ALL

SELECT 
    k.noICKetua AS noIC,
    k.namaKetua AS namaUser,
    k.emelKetua AS emel,
    j.namaJabatan AS namaJabatan,
    k.idJabatan AS idJabatan,
    p.namaPeranan AS namaPeranan,
    k.idPeranan AS idPeranan,
    'ketuaunit' AS userType
FROM ketuaunit k
LEFT JOIN jabatan j ON k.idJabatan = j.idJabatan
LEFT JOIN peranan p ON k.idPeranan = p.idPeranan

ORDER BY namaUser ASC
";

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

                    #editUserModal h3 {
                        margin-top: 0;
                        margin-bottom: 20px;
                        font-size: 22px;
                        color: #0306a0ff;
                        text-align: center;
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
                                    <label>Jabatan:</label>
                                        <select id="filterJabatan" name="jabatan" style="width: 10rem; padding: 0.2rem;">
                                            <option value="">Semua</option>

                                            <?php while($j = mysqli_fetch_assoc($resultJabatanList)) : ?>
                                                <option value="<?= $j['idJabatan']; ?>"
                                                    <?= ($jabatanFilter == $j['idJabatan']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($j['namaJabatan']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>


                                    <input type="text" id="searchName" placeholder="Search Nama Pengguna...">
                                    <button id="btnSearch">Cari</button>

                                    <button id="btnTambahUser" style="margin-bottom:1px; padding:4px 12px; cursor:pointer;">Tambah Pengguna</button>

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
                                        echo "<td>
                                                <button class='lihat-btn'
                                                        data-noic='".$row['noIC']."'
                                                        data-type='".$row['userType']."'>
                                                    Lihat
                                                </button>
                                            </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>Tiada aduan direkodkan</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="editUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
                    background: rgba(0,0,0,0.5); justify-content:center; align-items:center;">
                    <div style="background:#fff; padding:20px; border-radius:8px; width:400px; position:relative;">
                        <h3>Edit User</h3>
                        <form id="editUserForm">
                            <input type="hidden" name="table" id="modalUserType">


                            <div class="form-row">
                                <label>No IC:</label>
                                <input type="text" name="noIC" id="modalNoIC">
                            </div><br>

                            <div class="form-row">
                                <label>Nama:</label>
                                <input type="text" name="namaUser" id="modalNamaUser">
                            </div><br>

                            <div class="form-row">
                                <label>Kata Laluan:</label>
                                <input type="text" name="kataLaluan" id="modalKatalaluan">
                            </div><br>

                            <div class="form-row">
                                <label>Jabatan:</label>
                                <select name="idJabatan" id="modalJabatan" required>
                                <?php
                                $jabatanRes = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY idJabatan ASC");
                                while($jab = mysqli_fetch_assoc($jabatanRes)){
                                    echo "<option value='".$jab['idJabatan']."'>".$jab['namaJabatan']."</option>";
                                }
                                ?>
                                </select>
                            </div><br>

                            <div class="form-row">
                                <label>Emel:</label>
                                <input type="email" name="emel" id="modalEmel">
                            </div><br>

                            <div class="form-row">
                                <label>Jawatan:</label>
                                <input type="text" name="jawatan" id="modalJawatan">
                            </div><br>

                            <div class="form-row">
                                <label>No Office:</label>
                                <input type="text" name="noOffice" id="modalNoOffice">
                            </div><br>

                                <center>
                                    <button type="button" onclick="closeModal()">Tutup</button>
                                    <button type="button" id="deleteUserBtn">Buang</button>
                                    <button type="submit" style='padding:4px 10px; background:#0306a0ff; color:white; border:none; border-radius:5px; cursor:pointer;'>Simpan</button>
                                </center>  

                        </form>
                    </div>
                </div>

                <div id="addUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
                    background: rgba(0,0,0,0.5); justify-content:center; align-items:center;">
                    <div style="background:#fff; padding:20px; border-radius:8px; width:400px; position:relative;">
                        <h3>Tambah Pengguna</h3>
                        <form id="addUserForm">
                            <div class="form-row">
                                <label>No IC:</label>
                                <input type="text" name="noIC" required>
                            </div><br>

                            <div class="form-row">
                                <label>Nama:</label>
                                <input type="text" name="namaUser" required>
                            </div><br>

                            <div class="form-row">
                                <label>Kata Laluan:</label>
                                <input type="text" name="kataLaluan" required>
                            </div><br>

                            <div class="form-row">
                                <label>Jabatan:</label>
                                <select name="idJabatan" required>
                                    <?php
                                    $jabatanRes = mysqli_query($conn, "SELECT * FROM jabatan");
                                    while($jab = mysqli_fetch_assoc($jabatanRes)){
                                        echo "<option value='".$jab['idJabatan']."'>".$jab['namaJabatan']."</option>";
                                    }
                                    ?>
                                </select>
                            </div><br>

                            <div class="form-row">
                                <label>Emel:</label>
                                <input type="email" name="emel" required>
                            </div><br>

                            <div class="form-row">
                                <label>Jawatan:</label>
                                <input type="text" name="jawatan" required>
                            </div><br>

                            <div class="form-row">
                                <label>No Office:</label>
                                <input type="text" name="noOffice" required>
                            </div><br>

                            <div class="form-row">
                                <label>Peranan:</label>
                                <select name="perananType" required>
                                    <option value="">--Pilih Peranan--</option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="technician">Technician</option>
                                    <option value="ketuaunit">Ketua Unit</option>
                                </select>
                            </div><br>

                            <center>
                                <button type="button" onclick="closeAddModal()">Tutup</button>
                                <button type="submit" style='padding:4px 10px;
                                                            background:#0306a0ff;
                                                            color:white;
                                                            border:none;
                                                            border-radius:5px;
                                                            cursor:pointer;'>Simpan</button>
                            </center>
                        </form>
                    </div>
                </div>  

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    $('#btnTambahUser').on('click', function(){
                        $('#addUserModal').css('display','flex');
                    });

                    function closeAddModal() {
                        $('#addUserModal').hide();
                        $('#addUserForm')[0].reset(); 
                    }

                    $('#addUserForm').on('submit', function(e){
                        e.preventDefault();
                        
                        $.ajax({
                            url: 'tambah_user.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(response){
                                alert(response);
                                $('#addUserModal').hide();    
                                $('#addUserForm')[0].reset();  
                                loadUsers();                   
                            },
                            error: function(xhr){
                                alert(xhr.responseText);
                            }
                        });
                    });


                    $(document).ready(function () {
                    $('#filterJabatan').select2({
                        placeholder: "Semua Jabatan",
                        allowClear: true,
                        width: '10rem'
                    });

                    loadUsers();
                });


                    function confirmLogout() {
                        return confirm("Anda Pasti Untuk Log Keluar?");
                    }

                    function closeModal(){
                        $('#editUserModal').hide();
                    }

                    $(document).on('click', '.lihat-btn', function () {
                        let noIC = $(this).data('noic');
                        let type = $(this).data('type');

                        $.ajax({
                            url: 'fetch_user.php',
                            type: 'POST',
                            data: { noIC: noIC, type: type },
                            dataType: 'json',
                            success: function (data) {
                                if (!data.noIC) {
                                    alert('Data tidak dijumpai');
                                    return;
                                }

                                    $('#modalNoIC').val(data.noIC);
                                    $('#modalNamaUser').val(data.namaUser);
                                    $('#modalEmel').val(data.emel);
                                    $('#modalJabatan').val(data.idJabatan);
                                    $('#modalJawatan').val(data.jawatan);
                                    $('#modalNoOffice').val(data.noOffice);
                                    $('#modalKatalaluan').val(data.kataLaluan);

                                    $('#modalUserType').val(type);
                                    $('#editUserModal').css('display', 'flex');
                                },  
                            error: function (xhr) {
                                alert(xhr.responseText);
                            }
                        });
                    });

                    $('#editUserForm').on('submit', function(e){
                        e.preventDefault();
                        $.ajax({
                            url: 'update_user.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            success: function(response){
                                alert(response.message);
                                if(response.status === 'success'){
                                    $('#editUserModal').hide();
                                    loadUsers();
                                }
                            },
                            error: function(xhr){
                                alert(xhr.responseText);
                            }
                        });
                    });

                    $('#deleteUserBtn').on('click', function() {
                        if(!confirm("Anda pasti mahu membuang pengguna ini?")) return;

                        let noIC = $('#modalNoIC').val();
                        let type = $('#modalUserType').val();

                        $.ajax({
                            url: 'delete_user.php',
                            type: 'POST',
                            data: { noIC: noIC, userType: type },
                            success: function(response) {
                                alert(response);
                                $('#editUserModal').hide();
                                loadUsers(); // 
                            },
                            error: function(xhr) {
                                alert(xhr.responseText);
                            }
                        });
                    });

                    let typingTimer;
                    let typingDelay = 100;

                    function loadUsers() {
                        let idJabatan = $('#filterJabatan').val();
                        let search = $('#searchName').val();

                        $.ajax({
                            url: 'filter_user.php',
                            type: 'POST',
                            data: { idJabatan: idJabatan, search: search },
                            dataType: 'json',
                            success: function(data) {
                                let tbody = '';
                                if(data.length > 0){
                                    data.forEach((row, index) => {
                                        tbody += `<tr>
                                            <td>${index + 1}</td>
                                            <td>${row.namaUser}</td>
                                            <td>${row.namaJabatan}</td>
                                            <td>${row.emel}</td>
                                            <td>${row.namaPeranan}</td>
                                            <td>
                                                <button class='lihat-btn' data-noic='${row.noIC}' data-type='${row.userType}' style='                                                            padding:4px 10px;
                                                            background:#0306a0ff;
                                                            color:white;
                                                            border:none;
                                                            border-radius:5px;
                                                            cursor:pointer;'>
                                                    Lihat
                                                </button>
                                            </td>
                                        </tr>`;
                                    });
                                } else {
                                    tbody = "<tr><td colspan='6'>Tiada rekod dijumpai</td></tr>";
                                }
                                $('.aduan-table tbody').html(tbody);
                            },
                            error: function(xhr) {
                                alert(xhr.responseText);
                            }
                        });
                    }

                    $('#searchName').on('keyup', function() {
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(loadUsers, typingDelay);
                    });

                    $('#searchName').on('keydown', function() {
                        clearTimeout(typingTimer);
                    });

                    $('#filterJabatan').on('change', loadUsers);

                    loadUsers();
                </script>

            </body>
        </html>
