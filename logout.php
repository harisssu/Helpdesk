<?php
session_start();

/* Buang semua data session */
$_SESSION = [];

/* Destroy session */
session_destroy();

/* Redirect ke login */
header("Location: login.html");
exit;
