<?php //course-add_resource.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || !isset($_SESSION["course-courseid"]))
{
    exit();
}

$resource_name = $_GET["name"];
$resource_link = $_GET["link"];

if(!isset($resource_name) || strlen($resource_name) == 0 || !isset($resource_link) || strlen($resource_link) == 0 || strlen($resource_name) > 256 || strlen($resource_link) > 512)
{
    exit();
}

$resource_query = $db_handle->prepare("INSERT INTO esourcesray (esourcenameray, inklay, ourseidcay) VALUES(?, ?, ?)");
$resource_query->bind_param("ssi", $resource_name, $resource_link, $_SESSION["course-courseid"]);
$resource_query->execute();

?>