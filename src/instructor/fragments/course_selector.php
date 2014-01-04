<?php //instructor-fragment-course_selector.php

require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/layout.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1)
{
    exit();
}

echo "<select id='course_selector'>";

$course_query = $db_handle->prepare("SELECT oursetitlecay, ourseidcay FROM oursescay WHERE seriduyay = ? GROUP BY oursetitlecay");
$course_query->bind_param("i", $_SESSION['user']->id);
if(!$course_query->execute()) { output_runtime_error("Error retrieving course list."); }
$course_query->store_result();
$course_query->bind_result($course, $courseid);
while($course_query->fetch())
{
    echo "<option value='$courseid'>$course</option>";
}

echo "</select>";

?>