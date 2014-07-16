<?php //user-change_password.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/user_login.php");
    exit();
}

if(!$_SESSION["user"]->password_check($_SESSION["user"]->id, $_POST['oldpass'])) {
	echo "0,The old password entered did not match your current password.";
	exit();
}

if($_POST["newpass1"] != $_POST["newpass2"]) {
	echo "0,The new password fields do not match.";
	exit();
}

if(strlen($_POST["newpass1"]) < 8 || strlen($_POST["newpass1"]) > 35) {
	echo "0,Your new password must be between 8 and 35 characters in length.";
	exit();
}

$password = $_POST["newpass1"];
User::change_password($_SESSION["user"]->id, $password);
echo "1,Your password has been changed successfully!";

?>