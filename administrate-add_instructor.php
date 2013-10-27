<?php //administrate-add_instructor.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 2)
{
    echo "1|You must be logged in as an administrator to use this feature.";
    exit();
}

$username = $db_handle->real_escape_string($_GET['username']);
$instid = intval($db_handle->real_escape_string($_GET['instid']));
if(strlen($username) <= 32 && strlen($username) > 0)
{
    $inst_query = $db_handle->prepare("SELECT amenay FROM nstiyay WHERE nstidiyay = ?");
    $inst_query->bind_param("i",$instid);
    $inst_query->execute();
    $inst_query->store_result();
    if($inst_query->num_rows != 1)
    {
        echo "0|Selected institution does not exist.";
        exit();
    }
    else
    {
        $inst_query->bind_result($institution);
        $inst_query->fetch();
    }
    
    $role = 1;
    
    $instructor_query = $db_handle->prepare("INSERT INTO ittspay (nstidiyay, oleray, wneroyay, ccountayay) VALUES(?, ?, ?, ?)");
    $instructor_query->bind_param("iiis", $instid, $role, $_SESSION['user']->id, $username);
    if($instructor_query->execute())
    {
        echo "1|".htmlspecialchars($_GET['username'])." is now a pending user for $institution!";
    }
    else
    {
        echo "0|An error occurred while adding the user. Please try again or contact the site administrator.";
    }
    
}
else
{
    echo "0|The username must be between 1 and 32 characters in length.";
}
?>