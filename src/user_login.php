<?php //user_login.php
require_once "login.php";
require_once "authenticate.php";
require_once "sanitize.php";
require_once "layout.php";

function output_login_form($redirect = NULL)
{
    if(isset($redirect)) { echo "<p>You must be logged in to access this page.</p>"; }
    echo <<<_FORM
    <div id="login">
        <h2>Login</h2>
        <form method="post" action="user_login.php">
            <table>
                <label><tr><td>Username: </td><td><input type="text" name="username" size="40" maxlength="30"/></td></tr><label>
                <label><tr><td>Password: </td><td><input type="password" name="password" size="40" maxlength="30"/></td></tr></label>
                <tr><td><input type="submit"/></td></tr>
            </table>
        </form>
        <p>New user? <a href="user_create.php">Create an account.</a></p>
    </div>
_FORM;
}

session_start();
session_regenerate_id();

if(isset($_POST['username']) && isset($_POST['password'])) // Check for form submission and attempt a login.
{
    $_SESSION['user'] = new User();
    $user = $_SESSION['user'];
    $user->login($_POST['username'], $_POST['password']);

    if($user->logged_in) // On successful login, redirect to the last page visited if set, or to the home page.
    {
        if(isset($_SESSION['redirect']))
        {
            $redirect = $_SESSION['redirect'];
            $_SESSION['redirect'] = NULL;
            header("Location: https://". $_SERVER["HTTP_HOST"] . $redirect);
        }
        else { header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php"); }
    }
    else // Login failed, output error message along with login form.
    {
        output_start("", $_SESSION['user']);
        echo <<<_FAILURE
        <p><span class="warning">Invalid username/password combination.</span></p>
_FAILURE;
        output_login_form();
    }
}
else if(!isset($_SESSION['user']) || !$_SESSION['user']->logged_in) // If no form information was submitted, output a login form.
{
    output_start("", $_SESSION['user']);
    output_login_form($_SESSION['redirect']);
}
else // If the user is already logged in, redirect to the last page visited if set, or to the home page.
{
    if(isset($_SESSION['redirect'])) {
		$redirect = $_SESSION['redirect'];
		$_SESSION['redirect'] = NULL;
		header("Location: https://".$_SERVER["HTTP_HOST"].$redirect);
	}
    else { header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php"); }
    exit();
}

output_end();
?>