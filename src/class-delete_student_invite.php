<?php //class-delete_student_invite.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1 || !isset($_SESSION["class-classid"]) || !isset($_GET["account"]))
{
    echo "1|You must be logged in as an administrator to use this feature.";
    exit();
}

$account = $db_handle->real_escape_string($_GET["account"]);

$account_query = $db_handle->prepare("DELETE FROM ittspay WHERE ccountayay = ? AND lassidcay = ?");
$account_query->bind_param("si", $account, $_SESSION["class-classid"]);
$account_query->execute();

?>