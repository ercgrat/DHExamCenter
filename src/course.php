<?php //course.php
require_once "authenticate.php";
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();
$_SESSION["page"] = "course";

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

$courseid = $db_handle->real_escape_string($_GET["id"]);

if($_SESSION['user']->role == 0)
{
    $link_query = $db_handle->prepare("SELECT lassescay.lassidcay FROM lassescay JOIN lasslinkscay ON lassescay.lassidcay = lasslinkscay.lassidcay WHERE lasslinkscay.seriduyay = ? AND lasslinkscay.oleray = 1 AND lassescay.ourseidcay = ? AND lassescay.nsessioniyay = 1");
    $link_query->bind_param("ii", $_SESSION["user"]->id, $courseid);
    $link_query->execute();
    $link_query->store_result();
    if($link_query->num_rows() < 1)
    {
        header("Location:https://". $_SERVER["HTTP_HOST"] ."/student.php");
    }
}

$header = '<script src="course.js"></script>';
$header.= '<script src="question.js"></script>';
$header.= '<script src="course-fragment-tags_table.js"></script>';
$header.= '<script src="course-fragment-tag_presentation_selector.js"></script>';
$header.= '<link rel="stylesheet" type="text/css" href="course.css"/>';
output_start($header, $_SESSION["user"]);

$root = $_SERVER["HTTP_HOST"];

$course_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$course_query->bind_param("i", $courseid);
$course_query->execute();
$course_query->store_result();
if($course_query->num_rows() != 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}
else
{
    $_SESSION["course-courseid"] = $courseid;
    $_SESSION["page"] = "course";

    $course_query->bind_result($course_title);
    $course_query->fetch();
}

echo <<<_BODY1
    <h2>$course_title</h2>
    <div class="expander">
        <div class="expander_head header"><h3>Add Question</h3><img class="toggle_down" src="https://$root/expander_down.png"/><img class="toggle_up" src="https://$root/expander_up.png"/></div>
        <div class="expander_content">
        <form id="question_form">
			<h4>New Question</h4>
            <p id="question_warning" class="warning"></p>
            <label>Question: <br/><p class="warning"></p><textarea id="question_textarea"></textarea></label>
            <label>Tags (comma delineated): <br/><p class="warning"></p><textarea id="tag_textarea"></textarea></label>
            <label>Resource Link: <div id="resource_selector">
_BODY1;

require_once "course-fragment-resource_selector.php";

echo <<<_BODY2
        </div></label><br/>
        <label>Preserve answer order? <input id="order_checkbox" type="checkbox"/></label>
        <p id="answer_warning" class="warning"></p>
        <div id="answer_list">
            <table class='form'>
                <tr>
                    <td><input type="checkbox" value="1"/></td>
                    <td>Answer 1: </td>
                    <td><textarea class="answer"></textarea></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="2"/></td>
                    <td>Answer 2: </td>
                    <td><textarea class="answer"></textarea></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="3"/></td>
                    <td>Answer 3: </td>
                    <td><textarea class="answer"></textarea></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="4"/></td>
                    <td>Answer 4: </td>
                    <td><textarea class="answer"></textarea></td>
                </tr>
            </table>
        </div>
        <img src="add.png" id="adder" alt="[ADD ANSWER]"/>
        <img src="minus.png" id="remover" alt="[REMOVE ANSWER]"/>
        <label>Explanation (seen after submission of answers): <br/><textarea id="explanation_textarea"></textarea></label>
    </form>
    <button id="question_button" class="submit_type">Add Question</button><span></span>
    </div></div>
    <h3>Add Resource</h3>
    <table class="form">
        <tr><td>Resource Name:</td><td><input id="resource_name" type="text" size="40" maxlength="256"/> <span class="warning"></span></td></tr>
        <tr><td>URL:</td><td><input id="resource_url" type="text" size="40" maxlength="512" value="http://"/> <span class="warning"></span></td></tr>
    </table>
    <button id="resource_button" class="submit_type">Add Resource</button> <span></span>
    <h3>Corpus</h3>
    <h4>Current Tags</h4>
    <p>Search through the question corpus by selecting any number of tags.</p>
    <div id="tag_presentation_selector">
_BODY2;

require_once "course-fragment-tag_presentation_selector.php";

echo '</div> <div id="tags_table">';

require_once "course-fragment-tags_table.php";
echo <<<_BODY3
	</div>
	<button id="tags_button" class="submit_type" data-courseid="$courseid">Search Questions</button>
_BODY3;

output_end();

?>