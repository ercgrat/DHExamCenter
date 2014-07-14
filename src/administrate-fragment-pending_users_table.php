<?php //administrate-fragment-pending_users.php

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

function role_string($role)
{
    if($role == 0)
    {
        return "Student";
    }
    else if($role == 1)
    {
        return "Instructor";
    }
}

$pend_query = $db_handle->prepare("SELECT amenay, oleray, ccountayay FROM nstiyay, ittspay WHERE nstiyay.nstidiyay = ittspay.nstidiyay AND ittspay.wneroyay = ? AND oleray = 1 GROUP BY amenay, ccountayay");
$pend_query->bind_param("i", $_SESSION['user']->id);
if(!$pend_query->execute()) { output_runtime_error("Problem retrieving pending users."); }
$pend_query->store_result();
$pend_query->bind_result($inst, $role, $account);
if($pend_query->num_rows() > 0)
{
    $pend_query->fetch();
    $current_inst = $inst;
    
    echo "<table class='information'><tr><th>Institution</th><th>Account</th><th>Role</th></tr><tr><td>$inst</td><td>$account</td><td>".role_string($role)."</td></tr>";
    while($pend_query->fetch())
    {
        if($inst != $current_inst)
        {
            $current_inst = $inst;
            echo "<tr><td>$inst</td><td>$account</td><td>".role_string($role)."</td></tr>";
        }
        else
        {
            echo "<tr><td></td><td>$account</td><td>".role_string($role)."</td></tr>";
        }
    }
    echo "</table>";
}
else
{
    echo "<p>There are no pending faculty invitations.</p>";
}

?>