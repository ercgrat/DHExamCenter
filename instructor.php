<?php
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
else if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$header = "<script type='text/javascript' src='instructor.js'></script>";
output_start($header, $_SESSION['user']);

echo <<<_HEAD
    <h2>INSTRUCTOR DASHBOARD</h2>
    <hr/>
_HEAD;

echo <<<_COURSE1
    <h3>COURSES</h3>
    <div id="courses_table">
_COURSE1;

require_once "instructor-fragment-course_table.php";

echo <<<_COURSE2
    </div>
    <hr/>
    <h3>CREATE A COURSE</h3>
    <form>
        <p id="course_warning"></p>
        <table>
            <tr><td>Institution:</td> <td>
_COURSE2;

    $inst_query = $db_handle->prepare("SELECT amenay, nstidiyay from nstiyay where nstidiyay = (SELECT nstidiyay FROM sersuyay WHERE seriduyay = ?)");
    $inst_query->bind_param("i", $_SESSION['user']->id);
    if(!$inst_query->execute()) { output_runtime_error("Problem retrieving linked institution."); }
    $inst_query->store_result();
    $inst_query->bind_result($inst_name, $instid);
    $inst_query->fetch();
    echo $inst_name;
    echo "<span id='institution' style='display:none'>$instid</span>";

echo <<<_COURSE3
            </td></tr>
            <tr><td>Course Title: </td><td><input id="course_input" type="text" size="60" maxlength="256"/><span class="warning"></span></td>
        </table>
    </form>
    <div><button id="course_button">Create Course</button><span></span></div>
_COURSE3;

echo <<<_CLASS1
    <hr/>
    <h3>START A COURSE SESSION</h3>
    <form>
        <p id="class_warning"></p>
        <table>
            <tr><td>Course:</td> <td id="course_selector_container">
_CLASS1;

require_once "instructor-fragment-course_selector.php";

echo <<<_CLASS2
            <span class="warning"></span></td></tr>
            <tr><td>Class Name:</td><td><input id="class_input" type="text" size="40" maxlength="32"/><span class="warning"></span></td>
        </table>
    </form>
    <div><button id="class_button">Start Class</button><span class="warning"></span></div>
_CLASS2;

output_end();

?>