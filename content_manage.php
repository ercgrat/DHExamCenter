<?php // content_manage.php

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

output_start("", $_SESSION['user']);

echo <<<_OPTIONS
<h2>Manage Content</h2>
<ul>
    <li><a class="implied_link" href='create_test_form.php'>Create new assessment</a></li>
    <li><a class="implied_link" href='select_modify_test.php'>Select assessment to modify</a></li>
    <li><a class="implied_link" href='create_resource_form.php'>Add resource</a></li>
</ul>
_OPTIONS;

output_end();
?>