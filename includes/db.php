<?php ob_start();

$db_host = 'localhost';
$db_user = 'user123';
$db_pass = 'tajnehaslo';
$db_name = 'projekt';
$db_port = 3307;

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

$query = "SET NAMES utf8";
mysqli_query($connection,$query);
