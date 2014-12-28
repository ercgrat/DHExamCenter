<?php //class-fragment-registered_student_selector.php
require_once "authenticate.php";
require_once "login.php";
require_once "core-student.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || !isset($_SESSION["class-classid"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$classid = $_SESSION["class-classid"];

$student_query = $db_handle->prepare("SELECT seriduyay, ullnamefay, ccountayay FROM sersuyay AS users WHERE NOT EXISTS (SELECT lassidcay, seriduyay FROM lasslinkscay WHERE seriduyay = users.seriduyay AND lassidcay = ?) AND oleray = 0 AND nstidiyay = ?");
$student_query->bind_param("ii", $classid, $_SESSION["user"]->inst);
if(!$student_query->execute()){ exit(); }
$student_query->store_result();
$student_query->bind_result($userid, $name, $account);

$students = array();
while($student_query->fetch())
{   
    $student = new Student($name, $account, 0, $userid);
    array_push($students, $student);
}
usort($students, "student_cmp");

echo "<select id='registered_student_selector'>";
echo "<option value=''>Select a student</option>";
foreach($students as $student)
{
    $userid = $student->userid;
    $name = $student->name;
    $account = $student->account;
    echo "<option value='$userid'>$name - $account</option>";
}
echo "</select>";

?>