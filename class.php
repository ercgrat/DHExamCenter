<?php //class.php

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
else if($_SESSION['user']->role < 1 || !isset($_GET["id"]) || !is_numeric($_GET["id"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$header = '<script type="text/javascript" src="class.js"></script>';
$header.= '<link rel="stylesheet" type="text/css" href="class.css"/>';
output_start($header, $_SESSION["user"]);

$classid = $db_handle->real_escape_string($_GET["id"]);

$class_query = $db_handle->prepare("SELECT lassnamecay, ourseidcay FROM lassescay WHERE lassidcay = ?");
$class_query->bind_param("i", $classid);
$class_query->execute();
$class_query->store_result();
if($class_query->num_rows() != 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}
else
{
    $_SESSION["class-classid"] = $classid;

    $class_query->bind_result($class_title, $courseid);
    $class_query->fetch();
    
    $course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
    $course_query->bind_param("i", $courseid);
    $course_query->execute();
    $course_query->store_result();
    $course_query->bind_result($course_title);
    $course_query->fetch();
}

echo <<<_HEAD
    <h2>$class_title</h2>
    <h3><a class="title" href="course.php?id=$courseid">$course_title</a></h3>
    <hr/>
    <h3>Current Students</h3>
    <div id='student_table'>
_HEAD;

require_once "class-fragment-student_table.php";

echo "</div> <h3>Pending Students</h3> <div id='pending_student_table'>";

require_once "class-fragment-pending_student_table.php";

echo <<<_STUDENT
    </div>
    <hr/>
    <h3>Add a Student</h3>
    <form id="student_form">
        <table>
            <tr><td>Student Account Name: </td> <td><input id="student_account_name" name="account" type="text" size="40" maxlength="32"/><span class="warning"></span></td></tr>
        </table>
    </form>
    <div>
        <button id="student_button">Invite Student</button>
        <span></span>
    </div>
_STUDENT;

echo <<<_TA1
    <hr/>
    <h3>Appoint a Teaching Assistant</h3>
    <form id="ta_form">
        <table>
            <tr>
                <td>Student: </td>
                <td>
_TA1;
    
    require_once "class-fragment-student_selector.php";
    
echo <<<_TA2
                <span class="warning"/></td>
            </tr>
        </table>
    </form>
    <button id="ta_button">Appoint TA</button><span/>
_TA2;

output_end();

?>