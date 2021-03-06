<?php //class-fragment-student_table.php
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

if($student_query->num_rows() == 0)
{
    echo "<p>There are no current students.</p>";
}

$student_query->bind_result($userid, $role);
$students = array();
while($student_query->fetch())
{
    $info_query = $db_handle->prepare("SELECT ullnamefay, ccountayay FROM sersuyay WHERE seriduyay = ?");
    $info_query->bind_param("i", $userid);
    if(!$info_query->execute()) { exit(); }
    $info_query->store_result();
    $info_query->bind_result($fullname, $account);
    $info_query->fetch();
    
    if($role > 1) { continue; }
    $student = new Student();
    $student->role = $role;
    $student->userid = $userid;
    $student->name = $fullname;
    $student->account = $account;
    array_push($students, $student);
}
usort($students, "student_cmp");

echo "<ul class='horizontal_list'>";
for($student_index = 0; $student_index < count($students); $student_index++)
{
    $student = $students[$student_index];
    $account = $student->account;
    $name = $student->name;
    if($student->role == 0) { echo "<li class='horizontal_item'>$name - $account</li>"; }
    else { echo "<li class='horizontal_item ta'>$name - $account</li>"; }
}
echo "</ul>";

?>