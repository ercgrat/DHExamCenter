<?php //progress-fragments-question_frequency.php

require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || !isset($_POST["progress-question_frequency-classid"]))
{
    exit();
}
$classid = $_POST["progress-question_frequency-classid"];

$xml = "<root>";
$best_query = $db_handle->prepare("SELECT uestionresultsquay.uestionidquay, SUM(uestionresultsquay.orrectcay)/COUNT(uestionresultsquay.uestionidquay) AS 'ratio' FROM uestionresultsquay WHERE lassidcay = ? GROUP BY uestionidquay ORDER BY ratio DESC LIMIT 10");
$best_query->bind_param("i", $classid);
$best_query->execute();
$best_query->store_result();
$best_query->bind_result($questionid, $ratio);
while($best_query->fetch())
{
    $question_query = $db_handle->prepare("SELECT uestiontextquay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text);
    $question_query->fetch();
	$question_text = htmlentities($question_text);
    $xml .= "<object ratio='$ratio' identifier='$questionid'>$question_text</object>";
}

$xml .= "<object fake='1'></object><object fake='1'></object><object fake='1'></object>";

$worst_query = $db_handle->prepare("SELECT uestionresultsquay.uestionidquay, SUM(uestionresultsquay.orrectcay)/COUNT(uestionresultsquay.uestionidquay) AS 'ratio' FROM uestionresultsquay WHERE lassidcay = ? GROUP BY uestionidquay ORDER BY ratio ASC LIMIT 10");
$worst_query->bind_param("i", $classid);
$worst_query->execute();
$worst_query->store_result();
$worst_query->bind_result($questionid, $ratio);
$worst_questions = "";
while($worst_query->fetch())
{
    $question_query = $db_handle->prepare("SELECT uestiontextquay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text);
    $question_query->fetch();
	$question_text = htmlentities($question_text);
    $worst_questions = "<object ratio='$ratio' identifier='$questionid'>$question_text</object>".$worst_questions;
}

$xml .= $worst_questions;
$xml .= "</root>";

$doc = new DOMDocument();
$doc->loadXML($xml);

$XSL = new DOMDocument();
$XSL->load('xsl/gradient_bar_graph.xsl');

$xslt = new XSLTProcessor();
$xslt->importStylesheet($XSL);

echo "<div data-identifier='$classid'>".$xslt->transformToXml($doc)."</div>";
?>