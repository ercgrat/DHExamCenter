<?php //progress.php
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

output_start("", $_SESSION['user']);
echo "<p>This feature of XLearn is under construction.</p>";
output_end();
?>