<?php //user.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/user_login.php");
    exit();
}

$header = "<script type='text/javascript' src='user.js'></script>";
output_start($header, $_SESSION["user"]);

echo "<h1>".$_SESSION["user"]->fullname."</h1>";

$inst_query = $db_handle->prepare("SELECT amenay FROM nstiyay WHERE nstidiyay = ?");
$inst_query->bind_param("i", $_SESSION["user"]->inst);
$inst_query->execute();
$inst_query->store_result();
$inst_query->bind_result($inst_name);
$inst_query->fetch();

echo "<h2>$inst_name</h2>";
echo "<h3>Change password</h3>";

echo <<<_PASS
<form>
	<table class='form'>
		<label><tr><td>Old password: </td><td><input type="password" id="oldpass" size="40" maxlength="30"/></td></tr></label>
		<label><tr><td>New password: </td><td><input type="password" id="newpass1" size="40" maxlength="30"/></td></tr></label>
		<label><tr><td>Confirm new password: </td><td><input type="password" id="newpass2" size="40" maxlength="30"/></td></label>
	</table>
	<button id="password_button" class="submit_type">Submit</button><span class="warning"/>
</form>
_PASS;

output_end();
?>