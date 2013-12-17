<?php //student.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if ($_SESSION["user"]->role != 0)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$header = "<link rel='stylesheet' type='text/css' href='student.css'/>";
$header.= "<script type='text/javascript' src='student.js'></script>";
output_start($header, $_SESSION["user"]);

$inst_query = $db_handle->prepare("SELECT amenay FROM nstiyay WHERE nstidiyay = ?");
$inst_query->bind_param("i", $_SESSION["user"]->inst);
$inst_query->execute();
$inst_query->store_result();
$inst_query->bind_result($inst_name);
$inst_query->fetch();
echo "<h2>$inst_name</h2>";
echo "<hr/>";

$ta_query = $db_handle->prepare("SELECT lassidcay FROM lasslinkscay WHERE seriduyay = ? AND oleray = 1");
$ta_query->bind_param("i", $_SESSION["user"]->id);
$ta_query->execute();
$ta_query->store_result();
$ta_query->bind_result($classid);
if($ta_query->num_rows() > 0)
{
    echo "<h2>Courses you TA</h2>";
    echo "<ul style='list-style:none'>";
    $courses = array();
    while($ta_query->fetch())
    {
        $course_query = $db_handle->prepare("SELECT ourseidcay, nsessioniyay FROM lassescay WHERE lassidcay = ?");
        $course_query->bind_param("i", $classid);
        $course_query->execute();
        $course_query->store_result();
        $course_query->bind_result($courseid, $insession);
        $course_query->fetch();
        
        $course = array($courseid, $insession);
        array_push($courses, $course);
    }
    
    foreach($courses as $course)
    {
        $courseid = $course[0];
        $insession = $course[1];
    
        $title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
        $title_query->bind_param("i", $courseid);
        $title_query->execute();
        $title_query->store_result();
        $title_query->bind_result($course_title);
        $title_query->fetch();
        
        if($insession == 1)
        {
            echo "<li><a href='course.php?id=$courseid'>$course_title</a></li>";
        }
        else
        {
            echo "<li><span class='disabled'>$course_title <span class='note'>The sections you taught have ended.</span></span></li>";
        }
    }
    echo "</ul>";
    echo "<hr/>";
}

echo <<<_HEAD
    <h2>Classes</h2>
    <p>Select topic tags from one of your classes to take a short assessment focused on those topics.</p>
_HEAD;

$class_query = $db_handle->prepare("SELECT lassidcay FROM lasslinkscay WHERE seriduyay = ? AND oleray = 0");
$class_query->bind_param("i", $_SESSION["user"]->id);
$class_query->execute();
$class_query->store_result();
$class_query->bind_result($classid);

if($class_query->num_rows() == 0)
{
    echo "<p>You are not currently participating in any classes.</p>";
}
else
{
    while($class_query->fetch())
    {
        $classname_query = $db_handle->prepare("SELECT lassnamecay, ourseidcay, nsessioniyay FROM lassescay WHERE lassidcay = ?");
        $classname_query->bind_param("i", $classid);
        $classname_query->execute();
        $classname_query->store_result();
        $classname_query->bind_result($class_name, $courseid, $insession);
        $classname_query->fetch();
        
        $course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
        $course_query->bind_param("i", $courseid);
        $course_query->execute();
        $course_query->store_result();
        $course_query->bind_result($course_title);
        $course_query->fetch();
        
        if($insession == 1)
        {
            echo "<h3>$course_title</h3>";
            echo "<h4>$class_name</h4>";
            
            echo "<form method='post' action='exam.php'>";
            $_SESSION["course-courseid"] = $courseid;
            require "course-fragment-tags_table.php";
            echo "<input type='submit' value='Start Exam'/>";
            echo "<input name='courseid' type='hidden' value='$courseid'/>";
            echo "<div></div>";
            echo "</form>";
        }
        else
        {
            echo "<h3 class='disabled'>$course_title</h3>";
            echo "<h4 class='disabled'>$class_name <span class='note'>(ended)</span></h4>";
            
        }
    }
}
output_end();
?>