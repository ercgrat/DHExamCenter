<?php //user_created.php
require_once "authenticate.php";
require_once "layout.php";

session_start();
session_regenerate_id();

output_start();

if(!isset($_SESSION['user_created'])) { header("Location: https://" . $_SERVER["HTTP_HOST"] . "/index.php"); }

if($_SESSION['user_created'])
{
    echo "<p>Successfully created your user account.  Please proceed to <a href='user_login.php'>login</a>.</p>";
}
else
{
    echo "<p>An error occurred when trying to create your account  :(  <a href='user_create.php'>Try again?</a></p>";
}

output_end();
?>