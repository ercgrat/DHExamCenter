<?php //user_password_reset
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

$header = "<script type='text/javascript' src='user_password_reset.js'></script>";
output_start($header, NULL);
echo <<<_TEXT
	<h2>Reset Password</h2>
	<p>Are you sure you want to submit a password reset request?</p>
	<p>If you submit a request to reset your password, the site administrator will be given privileges to set a new password for your account.
	Make sure that when you submit the request you get in contact with the site administrator or your instructor so that they give you a new password.</p>
	<p>After getting your new password from the site administrator or your instructor, you should change your password again after logging in.</p>
	<form>
		<table class='form'>
			<tr><td>Username: </td><td><input type="text" id="username" size="40" maxlength="40"/><span class="warning"></span></td></tr>
		</table>
	</form>
	<button class="submit_type" id="reset_button">Request Password Reset</button><span></span>
_TEXT;

output_end();

?>