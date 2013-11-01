<?php //create_resource_form.php

require_once "layout.php";
require_once "authenticate.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

output_start("", $_SESSION["user"]);
echo <<<_FORM
    <h2>Add resource</h2>
    <form method="post" action="create_resource.php">
        <p><label>Name: <input type="text" name="resource_name" size="60"/></label></p>
        <p><label>URL: <input type="text" name="resource_link" size="60" value="https://"/></label></p>
        <input type="submit" value="Submit"/>
    </form>
_FORM;

output_end();

?>