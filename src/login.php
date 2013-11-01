<?php //login.php
$hostname = 'localhost';
$db = 'xlearn';
$db_username = 'php_access';
$db_password = 'monotremes';

$db_handle = new mysqli($hostname, $db_username, $db_password, $db);
if (mysqli_connect_errno($db_handle)) die("Unable to connect to database.");

$db_handle_pdo = new PDO("mysql:host=$hostname;dbname=$db;charset=utf8", $db_username, $db_password);
?>