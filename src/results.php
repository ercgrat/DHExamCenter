<?php //results.php
require_once 'login.php';
require_once 'authenticate.php';
require_once 'layout.php';

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
if(!isset($_SESSION['exam-courseid']) || !isset($_SESSION['exam-questions']) || !isset($_SESSION['exam-answers']))
{
    header("Location: https://". $_SERVER["HTTP_HOST"]."/student.php");
    exit();
}
$user = $_SESSION['user'];
$courseid = $_SESSION['exam-courseid'];
$classid = $_SESSION['exam-classid'];

$title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$title_query->bind_param("i", $courseid);
$title_query->execute();
$title_query->store_result();
if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Course could not be identified. <a href='student.php'>Go back</a>"); }
$title_query->bind_result($course_title);
$title_query->fetch();

$header = "<link rel='stylesheet' type='text/css' href='results.css'/>";
output_start($header, $_SESSION['user']);

$XSL = new DOMDocument();
$XSL->load('progress/xsl/donut_graph.xsl');
$xslt = new XSLTProcessor();
$xslt->importStylesheet($XSL);

echo "<h1>$title</h1>";

$evaluation_string = "";
$questions_arr = $_SESSION['exam-questions'];
$num_questions = count($questions_arr);
$correct_count = 0;
$date = date('Y-m-d H:i:s');
for($i = 0; $i < $num_questions; $i++)
{
    $questionid = $questions_arr[$i];
    $question_query = $db_handle->prepare("SELECT uestiontextquay, xplanationeyay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text, $explanation);
    $question_query->fetch();
    $question_text = string_with_space_preserved($question_text);
    $explanation = string_with_space_preserved($explanation);
    
    $question_string = "";
    $correct_tally = 1;
    $question_no = $i + 1;
    
    $current_answers = $_SESSION['exam-answers'][$i];
    $num_answers = count($current_answers);
    for($j = 0; $j < $num_answers; $j++)
    {
        $answerid = $current_answers[$j][0];
        $answer_text = $current_answers[$j][1];
        $answer_query = $db_handle->prepare("SELECT orrectcay FROM nswersayay WHERE nsweridayay = ?");
        $answer_query->bind_param("i", $answerid);
        $answer_query->execute();
        $answer_query->store_result();
        $answer_query->bind_result($correct);
        $answer_query->fetch();
        
        $submitted_answers = $_POST["question".$questionid];
        $in_array = in_array($answerid, $submitted_answers);
        
        if($in_array) //Selected by the user.
        {
            if($correct == 1)
            {
				$question_string .= "<tr><td><img src='correct.png' alt='CORRECT: '/></td><td><img src='correct.png' alt='CORRECT: '/></td><td>$answer_text</td></tr>";
            }
            else
            {
                $correct_tally = 0;
				$question_string .= "<tr><td><img src='incorrect.png' alt='CORRECT: '/></td><td><img src='correct.png' alt='CORRECT: '/></td><td>$answer_text</td></tr>";
            }
        }
        else //Not selected by the user.
        {
			$in_array = 0; // Null value causes error on INSERT query for answer results
            if($correct == 1)
            {
                $correct_tally = 0;
                $question_string .= "<tr><td><img src='correct.png' alt='CORRECT: '/></td><td></td><td>$answer_text</td></tr>";
            }
            else
            {
                $question_string .= "<tr><td><img src='incorrect.png' alt='CORRECT: '/></td><td></td><td>$answer_text</td></tr>";
            }
        }
        $answer_results_query = $db_handle->prepare("INSERT INTO nswerresultsayay(seriduyay, imestamptay, nsweridayay, electedsay, lassidcay) VALUES(?, ?, ?, ?, ?)");
        $answer_results_query->bind_param("isiii", $_SESSION["user"]->id, $date, $answerid, $in_array, $classid);
        $answer_results_query->execute();
    }
    
    $evaluation_string .= "<div class='question_header'>";
    if($correct_tally == 1) { $evaluation_string .= "<img class='correction_indicator' src='correct.png' alt='CORRECT: '/>"; }
    else { $evaluation_string .= "<img class='correction_indicator' src='incorrect.png' alt='INCORRECT: '/>"; }
    $evaluation_string .= "<h3 class='question_header'>Question $question_no</h3></div>";
    $evaluation_string .= "<div class='question_body'><p class='question'>".$question_text."</p>";
    $evaluation_string .= "<table class='information'><tr><th>Correct</th><th>Your response</th><th>Answer text</th></tr>";
    $evaluation_string .= $question_string;
	$evaluation_string .= "</table>";
	if(strlen($explanation) > 0) { $evaluation_string .= "<p><span class='explanation'>Explanation:</span> $explanation</p>"; }
    $correct_count += $correct_tally;
    
    $question_results_query = $db_handle->prepare("INSERT INTO uestionresultsquay(seriduyay, imestamptay, uestionidquay, orrectcay, lassidcay) VALUES(?, ?, ?, ?, ?)");
    $question_results_query->bind_param("isiii", $_SESSION["user"]->id, $date, $questionid, $correct_tally, $classid);
    $question_results_query->execute();
	
	$results_query = $db_handle->prepare("SELECT SUM(uestionresultsquay.orrectcay)/COUNT(uestionresultsquay.uestionidquay) AS 'ratio' FROM uestionresultsquay WHERE uestionidquay = ?");
	$results_query->bind_param("i", $questionid);
	$results_query->execute();
	$results_query->store_result();
	$results_query->bind_result($ratio);
	$results_query->fetch();
	$xml = "<root><object ratio='$ratio'>" . ($ratio*100)	. "% of students selected the correct value for this answer.</object></root>";
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	$donut = $xslt->transformToXml($doc);
	
	$evaluation_string .= $donut;
	$evaluation_string .= "</div>";
}

$percentage = 100*($correct_count/$num_questions);
$score_string = number_format($percentage, 2);

echo <<<_RESULTS
        <h2 class="score">Score: $score_string%</h2>
        <p>Questions answered correctly: $correct_count<br/>Total number of questions: $num_questions</p>
        <div class="question_body">
            $evaluation_string
        </div>
_RESULTS;

output_end();

$_SESSION['exam-courseid'] = NULL;
$_SESSION['exam-classid'] = NULL;
$_SESSION['exam-questions'] = NULL;
$_SESSION['exam-answers'] = NULL;
?>