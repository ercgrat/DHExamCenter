<?php //class-fragment-student_table.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1 || !isset($_SESSION["class-classid"])) {
    exit();
}

$classid = $db_handle->real_escape_string($_SESSION["class-classid"]);

$account_query = $db_handle->prepare("SELECT ccountayay FROM ittspay WHERE lassidcay = ?");
$account_query->bind_param("i", $classid);
if(!$account_query->execute()) { exit(); }
$account_query->store_result();

if($account_query->num_rows() == 0) {
    echo "<p>There are no pending student invitations.</p>";
} else {
	$account_query->bind_result($account);
	echo "<table>";
	while($account_query->fetch()) {
		echo "<tr><td>$account</td><td><button class='delete_invite_button'>Cancel Invitation</button></td></tr>";
	}
	echo "</table>";
}