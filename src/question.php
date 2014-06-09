<?php //question.php
require_once "authenticate.php";
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();
$_SESSION["page"] = "question";

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if(!isset($_GET["id"]) || !is_numeric($_GET["id"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$questionid = $db_handle->real_escape_string($_GET["id"]);
$courseid = $_SESSION["course-courseid"];

$question_query = $db_handle->prepare("SELECT oursescay.ourseidcay FROM oursescay JOIN agstay ON agstay.ourseidcay = oursescay.ourseidcay JOIN aglinkstay ON agstay.agidtay = aglinkstay.agidtay WHERE aglinkstay.uestionidquay = ?");
$question_query->bind_param("i", $questionid);
$question_query->execute();
$question_query->store_result();
$question_query->bind_result($q_courseid);
$question_query->fetch();
if($q_courseid != $courseid) {
	header("Location:https://". $_SERVER["HTTP_HOST"] ."/student.php");
}

if($_SESSION['user']->role == 0)
{
    $link_query = $db_handle->prepare("SELECT * FROM lasslinkscay JOIN lassescay ON lasslinkscay.lassidcay = lassescay.lassidcay JOIN oursescay ON oursescay.ourseidcay = lassescay.ourseidcay WHERE lasslinkscay.seriduyay = ? AND oursescay.ourseidcay = ? AND lassescay.nsessioniyay = 1");
    $link_query->bind_param("ii", $_SESSION["user"]->id, $courseid);
    $link_query->execute();
    $link_query->store_result();
    if($link_query->num_rows() < 1)
    {
        header("Location:https://". $_SERVER["HTTP_HOST"] ."/student.php");
    }
}

$header = '<script type="text/javascript" src="question.js"></script>';
$header .= '<link rel="stylesheet" type="text/css" href="course.css"/>';
output_start($header, $_SESSION["user"]);

$course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$course_query->bind_param("i", $courseid);
$course_query->execute();
$course_query->store_result();
$course_query->bind_result($course_title);
$course_query->fetch();

$question_query = $db_handle->prepare("SELECT rderoyay, uestiontextquay, esourceidray, xplanationeyay FROM uestionsquay WHERE uestionidquay = ?");
$question_query->bind_param("i", $questionid);
$question_query->execute();
$question_query->store_result();
$question_query->bind_result($order, $question_text, $resourceid, $explanation);
$question_query->fetch();
$_SESSION["question-questionid"] = $questionid;
$_SESSION["question-resourceid"] = $resourceid;

echo <<<_BODY
    <h2><a class="title2" href="course.php?id=$courseid">$course_title</a></h2>
    <h3>Edit Question</h3>
    <form id="question_form">
        <p id="question_warning" class="warning"></p>
        <label>Question: <br/><p class="warning"></p><textarea id="question_textarea">$question_text</textarea></label>
        <label>Tags (comma delineated): <br/><p class="warning"></p><textarea id="tag_textarea">
_BODY;

$tag_query = $db_handle->prepare("SELECT agnametay FROM agstay AS tags WHERE EXISTS (SELECT * FROM aglinkstay WHERE uestionidquay = ? AND agidtay = tags.agidtay)");
$tag_query->bind_param("i", $questionid);
$tag_query->execute();
$tag_query->store_result();
$tag_query->bind_result($tag_name);
$tag_count = $tag_query->num_rows();
$tag_query->fetch();
echo $tag_name;
while($tag_query->fetch())
{
    echo ", ".$tag_name;
}

echo <<<_BODY
        </textarea></label>
        <label>Resource Link: <div id="resource_selector">
_BODY;

require_once "course-fragment-resource_selector.php";

echo <<<_BODY
        </div></label><br/>
        <label>Preserve answer order? 
_BODY;

if($order) { $checked = "checked"; }
else { $checked = ""; }
echo "<input id='order_checkbox' type='checkbox' $checked/>";

echo <<<_BODY
        </label>
        <p id="answer_warning" class="warning"></p>
        <div id="answer_list">
            <table class='form'>
_BODY;

$answer_query = $db_handle->prepare("SELECT nsweridayay, nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ? ORDER BY nsweridayay");
$answer_query->bind_param("i", $questionid);
$answer_query->execute();
$answer_query->store_result();
$answer_query->bind_result($answerid, $answer_text, $correct);
$answer_number = 0;
while($answer_query->fetch())
{
    $answer_number++;
    if($correct) { $checked = "checked"; }
    else { $checked = ""; }
    $answer_text = htmlentities($answer_text);
    echo <<<_ANSWER
        <tr>
            <td><input type="checkbox" value="$correct" $checked/></td>
            <td>Answer $answer_number: </td>
            <td><textarea class="answer">$answer_text</textarea></td>
        </tr>
_ANSWER;
}

echo <<<_BODY
            </table>
        </div>
        <img src="add.png" id="adder" alt="[ADD ANSWER]"/>
        <img src="minus.png" id="remover" alt="[REMOVE ANSWER]"/>
        <label>Explanation (seen after submission of answers): <br/><textarea id="explanation_textarea">$explanation</textarea></label>
    </form>
    <p><button id="question_button">Save Question</button> <span></span></p>
_BODY;

output_end();

?>