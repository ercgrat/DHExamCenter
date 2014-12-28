<?php //question-fragment-preview.php
require_once "authenticate.php";
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    exit();
}
else if(!isset($_SESSION["question-questionid"]) || !is_numeric($_SESSION["question-questionid"]))
{
    exit();
}

$questionid = $db_handle->real_escape_string($_SESSION["question-questionid"]);
$result = "<div>";

$question_query = $db_handle->prepare("SELECT uestiontextquay, rderoyay, esourceidray, xplanationeyay FROM uestionsquay WHERE uestionidquay = ?");
$question_query->bind_param("i", $questionid);
$question_query->execute();
$question_query->store_result();
$question_query->bind_result($question_text, $order, $resourceid, $explanation);
$question_query->fetch();
$question_text = string_with_space_preserved($question_text);
$result .= "<p>QUESTION <a href=\"progress/question/?id=$questionid\">(results)</a>: $question_text</p>";

$tags_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ?");
$tags_query->bind_param("i", $questionid);
$tags_query->execute();
$tags_query->store_result();
$tags_query->bind_result($qtagid);

$num_tags = $tags_query->num_rows();
$current_tag = 0;
$result .= '<p class="details">TAGS: ';
while($tags_query->fetch())
{
	$current_tag++;
	$tagname_query = $db_handle->prepare("SELECT agnametay FROM agstay WHERE agidtay = ?");
	$tagname_query->bind_param("i", $qtagid);
	$tagname_query->execute();
	$tagname_query->store_result();
	$tagname_query->bind_result($qtagname);
	$tagname_query->fetch();
	$result .= "<a class='title3' href=\"tag.php?ids[]=$qtagid\">$qtagname</a>";
	if($current_tag < $num_tags) { $result .= ", "; }
}
$result .= '</p>';

$resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray WHERE esourceidray = ?");
$resource_query->bind_param("i", $resourceid);
$resource_query->execute();
$resource_query->store_result();
$resource_query->bind_result($resource_name, $resource_link);
$resource_query->fetch();
if(!isset($resource_name)) { $resource_name = "N/A"; }

$result .= "<p class='details'>RESOURCE: <a href\"$resource_link\">$resource_name</a></p>";

if($order) { $order = "ORDERED"; }
else { $order = "UNORDERED"; }
$result .= "<p class='details'>$order ANSWERS</p>";

$result .= "<ul class='answer_list'>";
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
	$result .= "<li>$img $answer_text</li>";
}
$result .= "</ul>";

$explanation = string_with_space_preserved($explanation);
$result .= "<p>EXPLANATION: $explanation</p>";
$result .= "</div>";

echo $result;

?>