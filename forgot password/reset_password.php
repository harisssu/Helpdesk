<?php
session_start();
require_once __DIR__ . "/db_connect.php";

date_default_timezone_set("Asia/Kuala_Lumpur");


$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$error = "";
$success = "";

if ($token === '') {
    die("Token tiada.");
}

// ambil semua token yang belum used & belum expired (latest dulu)
$stmt = $conn->prepare("
    SELECT id, email, role_type, noIC, token_hash, expires_at, used
    FROM password_reset
    WHERE used = 0 AND expires_at >= NOW()
    ORDER BY id DESC
");
$stmt->execute();
$res = $stmt->get_result();

$resetRow = null;
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if (sha1($token) === $row['token_hash']) {
    $resetRow = $row;
    break;
}

    }
}

if (!$resetRow) {
    die("Link tidak sah atau telah tamat tempoh. Sila request semula.");
}

// bila user submit password baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if ($newPass === '' || $confirm === '') {
        $error = "Sila isi kata laluan baru.";
    } elseif (strlen($newPass) < 6) {
        $error = "Password minimum 6 aksara.";
    } elseif ($newPass !== $confirm) {
        $error = "Kata laluan tidak sama.";
    } else {
        $hashed = $hashed = $newPass; // simpan plain text (DEV ONLY)

        $noIC = $resetRow['noIC'];
        $role = $resetRow['role_type'];

        if ($role === 'admin') {
            $u = $conn->prepare("UPDATE admin SET kataLaluanAdmin=? WHERE noICAdmin=?");
        } elseif ($role === 'technician') {
            $u = $conn->prepare("UPDATE technician SET kataLaluanTechnician=? WHERE noICTechnician=?");
        } elseif ($role === 'ketuaunit') {
            $u = $conn->prepare("UPDATE ketuaunit SET kataLaluanKetua=? WHERE noICKetua=?");
        } else {
            $u = $conn->prepare("UPDATE user SET kataLaluan=? WHERE noIC=?");
        }

        $u->bind_param("ss", $hashed, $noIC);

        if ($u->execute()) {
            // mark used
            $mark = $conn->prepare("UPDATE password_reset SET used=1 WHERE id=?");
            $mark->bind_param("i", $resetRow['id']);
            $mark->execute();

            $success = "Password berjaya ditukar. Sila login semula.";
        } else {
            $error = "Gagal update password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Reset Kata Laluan</title>
     <style>
    body{
      font-family: Arial, Helvetica, sans-serif;
      margin:0;
      min-height:100vh;
      background-image:url("img/it6.jpg");
      background-size:cover;
      background-position:center;
      background-repeat:no-repeat;
      display:flex;
      justify-content:center;
      align-items:center;
      padding: 20px;
    }

    .card{
      width: 420px;
      background: rgba(240, 237, 237, 0.95);
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }

    .title{
      margin: 0 0 16px 0;
      text-align:center;
      color:#111;
      font-size: 24px;
      font-weight: 700;
    }

    .desc{
      margin: 0 0 18px 0;
      text-align:center;
      color:#333;
      font-size: 14px;
    }

    .field{
      margin-bottom: 14px;
    }

    label{
      display:block;
      margin-bottom: 6px;
      color:#111;
      font-weight: 600;
      font-size: 14px;
    }

    input{
      width:100%;
      box-sizing:border-box;
      padding: 10px 12px;
      border: 1px solid #c9c9c9;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
    }

    input:focus{
      border-color:#081c4b;
      box-shadow: 0 0 0 3px rgba(8, 28, 75, 0.15);
    }

    .btn{
      width:100%;
      padding: 10px 12px;
      border: 0;
      border-radius: 8px;
      background:#081c4b;
      color:#fff;
      font-weight: 700;
      cursor:pointer;
      margin-top: 6px;
    }

    .btn:hover{
      filter: brightness(1.05);
    }

    .err{
      background: rgba(176,0,32,0.10);
      border: 1px solid rgba(176,0,32,0.25);
      color:#b00020;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 14px;
    }

    .ok{
      background: rgba(10,122,10,0.10);
      border: 1px solid rgba(10,122,10,0.25);
      color:#0a7a0a;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 14px;
    }

    .link{
      display:block;
      text-align:center;
      margin-top: 12px;
      color:#081c4b;
      font-weight: 700;
      text-decoration:none;
    }

    .link:hover{ text-decoration: underline; }
  </style>
</head>
<body>

  <div class="card">
    <h2 class="title">Reset Kata Laluan</h2>
    <p class="desc">Sila masukkan kata laluan baharu anda.</p>

    <?php if ($error !== ""): ?>
      <div class="err"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
      <div class="ok"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
      <a class="link" href="login.html">Pergi ke Login</a>
    <?php else: ?>
      <form method="POST">
        <div class="field">
          <label>Password Baru</label>
          <input type="password" name="new_password" required>
        </div>

        <div class="field">
          <label>Sahkan Password</label>
          <input type="password" name="confirm_password" required>
        </div>

        <button class="btn" type="submit">Simpan</button>
      </form>
    <?php endif; ?>
  </div>

</body>
</html>
