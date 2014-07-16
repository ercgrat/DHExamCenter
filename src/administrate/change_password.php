<?php //change_password.php
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || $_SESSION["user"]->role < 2) {
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
$userid = $_POST["userid"];

if(!is_numeric($userid)) {
	echo "2,Invalid user id.";
	exit();
}
$request_query = $db_handle->prepare("SELECT * FROM password_change_requests WHERE userid = ?");
$request_query->bind_param("i", $userid);
$request_query->execute();
$request_query->store_result();
if($request_query->num_rows() < 1) {
	echo "2,Invalid user selection.";
	exit();
}

User::change_password($userid, $password);
$delete_query = $db_handle->prepare("DELETE FROM password_change_requests WHERE userid = ?");
$delete_query->bind_param("i", $userid);
$delete_query->execute();
echo "1,The password has been changed successfully!";
?>