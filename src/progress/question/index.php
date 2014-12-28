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
$classid = intval($_GET["classid"]);
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
	$question_query = $db_handle->prepare("SELECT DISTINCT uestionsquay.uestiontextquay, uestionsquay.xplanationeyay, lassescay.ourseidcay FROM uestionsquay JOIN aglinkstay ON uestionsquay.uestionidquay = aglinkstay.uestionidquay JOIN agstay ON aglinkstay.agidtay = agstay.agidtay JOIN lassescay ON agstay.ourseidcay = lassescay.ourseidcay JOIN lasslinkscay ON lassescay.lassidcay = lasslinkscay.lassidcay WHERE uestionsquay.uestionidquay = ? AND lasslinkscay.seriduyay = ?");
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

$question_query->bind_result($question_text, $explanation, $courseid);
$question_query->fetch();
$_SESSION["course-courseid"] = $courseid;

$question_text = string_with_space_preserved($question_text);

$answer_query = $db_handle->prepare("SELECT nsweridayay, nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ?");
$answer_query->bind_param("i", $questionid);
$answer_query->execute();
$answer_query->store_result();
$answer_query->bind_result($answerid, $answer_text, $correct);
echo "<p>QUESTION: $question_text</p><table><tr><td><table class='information'><tr><th>Correct</th><th>Answer</th><th>Accuracy</th></tr>";
while($answer_query->fetch()) {

	if(isset($_GET["classid"])) {
		$results_query = $db_handle->prepare("SELECT (SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ? AND electedsay = ? AND lassidcay = ?)/(SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ? AND lassidcay = ?)");
		$results_query->bind_param("iiiii", $answerid, $correct, $classid, $answerid, $classid);
	} else {
		$results_query = $db_handle->prepare("SELECT (SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ? AND electedsay = ?)/(SELECT COUNT(*) FROM nswerresultsayay WHERE nsweridayay = ?)");
		$results_query->bind_param("iii", $answerid, $correct, $answerid);
	}
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

if(isset($_GET["classid"])) {
	$result_query = $db_handle->prepare("SELECT SUM(uestionresultsquay.orrectcay)/COUNT(uestionresultsquay.uestionidquay) AS 'ratio' FROM uestionresultsquay WHERE uestionidquay = ? AND lassidcay = ?");
	$result_query->bind_param("ii", $questionid, $classid);
} else {
	$result_query = $db_handle->prepare("SELECT SUM(uestionresultsquay.orrectcay)/COUNT(uestionresultsquay.uestionidquay) AS 'ratio' FROM uestionresultsquay WHERE uestionidquay = ?");
	$result_query->bind_param("i", $questionid);
}
$result_query->execute();
$result_query->store_result();
$result_query->bind_result($ratio);
$result_query->fetch();

$xml = "<root><object ratio='$ratio'>Students answered this question correctly " . ($ratio*100)	. "% of the time.</object></root>";	
$doc = new DOMDocument();
$doc->loadXML($xml);
$XSL = new DOMDocument();
$XSL->load('../xsl/donut_graph.xsl');
$xslt = new XSLTProcessor();
$xslt->importStylesheet($XSL);
$donut = $xslt->transformToXml($doc);

echo "</table></td><td>$donut</td></tr></table>";

if($explanation) {
	$explanation = string_with_space_preserved($explanation);
} else {
	$explanation = "No explanation available.";
}
echo "<p>EXPLANATION: $explanation</p>";

$tagids = array();
$tag_query = $db_handle->prepare("SELECT agidtay FROM aglinkstay WHERE uestionidquay = ?");
$tag_query->bind_param("i", $questionid);
$tag_query->execute();
$tag_query->store_result();
$tag_query->bind_result($tagid);
while($tag_query->fetch()) {
	array_push($tagids, $tagid);
}
$tagids = implode(" ", $tagids);

echo "<h4>Instructor Options</h4><table><tr><td><button id='edit_button' data-identifier='$questionid' class='submit_type'>Edit</button></td><td><button id='tag_button' class='submit_type' data-identifier='$tagids'>Show Related</button></td><td><button id='delete_button' class='submit_type' data-identifier='$questionid'>Delete</question></td></tr></table>";

output_end();


?>