<?php //tag-delete_question.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || !isset($_GET["id"]) || !isset($_SESSION["course-courseid"]) || !is_numeric($_GET["id"]))
{
    exit();
}

$questionid = $_GET["id"];

$tag_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ? LIMIT 1");
$tag_query->bind_param("i", $questionid);
$tag_query->execute();
$tag_query->store_result();
if($tag_query->num_rows() != 1) { exit(); }
$tag_query->bind_result($tagid);
$tag_query->fetch();

$course_query = $db_handle->prepare("SELECT ourseidcay FROM agstay WHERE agidtay = ?");
$course_query->bind_param("i", $tagid);
$course_query->execute();
$course_query->store_result();
$course_query->bind_result($courseid);
$course_query->fetch();

if($courseid != $_SESSION["course-courseid"]) { exit(); }

$question_query = $db_handle->prepare("DELETE FROM uestionsquay WHERE uestionidquay = ?");
$question_query->bind_param("i", $questionid);
$question_query->execute();

$answer_query = $db_handle->prepare("DELETE FROM nswersayay WHERE uestionidquay = ?");
$answer_query->bind_param("i", $questionid);
$answer_query->execute();

$tag_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ?");
$tag_query->bind_param("i", $questionid);
$tag_query->execute();
$tag_query->store_result();
$tag_query->bind_result($tagid);
$tagids = array();
while($tag_query->fetch())
{
    array_push($tagids, $tagid);
}

$taglink_query = $db_handle->prepare("DELETE FROM aglinkstay WHERE uestionidquay = ?");
$taglink_query->bind_param("i", $questionid);
$taglink_query->execute();

foreach($tagids as $tagid)
{
    $remains_query = $db_handle->prepare("SELECT * FROM aglinkstay WHERE agidtay = ?");
    $remains_query->bind_param("i", $tagid);
    $remains_query->execute();
    $remains_query->store_result();
    if($remains_query->num_rows() == 0)
    {
        $tagremove_query = $db_handle->prepare("DELETE FROM agstay WHERE agidtay = ?");
        $tagremove_query->bind_param("i", $tagid);
        $tagremove_query->execute();
    }
}

?>