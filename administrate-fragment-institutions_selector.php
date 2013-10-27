<?php //administrate-fragment-institutions_selector.php
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

echo "<select id='institution_selector'>";
$inst_query = $db_handle->prepare("SELECT amenay, nstidiyay FROM nstiyay GROUP BY amenay");
if(!$inst_query->execute()) { output_runtime_error("Problem retrieving institutions to select."); }
$inst_query->store_result();
$inst_query->bind_result($inst_name, $instid);
while($inst_query->fetch())
{
    echo "<option value='$instid'>$inst_name</option>";
}
echo "</select>";

?>