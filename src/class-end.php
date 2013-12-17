<?php //class-end.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

$classid = $_SESSION["class-classid"];
$_SESSION["class-classid"] = NULL;

$termination_query = $db_handle->prepare("UPDATE lassescay SET nsessioniyay = 0 WHERE lassidcay = ?");
$termination_query->bind_param("i", $classid);
$termination_query->execute();

?>