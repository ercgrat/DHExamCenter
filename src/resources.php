<?php //about.php
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

output_start("", $_SESSION["user"]);

echo <<<_CONTENT
<h2>Learning Resources</h2>
<h3>Source Documents</h3>
<p>The following XML documents are referenced by certain questions in the assessments offerred:</p>
<ul>
_CONTENT;

$resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray");
$resource_query->execute();
$resource_query->store_result();
$resource_query->bind_result($resource_name, $inklay);

for($i = 0; $i < $resource_query->num_rows(); $i++)
{
    $resource_query->fetch();
    echo "<li><a href='$inklay'>$resource_name</a></li>";
}

echo "</ul>";

output_end();
?>