<?php //index.php (progress/question)
require_once $_SERVER["DOCUMENT_ROOT"]."/layout.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in) {
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

$questionid = intval($_GET["id"]);
if($questionid == 0) {
	header("Location: https://". $_SERVER["HTTP_HOST"] . "/progress/");
	exit();
}

if($_SESSION["user"]->role == 2) {
	$question_query = $db_handle->prepare("SELECT DISTINCT uestionsquay.uestiontextquay, uestionsquay.xplanationeyay, oursescay.ourseidcay FROM uestionsquay JOIN aglinkstay ON uestionsquay.uestionidquay = aglinkstay.uestionidquay JOIN agstay ON aglinkstay.agidtay = agstay.agidtay JOIN oursescay ON agstay.ourseidcay = oursescay.ourseidcay WHERE uestionsquay.uestionidquay = ?");
	$question_query->bind_param("i", $questionid);
} else if ($_SESSION["user"]->role == 1) {
	$question_query = $db_handle->prepare("SELECT DISTINCT uestionsquay.uestiontextquay, uestionsquay.xplanationeyay, oursescay.ourseidcay FROM uestionsquay JOIN aglinkstay ON uestionsquay.uestionidquay = aglinkstay.uestionidquay JOIN agstay ON aglinkstay.agidtay = agstay.agidtay JOIN oursescay ON agstay.ourseidcay = oursescay.ourseidcay WHERE uestionsquay.uestionidquay = ? AND oursescay.seriduyay = ?");
	$question_query->bind_param("ii", $questionid, $_SESSION["user"]->id);
} else { // role == 0
	$question_query = $db_handle->prepare("SELECT DISTINCT uestionsquay.uestiontextquay, uestionsquay.xplanationeyay FROM uestionsquay JOIN aglinkstay ON uestionsquay.uestionidquay = aglinkstay.uestionidquay JOIN agstay ON aglinkstay.agidtay = agstay.agidtay JOIN lassescay ON agstay.ourseidcay = lassescay.ourseidcay JOIN lasslinkscay ON lassescay.lassidcay = lasslinkscay.lassidcay WHERE uestionsquay.uestionidquay = ? AND lasslinkscay.seriduyay = ?");
	$question_query->bind_param("ii", $questionid, $_SESSION["user"]->id);
}
$question_query->execute();
$question_query->store_result();
if($question_query->num_rows() != 1) {
	header("Location: https://". $_SERVER["HTTP_HOST"] . "/progress/");
	exit();
}

$header = "<link rel='stylesheet' type='text/css' href='progress-question.css'></link>";
$header.= "<script type='text/javascript'>var _progressQuestionEditUrl = \"". $_SERVER["HTTP_HOST"] ."/question.php\";\n";
$header.= "var _progressQuestionDeleteUrl = \"". $_SERVER["HTTP_HOST"] ."/tag-delete_question.php\";\n";
$header.= "var _progressQuestionTagUrl = \"". $_SERVER["HTTP_HOST"] ."/tag.php\";</script>";
$header.= "<script type='text/javascript' src='progress-question.js'></script>";
output_start($header, $_SESSION['user']);
echo "<h2><a class='title2' href='https://".$_SERVER["HTTP_HOST"]."/progress.php'>Progress</a></h2>";

if($_SESSION["user"]->role > 0) {
	$question_query->bind_result($question_text, $explanation, $courseid);
	$_SESSION["course-courseid"] = $courseid;
} else {
	$question_query->bind_result($question_text, $explanation);
}
$question_query->fetch();

$question_text = string_with_space_preserved($question_text);
echo "<p>QUESTION: $question_text</p>";

$answer_query = $db_handle->prepare("SELECT nsweridayay, nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ?");
$answer_query->bind_param("i", $questionid);
$answer_query->execute();
$answer_query->store_result();
$answer_query->bind_result($answerid, $answer_text, $correct);
echo "<p>ANSWERS:</p><table class='information'><tr><th>Correct</th><th>Answer</th><th>Accuracy</th></tr>";
while($answer_query->fetch()) {

	$results_query = $db_handle->prepare("SELECT (SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ? AND electedsay = ?)/(SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ?)");
	$results_query->bind_param("iii", $answerid, $correct, $answerid);
	$results_query->execute();
	$results_query->store_result();
	$results_query->bind_result($ratio);
	$results_query->fetch();

	$xml = "<root><object ratio='$ratio'>" . ($ratio*100)	. "% of students selected the correct value for this answer.</object></root>";
	
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	$XSL = new DOMDocument();
	$XSL->load('../xsl/donut_graph.xsl');
	$xslt = new XSLTProcessor();
	$xslt->importStylesheet($XSL);
	
	$donut = $xslt->transformToXml($doc);
	$answer_text = string_with_space_preserved($answer_text);
	$root = "https://" . $_SERVER["HTTP_HOST"];
	if($correct == 1) {
		$correctness_indicator = "<img class='correction_indicator' src='$root/correct.png' alt='CORRECT: '/>";
	} else {
		$correctness_indicator = "<img class='correction_indicator' src='$root/incorrect.png' alt='CORRECT: '/>";
	}
	
	echo "<tr><td>$correctness_indicator</td></td><td>$answer_text</td><td>$donut</td></tr>";
}
echo "</table>";

if($explanation) {
	$explanation = string_with_space_preserved($explanation);
} else {
	$explanation = "No explanation available.";
}
echo "<p>EXPLANATION: $explanation</p>";

$tag_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ? LIMIT 1");
$tag_query->bind_param("i", $questionid);
$tag_query->execute();
$tag_query->store_result();
$tag_query->bind_result($tagid);
$tag_query->fetch();

if($_SESSION["user"]->role > 0) {
	echo "<h4>Instructor Options</h4><table><tr><td><button id='edit_button' data-identifier='$questionid' class='submit_type'>Edit</button></td><td><button id='tag_button' class='submit_type' data-identifier='$tagid'>Show Related</button></td><td><button id='delete_button' class='submit_type' data-identifier='$questionid'>Delete</question></td></tr></table>";
}

output_end();


?>