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

$title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
$title_query->bind_param("i", $courseid);
$title_query->execute();
$title_query->store_result();
if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Course could not be identified. <a href='student.php'>Go back</a>"); }
$title_query->bind_result($course_title);
$title_query->fetch();

// Insert separate CSS stylesheet and JavaScript script into the header.
$header = "<link rel='stylesheet' type='text/css' href='exam.css'/>";
$header.= "<link rel='stylesheet' type='text/css' href='results.css'/>";
output_start($header, $_SESSION['user']);
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
    $question_text = htmlentities($question_text);
    $explanation = htmlentities($explanation);
    
    $question_string = "";
    $correct_tally = 1;
    $question_no = $i + 1;
    $evaluation_string .= "<h3>Question $question_no</h3><div class='question_body'><p class='question'>".$question_text."</p>";
    
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
                $question_string .= "<li class='correct selected'><img class='correction' src='correct.png' alt='CORRECT: '/><span>".$answer_text."</span></li>";
            }
            else
            {
                $correct_tally = 0;
                $question_string .= "<li class='incorrect selected'><img class='correction' src='incorrect.png' alt='INCORRECT: '/><span>".$answer_text."</span></li>";
            }
        }
        else //Not selected by the user.
        {
            if($correct == 1)
            {
                $correct_tally = 0;
                $question_string .= "<li class='unselected'><img class='correction' src='correct.png' alt='CORRECT: '/>".$answer_text."</li>";
            }
            else
            {
                $question_string .= "<li class='unselected'><img class='correction' src='incorrect.png' alt='INCORRECT: '/>".$answer_text."</li>";
            }
        }
        $answer_results_query = $db_handle->prepare("INSERT INTO nswerresultsayay(seriduyay, imestamptay, nsweridayay, electedsay) VALUES(?, ?, ?, ?)");
        $answer_results_query->bind_param("isii", $_SESSION["user"]->id, $date, $answerid, $in_array);
        $answer_results_query->execute();
    }
    
    $question_string .= "</ul>";
    if(strlen($explanation) > 0) { $question_string .= "<p>Explanation: $explanation</p>"; }
    $question_string .= "</div>";
    if($correct_tally == 0) { $evaluation_string .= "<ul class='correction_list'>"; }
    else { $evaluation_string .= "<ul class='correction_list'>"; }
    $evaluation_string .= $question_string;
    $correct_count += $correct_tally;
    
    $question_results_query = $db_handle->prepare("INSERT INTO uestionresultsquay(seriduyay, imestamptay, uestionidquay, orrectcay) VALUES(?, ?, ?, ?)");
    $question_results_query->bind_param("isii", $_SESSION["user"]->id, $date, $questionid, $correct_tally);
    $question_results_query->execute();
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
$_SESSION['exam-questions'] = NULL;
$_SESSION['exam-answers'] = NULL;
?>