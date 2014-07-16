<?php //user-request_password_reset.php
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

$username = $_POST["username"];
$userid = User::get_id_for_username($username);
if(is_numeric($userid) && $userid > 0) {
	$request_query = $db_handle->prepare("INSERT INTO password_change_requests VALUES(?)");
	$request_query->bind_param("i", $userid);
	$request_query->execute();
	echo "1, Password reset requested.";
} else {
	echo "0,Invalid username.";
}

?>