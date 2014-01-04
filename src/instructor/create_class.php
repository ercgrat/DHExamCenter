<?php //instructor-create_class.php

require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1)
{
    echo "0|You must be logged in as an instructor to use this feature.";
    exit();
}

$course_id = intval($db_handle->real_escape_string($_GET['course_id']));
$class = $db_handle->real_escape_string($_GET['class_title']);
if(!$course_id)
{
    echo "0|The submitted institution identifier does not exist or is not a number.";
    exit();
}

if(strlen($class) <= 256 && strlen($class) > 0)
{
    $class_query = $db_handle->prepare("INSERT INTO lassescay (ourseidcay, lassnamecay) VALUES(?, ?)");
    $class_query->bind_param("is", $course_id, $class);
    if(!$class_query->execute())
    {
        echo "2|This class title is already taken.";
        exit();
    }
    
    $id_query = $db_handle->prepare("SELECT lassidcay FROM lassescay WHERE ourseidcay = ? AND lassnamecay = ?");
    $id_query->bind_param("is", $course_id, $class);
    $class_id = 0;
    if($id_query->execute())
    {
        $id_query->store_result();
        $id_query->bind_result($class_id);
        $id_query->fetch();
    }
    else
    {
        echo "0|Error identifying the class entry.";
        exit();
    }
    
    $link_query = $db_handle->prepare("INSERT INTO lasslinkscay (seriduyay, oleray, lassidcay) VALUES(?, ?, ?)");
    $link_query->bind_param("iii", $_SESSION['user']->id, intval(2), $class_id);
    if($link_query->execute())
    {
        echo "1|You are ready to create content for ".htmlspecialchars($class)."!";
        exit();
    }
    else
    {
        echo "0|Error establishing class link.";
        exit();
    }
}
else
{
    echo "2|The class title must be between 1 and 256 characters in length.";
    exit();
}

?>