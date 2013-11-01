<?php //create_resource.php

require_once "layout.php";
require_once "authenticate.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

if(!isset($_POST["resource_name"]) || !isset($_POST["resource_link"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/create_resource.php");
    exit();
}

output_start("", $_SESSION["user"]);
$resource_query = $db_handle->prepare("INSERT INTO esourcesray(esourcenameray, inklay) VALUES(?, ?)");
$resource_query->bind_param("ss", $_POST["resource_name"], $_POST["resource_link"]);
if($resource_query->execute())
{
    echo "<p>Successfully added a new resource.</p>";
}
else
{
    echo "<p>Resource addition failed.</p>";
}

output_end();

?>