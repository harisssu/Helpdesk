<?php
require_once __DIR__ . "/src/Exception.php";
require_once __DIR__ . "/src/PHPMailer.php";
require_once __DIR__ . "/src/SMTP.php";

function sendResetLink($toEmail, $toName, $link) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = "a.blqss.1725@gmail.com";
        $mail->Password = "nkyh ispz opjn xfyr";

        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // ✅ Fix untuk XAMPP lama (LOCAL DEV ONLY)
        $mail->SMTPOptions = array(
          'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          )
        );

        $mail->setFrom("a.blqss.1725@gmail.com", "E-ICT Aduan");
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Reset Kata Laluan - E-ICT Aduan";
        $mail->Body = "
            <p>Hi <b>".htmlspecialchars($toName)."</b>,</p>
            <p>Klik link reset password (sah 10 minit):</p>
            <p><a href='{$link}'>{$link}</a></p>
        ";

        $mail->send();
        return true;

    } catch (\Exception $e) {
        error_log("PHPMailer error: " . $e->getMessage());
        error_log("ErrorInfo: " . $mail->ErrorInfo);
        return false;
    }
}
