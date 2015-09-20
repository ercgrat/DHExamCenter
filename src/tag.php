<?php //tag.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if((!isset($_POST["tagids"]) && !isset($_GET["ids"])) || !isset($_SESSION["course-courseid"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

if(isset($_POST["tagids"])) {
	$tagids = explode(',', $_POST["tagids"]);
} else {
	$tagids = $_GET["ids"];
}
$tag_names = array();
if(count($tagids) > 1 || (count($tagids) == 1 && $tagids[0] != "")) {
	for($i = 0; $i < count($tagids); $i++) {
		if(!is_numeric($tagids[$i]))
		{
			header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
			exit();
		}
		
		$access_query = $db_handle->prepare("SELECT ourseidcay, agnametay FROM agstay WHERE agidtay = ?");
		$access_query->bind_param("i", $tagids[$i]);
		$access_query->execute();
		$access_query->store_result();
		$access_query->bind_result($courseid, $tag_name);
		$access_query->fetch();
		if($courseid != $_SESSION["course-courseid"] && $_SESSION["user"]->role != 2)
		{
			$courseid = $_SESSION["course-courseid"];
			output_start("", $_SESSION["user"]);
			echo "<h2>No questions found.</h2>";
			echo "<p>There are no questions using this tag, so the tag no longer exists. <a href='course.php?id=$courseid'>Go back to the course page.</a></p>";
			output_end();
		}
		
		$tag_names[] = $tag_name;
	}
}

$courseid = $_SESSION["course-courseid"];
$course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$course_query->bind_param("i", $courseid);
$course_query->execute();
$course_query->store_result();
$course_query->bind_result($course_title);
$course_query->fetch();

$header = "<link rel='stylesheet' type='text/css' href='tag.css'/>";
$header.= "<script type='text/javascript' src='tag.js'></script>";
output_start($header, $_SESSION["user"]);

$tag_name_string = implode(", ", $tag_names);
echo <<<_HEAD
    <h2>Questions with Tags: "$tag_name_string"</h2>
    <h3><a class="title" href="course.php?id=$courseid">$course_title</a></h3>
    <h4>Select a Question to Edit</h4>
    <div id="review_question_container">
_HEAD;

if(count($tagids) > 1 || (count($tagids) == 1 && $tagids[0] != "")) {
	$tagid_query_string = "WHERE agidtay = ?";
	for($i = 1; $i < count($tagids); $i++) {
		$tagid_query_string .= " OR agidtay = ?";
	}
}

$id_query = $db_handle_pdo->prepare("SELECT uestionidquay FROM aglinkstay $tagid_query_string");
for($i = 1; $i <= count($tagids); $i++) {
	$id_query->bindValue($i, $tagids[$i - 1]);
}
$id_query->execute();
$id_query->bindColumn(1, $questionid);

$fetch_count = 0;
while($id_query->fetch())
{
    $fetch_count++;
    if($fetch_count % 2 == 0) { echo "<div class='review_question highlighted hoverable' onclick='window.location=\"question.php?id=$questionid\"'>"; }
    else { echo "<div class='review_question hoverable' onclick='window.location=\"question.php?id=$questionid\"'>"; }
    echo "<input type='hidden' value='$questionid'/>";
    echo "<img class='delete' src='delete.png' alt='DELETE'/>";

    $_SESSION["question-questionid"] = $questionid;
	include "question-fragment-preview.php";
    
    echo "</div>";
}

echo "</div>";
output_end();
?>