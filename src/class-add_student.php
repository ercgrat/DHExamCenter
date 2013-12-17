<?php //class-add_student.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1 || !isset($_SESSION["class-classid"]))
{
    echo "1|You must be logged in as an administrator to use this feature.";
    exit();
}

$classid = $_SESSION["class-classid"];
$userid = $db_handle->real_escape_string($_GET["id"]);

if(is_numeric($userid))
{
    $duplicate_query = $db_handle->prepare("SELECT * from lasslinkscay WHERE lassidcay = ? AND seriduyay = ?");
    $duplicate_query->bind_param("ii", $classid, $userid);
    $duplicate_query->execute();
    $duplicate_query->store_result();
    if($duplicate_query->num_rows() > 0)
    {
        echo "0|This user is already in the class.";
        exit();
    }

    $link_query = $db_handle->prepare("INSERT INTO lasslinkscay (lassidcay, seriduyay) VALUES (?, ?)");
    $link_query->bind_param("ii", $classid, $userid);
    if(!$link_query->execute())
    {
        echo "1|An error occurred while adding the student.";
    }
    else
    {
        $name_query = $db_handle->prepare("SELECT ullnamefay FROM sersuyay WHERE seriduyay = ?");
        $name_query->bind_param("i", $userid);
        $name_query->execute();
        $name_query->store_result();
        $name_query->bind_result($name);
        $name_query->fetch();
        echo "2|$name has been added to the class!";
    }
}
else
{
    echo "0|The submitted user ID was non-numeric.";
}
?>