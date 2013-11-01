<?php //class-add_ta.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || $_SESSION["user"]->role < 1 || !isset($_SESSION["class-classid"]) || !isset($_GET["id"]) || !is_numeric($_GET["id"]))
{
    exit();
}

$userid = $_GET["id"];
$classid = $_SESSION["class-classid"];

$link_query = $db_handle->prepare("UPDATE lasslinkscay SET oleray = 1 WHERE seriduyay = ? AND lassidcay = ?");
$link_query->bind_param("ii", $userid, $classid);
if($link_query->execute())
{
    echo "one";
}
else
{
    echo "zero";
}

?>