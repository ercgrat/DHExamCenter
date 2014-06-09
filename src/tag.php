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
else if(!isset($_GET["id"]) || !isset($_SESSION["course-courseid"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$tagid = $db_handle->real_escape_string($_GET["id"]);
if(!is_numeric($tagid))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$access_query = $db_handle->prepare("SELECT ourseidcay FROM agstay WHERE agidtay = ?");
$access_query->bind_param("i", $tagid);
$access_query->execute();
$access_query->store_result();
$access_query->bind_result($courseid);
$access_query->fetch();
if($courseid != $_SESSION["course-courseid"])
{
    $courseid = $_SESSION["course-courseid"];
    output_start("", $_SESSION["user"]);
    echo "<h2>No questions found.</h2>";
    echo "<p>There are no questions using this tag, so the tag no longer exists. <a href='course.php?id=$courseid'>Go back to the course page.</a></p>";
    output_end();
}

$course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$course_query->bind_param("i", $courseid);
$course_query->execute();
$course_query->store_result();
$course_query->bind_result($course_title);
$course_query->fetch();

$tag_query = $db_handle->prepare("SELECT agnametay FROM agstay WHERE agidtay = ?");
$tag_query->bind_param("i", $tagid);
$tag_query->execute();
$tag_query->store_result();
$tag_query->bind_result($tag_name);
$tag_query->fetch();

$header = "<link rel='stylesheet' type='text/css' href='tag.css'/>";
$header.= "<script type='text/javascript' src='tag.js'></script>";
output_start($header, $_SESSION["user"]);

echo <<<_HEAD
    <h2>Questions with Tag "$tag_name"</h2>
    <h3><a class="title" href="course.php?id=$courseid">$course_title</a></h3>
    <hr/>
    <h4>Select a Question to Edit</h4>
    <div id="review_question_container">
_HEAD;

$id_query = $db_handle->prepare("SELECT uestionidquay FROM aglinkstay WHERE agidtay = ?");
$id_query->bind_param("i", $_GET["id"]);
$id_query->execute();
$id_query->store_result();
$id_query->bind_result($questionid);

$fetch_count = 0;
while($id_query->fetch())
{
    $fetch_count++;
    if($fetch_count % 2 == 0) { echo "<div class='review_question highlighted hoverable' onclick='window.location=\"question.php?id=$questionid\"'>"; }
    else { echo "<div class='review_question hoverable' onclick='window.location=\"question.php?id=$questionid\"'>"; }
    echo "<input type='hidden' value='$questionid'/>";
    echo "<img class='delete' src='delete.png' alt='DELETE'/>";

    $question_query = $db_handle->prepare("SELECT uestiontextquay, rderoyay, esourceidray, xplanationeyay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text, $order, $resourceid, $explanation);
    $question_query->fetch();
    $question_text = string_with_space_preserved($question_text);
    echo "<p>QUESTION: $question_text</p>";
    
    $tags_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ?");
    $tags_query->bind_param("i", $questionid);
    $tags_query->execute();
    $tags_query->store_result();
    $tags_query->bind_result($qtagid);
    
    $num_tags = $tags_query->num_rows();
    $current_tag = 0;
    echo '<p class="details">TAGS: ';
    while($tags_query->fetch())
    {
        $current_tag++;
        $tagname_query = $db_handle->prepare("SELECT agnametay FROM agstay WHERE agidtay = ?");
        $tagname_query->bind_param("i", $qtagid);
        $tagname_query->execute();
        $tagname_query->store_result();
        $tagname_query->bind_result($qtagname);
        $tagname_query->fetch();
        echo "<a class='title3' href=\"tag.php?id=$qtagid\">$qtagname</a>";
        if($current_tag < $num_tags) { echo ", "; }
    }
    echo '</p>';
    
    $resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray WHERE esourceidray = ?");
    $resource_query->bind_param("i", $resourceid);
    $resource_query->execute();
    $resource_query->store_result();
    $resource_query->bind_result($resource_name, $resource_link);
    $resource_query->fetch();
    if(!isset($resource_name)) { $resource_name = "N/A"; }
    
    echo "<p class='details'>RESOURCE: <a href\"$resource_link\">$resource_name</a></p>";
    
    if($order) { $order = "ORDERED"; }
    else { $order = "UNORDERED"; }
    echo "<p class='details'>$order ANSWERS</p>";
    
    echo "<ul class='answer_list'>";
    $answer_query = $db_handle->prepare("SELECT nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ? ORDER BY nsweridayay");
    $answer_query->bind_param("i", $questionid);
    $answer_query->execute();
    $answer_query->store_result();
    $answer_query->bind_result($answer_text, $correct);
    while ($answer_query->fetch())
    {
        $answer_text = string_with_space_preserved($answer_text);
        if($correct == 1)
        {
            $img = "<img src='correct.png' alt='RIGHT: '/>";
        }
        else
        {
            $img = "<img src='incorrect.png' alt='WRONG: '/>";
        }
        echo "<li>$img $answer_text</li>";
    }
    echo "</ul>";
    
    $explanation = string_with_space_preserved($explanation);
    echo "<p>EXPLANATION: $explanation</p>";
    
    echo "</div>";
}

echo "</div>";
output_end();

?>