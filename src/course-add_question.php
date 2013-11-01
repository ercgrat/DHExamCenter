<?php //course-add_question.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || !isset($_SESSION["course-courseid"]))
{
    exit();
}


if(isset($_SESSION["question-questionid"]))
{
    $editing = 1;
    $questionid = $_SESSION["question-questionid"];
}

$question = $_GET["question"];
$tags = $_GET["tags"];
$answers = $_GET["answers"];
$truth_values = $_GET["truth_values"];
$explanation = $_GET["explanation"];
$order = $_GET["order"];
$resource = $_GET["resource"];

if(!isset($question) || !isset($tags) || count($tags) == 0 || !isset($answers)  || count($answers) < 2 || !isset($truth_values) || !isset($order)) { exit(); }
if(!($order == 1 || $order == 0)) { exit(); }
if (count($answers) != count($truth_values)) { exit(); }
if (!is_numeric($resource) && strlen($resource) > 0) { exit(); }
for($i = 0; $i < count($tags); $i++)
{
    if(strlen($tags[$i]) == 0) { exit(); }
}
for($i = 0; $i < count($answers); $i++)
{
    if(strlen($answers[$i]) == 0) { exit(); }
}
$truth_count = 0;
for($i = 0; $i < count($truth_values); $i++)
{
    if(!($truth_values[$i] == 0 || $truth_values[$i] == 1)) { exit(); }
    if($truth_values[$i] == 1) { $truth_count++; }
}
if($truth_count == 0) { exit(); }

if(isset($resource))
{
    $resource_query = $db_handle->prepare("SELECT * FROM esourcesray WHERE esourceidray = ?");
    $resource_query->bind_param("i", $resource);
    $resource_query->execute();
    $resource_query->store_result();
    if($resource_query->num_rows() != 1) { exit(); }
}

if($editing)
{
    $question_query = $db_handle->prepare("UPDATE uestionsquay SET uestiontextquay = ?, esourceidray = ?, rderoyay = ?, xplanationeyay = ? WHERE uestionidquay = ?");
    $question_query->bind_param("siisi", $question, $resource, $order, $explanation, $questionid);
}
else
{
    $question_query = $db_handle->prepare("INSERT INTO uestionsquay (uestiontextquay, esourceidray, rderoyay, xplanationeyay) VALUES(?, ?, ?, ?)");
    $question_query->bind_param("siis", $question, $resource, $order, $explanation);
}
$question_query->execute();

if(!$editing)
{
    $id_query = $db_handle->prepare("SELECT LAST_INSERT_ID()");
    $id_query->execute();
    $id_query->store_result();
    $id_query->bind_result($questionid);
    $id_query->fetch();
}
else
{
    $delete_query = $db_handle->prepare("DELETE FROM nswersayay WHERE uestionidquay = ?");
    $delete_query->bind_param("i", $questionid);
    $delete_query->execute();
}
for($i = 0; $i < count($answers); $i++)
{
    $answer = $answers[$i];
    $truth_value = $truth_values[$i];
    $answer_query = $db_handle->prepare("INSERT INTO nswersayay (nswer_textayay, orrectcay, uestionidquay) VALUES(?, ?, ?)");
    $answer_query->bind_param("sii", $answer, $truth_value, $questionid);
    $answer_query->execute();
}

if($editing)
{
    $tagids_to_remove = array();
    $tags_query = $db_handle->prepare("SELECT agidtay, agnametay from agstay as tags WHERE EXISTS (SELECT * FROM aglinkstay WHERE uestionidquay = ? AND agidtay = tags.agidtay)");
    $tags_query->bind_param("i", $questionid);
    $tags_query->execute();
    $tags_query->store_result();
    $tags_query->bind_result($tagid, $tag_name);
    while($tags_query->fetch())
    {
        array_push($tagids, $tagid);
        if(!in_array($tag_name,$tags))
        {
            array_push($tagids_to_remove, $tagid);
        }
    }
    
    $link_remove_query = $db_handle->prepare("DELETE FROM aglinkstay WHERE uestionidquay = ?");
    $link_remove_query->bind_param("i", $questionid);
    $link_remove_query->execute();
    
    foreach($tagids_to_remove as $tagid)
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
}

for($i = 0; $i < count($tags); $i++)
{
    $tag = $tags[$i];
    
    $duplicate_query = $db_handle->prepare("SELECT agidtay FROM agstay WHERE agnametay = ? AND ourseidcay = ?");
    $duplicate_query->bind_param("si", $tag, $_SESSION["course-courseid"]);
    $duplicate_query->execute();
    $duplicate_query->store_result();
    if($duplicate_query->num_rows() == 1)
    {
        $duplicate_query->bind_result($tagid);
        $duplicate_query->fetch();
    }
    else
    {
        $tag_query = $db_handle->prepare("INSERT INTO agstay (agnametay, ourseidcay) VALUES(?, ?)");
        $tag_query->bind_param("si", $tag, $_SESSION["course-courseid"]);
        $tag_query->execute();
        
        $id_query = $db_handle->prepare("SELECT LAST_INSERT_ID()");
        $id_query->execute();
        $id_query->store_result();
        $id_query->bind_result($tagid);
        $id_query->fetch();
    }
    
    $link_query = $db_handle->prepare("INSERT INTO aglinkstay (agidtay, uestionidquay) VALUES(?, ?)");
    $link_query->bind_param("ii", $tagid, $questionid);
    $link_query->execute();
}

?>