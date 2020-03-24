<?php
# this file is for connecting the database.
$servername = "localhost";
$username = "root";
$userpassword = "mai123";

$mysql_connect = mysqli_connect($servername, $username, $userpassword);
if (!$mysql_connect)
    die("Cannot connect to database " . mysqli_connect_error());
    mysqli_select_db($mysql_connect, 'neighborhood');
?>