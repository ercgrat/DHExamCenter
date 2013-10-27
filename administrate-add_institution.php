<?php //administrate-add_institution.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 2)
{
    echo "0|You must be logged in as an administrator to use this feature.";
    exit();
}

$inst_name = $db_handle->real_escape_string($_GET['inst_name']);
$date = date("Y-m-d");
if(strlen($inst_name) <= 128 && strlen($inst_name) > 0)
{
    $inst_query = $db_handle->prepare("INSERT INTO nstiyay (amenay, oindatejay) VALUES(?, ?)");
    $inst_query->bind_param("ss", $inst_name, $date);
    if($inst_query->execute())
    {
        echo "1|".htmlspecialchars($inst_name)." was added to eXam Center!";
    }
    else
    {
        echo "0|An error occurred while creating the institution. Please try again or contact the site administrator.";
    }
}
else
{
    echo "0|The institution name must be between 1 and 128 characters in length.";
}
$inst_query = $db_handle->prepare("");
?>