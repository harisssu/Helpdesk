<?php
include "db_connect.php";

if(isset($_POST['noIC'])){
    $noIC = $_POST['noIC'];
    $sql = "SELECT * FROM user WHERE noIC='$noIC'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    echo json_encode($row);
}
?>
