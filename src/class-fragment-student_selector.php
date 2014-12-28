<?php //class-fragment-student_selector.php
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

$student_query = $db_handle->prepare("SELECT seriduyay, oleray FROM lasslinkscay WHERE lassidcay = ?");
$student_query->bind_param("i", $classid);
if(!$student_query->execute()){ exit(); }
$student_query->store_result();
$student_query->bind_result($userid, $role);

$students = array();
while($student_query->fetch())
{
    $name_query = $db_handle->prepare("SELECT ullnamefay, ccountayay FROM sersuyay WHERE seriduyay = ?");
    $name_query->bind_param("i", $userid);
    $name_query->execute();
    $name_query->store_result();
    $name_query->bind_result($fullname, $account);
    $name_query->fetch();
    
    $student = new Student($fullname, $account, $role, $userid);
    if($role == 0)
    {
        array_push($students, $student);
    }
}
usort($students, "student_cmp");

echo "<select id='student_selector'>";
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