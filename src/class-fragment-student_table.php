<?php //class-fragment-student_table.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1 || !isset($_SESSION["class-classid"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$classid = $_SESSION["class-classid"];

class Student
{
    public $name = "";
    public $account = "";
    public $role = 0;
    public $userid = 0;
}

function student_cmp ($student1, $student2)
{
    if($student1->role != $student2->role)
    {
        return ($student1->role > $student2->role) ? -1 : 1;
    }
    return strcmp($student1->name, $student2->name);
}

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

echo "<table>";
for($student_index = 0; $student_index < count($students); $student_index++)
{
    if($student_index % 4 == 0) { echo "<tr>"; }
    
    $student = $students[$student_index];
    $account = $student->account;
    $name = $student->name;
    if($student->role == 0) { echo "<td>$name - $account</td>"; }
    else { echo "<td class='ta'>$name - $account</td>"; }
    
    if ($student_index % 4 == 3) { echo "</tr>"; }
}
if($student_index % 4 < 3) { echo "</tr>"; }
echo "</table>";

?>