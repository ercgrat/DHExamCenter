<?php
require_once "authenticate.php";

session_start();
session_regenerate_id();

$user = $_SESSION['user'];
if(isset($user) && $user->logged_in)
{
    $_SESSION = array();
    if(session_id() != "" || isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time() - 2592000, '/');
    }
    session_destroy();
}

header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");

?>