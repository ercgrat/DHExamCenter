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
if(!isset($_GET["id"]) || !is_numeric($_GET["id"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}

$classid = $db_handle->real_escape_string($_GET["id"]);
if($_SESSION["user"]->role != 2) {
	$admin_query = $db_handle->prepare("SELECT * FROM lasslinkscay WHERE seriduyay = ? AND lassidcay = ? AND oleray > 0");
	$admin_query->bind_param("ii", $_SESSION["user"]->id, $classid);
	$admin_query->execute();
	$admin_query->store_result();
	if($admin_query->num_rows() != 1)
	{
		header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
		exit();
	}
}

$session_query = $db_handle->prepare("SELECT nsessioniyay FROM lassescay WHERE lassidcay = ?");
$session_query->bind_param("i", $classid);
$session_query->execute();
$session_query->store_result();
$session_query->bind_result($insession);
$session_query->fetch();
if($insession != 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"]."/instructor.php");
    exit();
}

$header = '<script type="text/javascript" src="class.js"></script>';
$header.= '<link rel="stylesheet" type="text/css" href="class.css"/>';
output_start($header, $_SESSION["user"]);

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

$root = $_SERVER["HTTP_HOST"];

echo <<<_HEAD
    <h2>$class_title</h2>
    <h3><a class="title" href="course.php?id=$courseid">$course_title</a></h3>
    <div class='expander content_section'>
        <div class='expander_head subheader'><h4>Current Students</h4><img class='toggle_up' src='https://$root/expander_up.png' ALT='MINIMIZE'/><img class='toggle_down' src='https://$root/expander_down.png' ALT='EXPAND'/></div>
        <div class='expander_content'>
            <div id='student_table'>
_HEAD;

require_once "class-fragment-student_table.php";

echo "</div></div></div> <div class='expander content_section'><div class='expander_head subheader'><h4>Pending Students</h4><img class='toggle_up' src='https://$root/expander_up.png' ALT='MINIMIZE'/><img class='toggle_down' src='https://$root/expander_down.png' ALT='EXPAND'/></div> <div class='expander_content'><div id='pending_student_table'>";

require_once "class-fragment-pending_student_table.php";

echo "</div></div></div><div class='content_section'><h4>Add a Registered Student</h4>";

echo <<<_STUDENT
    <form id="registered_student_form">
        <table>
            <tr>
                <td>Student: </td>
                <td>
_STUDENT;
require_once "class-fragment-registered_student_selector.php";

echo <<<_STUDENT
                <span class="warning"></span></td>
            </tr>
        </table>
    </form>
    <button id="registered_student_button">Add Student</button><span></span>
	</div>
_STUDENT;

echo <<<_STUDENT
	<div class='content_section'>
    <h4>Invite a Student</h4>
    <form id="student_form">
        <table>
            <tr><td>Student Account Name: </td> <td><input id="student_account_name" name="account" type="text" size="40" maxlength="32"/><span class="warning"></span><span class="note"> (Used for identification during student account registration.)</span></td></tr>
        </table>
    </form>
    <div>
        <button id="student_button">Invite Student</button>
        <span></span>
    </div>
	</div>
_STUDENT;

echo <<<_TA1
	<div class='content_section'>
    <h4>Appoint a Teaching Assistant</h4>
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
	</div>
_TA2;

echo <<<_DISABLE
	<div class='content_section'>
    <h4>End Class Session</h4>
    <p>Terminating the class session will prevent all students and teaching assistants from accessing the course content via this portal.
    Students who wish to continue using the materials must be added to an active class.</p>
    <button id="end_button">End Class</button><span class="note">The class can always be restarted from the course page.</span>
	</div>
_DISABLE;

output_end();

?>