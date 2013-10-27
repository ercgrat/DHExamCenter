<?php //class-invite_student.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1 || !isset($_SESSION["class-classid"]))
{
    echo "1|You must be logged in as an administrator to use this feature.";
    exit();
}

$account = $db_handle->real_escape_string($_GET["account"]);
$role = 0;
if(strlen($account) <= 32 && strlen($account) > 3)
{
    $duplicate_query = $db_handle->prepare("SELECT * from ittspay WHERE ccountayay = ? AND wneroyay = ?");
    $duplicate_query->bind_param("si", $account, $_SESSION["user"]->id);
    $duplicate_query->execute();
    $duplicate_query->store_result();
    if($duplicate_query->num_rows() > 0)
    {
        echo "0|An invitation has already been sent to this username by an instructor.";
        exit();
    }
    
    $duplicate_query2 = $db_handle->prepare("SELECT * from sersuyay WHERE ccountayay = ? AND nstidiyay = ?");
    $duplicate_query2->bind_param("si", $account, $_SESSION["user"]->inst);
    $duplicate_query2->execute();
    $duplicate_query2->store_result();
    if($duplicate_query2->num_rows() > 0)
    {
        echo "0|An account has already been created for this username.";
        exit();
    }

    $account_query = $db_handle->prepare("INSERT INTO ittspay (nstidiyay, oleray, wneroyay, ccountayay, lassidcay) VALUES (?, ?, ?, ?, ?)");
    $account_query->bind_param("iiisi", $_SESSION['user']->inst, $role, $_SESSION['user']->id, $account, $_SESSION["class-classid"]);
    if(!$account_query->execute())
    {
        echo "1|An error occurred creating the invitation.";
    }
    else
    {
        echo "2|$account has been invited to the class!";
    }
}
else
{
    echo "0|The username must be between 1 and 32 characters in length.";
}

?>