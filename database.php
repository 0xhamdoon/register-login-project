<?php
    $hostName = "localhost";
    $dbUser = "root";
    $dbPasswrd = "";
    $dbName = "login_register";
    $conn = mysqli_connect($hostName, $dbUser, $dbPasswrd, $dbName);
    if (!$conn) {
        die("Something went wrong");
    }
?>