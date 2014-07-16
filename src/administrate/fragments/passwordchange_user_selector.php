<?php //passwordchange_user_selector.php
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

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

echo "<select id='passwordchange_user_selector'><option value=''></option>";
$user_query = $db_handle->prepare("SELECT password_change_requests.userid, sersuyay.ccountayay, sersuyay.ullnamefay FROM password_change_requests JOIN sersuyay ON password_change_requests.userid = sersuyay.seriduyay ORDER BY sersuyay.ccountayay");
$user_query->execute();
$user_query->store_result();
$user_query->bind_result($userid, $account, $fullname);
while($user_query->fetch())
{
    echo "<option value='$userid'>$account - $fullname</option>";
}
echo "</select>";

?>