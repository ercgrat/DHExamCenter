<?php //administrate.php
require_once "authenticate.php";
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if($_SESSION['user']->role < 2)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}
output_start($header, $_SESSION['user']);
echo "<p>SADFASDFASDFASD</p>";
/*$header = "<script type='text/javascript'>var _administrateRoot = \"".$_SERVER["HTTP_HOST"]."/administrate/\";</script>";
$header.= "<script type='text/javascript' src='administrate.js'></script>";
output_start($header, $_SESSION['user']);

echo <<<_HEAD
    <h2>ADMIN DASHBOARD</h2>
_HEAD;

echo <<<_DATA1
    <h3>FACULTY</h3>
    <h4>Pending Faculty</h4>
    <div id="pending_users_table">
_DATA1;

require_once "https://".$_SERVER["HTTP_HOST"]."/administrate/fragments/pending_users_table.php";

echo <<<_DATA2
    </div>
    <h4>Current Faculty</h4> <div id="current_faculty_table">
_DATA2;

require_once "https://".$_SERVER["HTTP_HOST"]."/administrate/fragments/current_faculty_table.php";

echo <<<_DATA3
    </div>   
_DATA3;

echo <<<_INSTR1
    <h3>ADD AN INSTRUCTOR</h3>
    <form>
        <table>
            <tr><td>Institution: </td> <td id="institutions_selector">
_INSTR1;

require_once "https://".$_SERVER["HTTP_HOST"]."/administrate/fragments/institutions_selector.php";

echo <<<_INSTR2
            </td></tr>
            <tr><td>Instructor Username: </td><td><input id="instructor_username" type="text" size="40" maxlength="32"/><span class="warning"></span></td>
        </table>
    </form>
    <div><button id="instructor_button">Add Instructor</button><span></span></div>
_INSTR2;
    
echo <<<_INSTI
    <h3>CREATE AN INSTITUTION</h3>
    <form>
        <table>
            <label><tr><td>Institution Name: </td> <td><input id="institution_name" type="text" size="40" maxlength="128"/><span class="warning"></span></td></label>
        </table>
    </form>
    <div><button id="institution_button">Create Institution</button><span></span></div>
_INSTI;

echo <<<_PASSWORD
	<h3>FIELD PASSWORD CHANGE REQUESTS</h3>
	<form>
		<table><tr><td>Account: </td><td>
_PASSWORD;
require_once "https://".$_SERVER["HTTP_HOST"]."/administrate/fragments/password_change.php";
echo <<<_PASSWORD
		</td></tr>
			<label><tr><td>Password: </td><td><input id="password" type="password" size="40" maxlength="128"/><span class="warning"></span></td></label>
			<label><tr><td>Confirm: </td><td><input id="confirm_password" type="password" size="40" maxlength="128"/><span class="warning"></span></td></label>
		</table>
	</form>
_PASSWORD;
*/
output_end();

?>