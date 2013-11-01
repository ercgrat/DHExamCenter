<?php //instructor-create_course.php

require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1)
{
    echo "0|You must be logged in as an instructor to use this feature.";
    exit();
}

$course = $db_handle->real_escape_string($_GET['course_title']);
$instid = intval($db_handle->real_escape_string($_GET['inst_id']));
if(!$instid)
{
    echo "0|The submitted institution identifier does not exist or is not a number.";
    exit();
}
if(strlen($course) <= 256 && strlen($course) > 0)
{
    $inst_query = $db_handle->prepare("INSERT INTO oursescay (oursetitlecay, nstidiyay, seriduyay) VALUES(?, ?, ?)");
    $inst_query->bind_param("sii", $course, $instid, $_SESSION['user']->id);
    if(!$inst_query->execute())
    {
        echo "0|This course title is already taken.";
        exit();
    }
    else
    {
        echo "1|".htmlspecialchars($course)." has been created!";
    }
}
else
{
    echo "0|The course title must be between 1 and 256 characters in length.";
}

?>