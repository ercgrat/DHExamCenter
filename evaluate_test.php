<?php
require_once 'login.php';
require_once 'authenticate.php';
require_once 'layout.php';
require_once 'sanitize.php';

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
if(!isset($_SESSION['exam-courseid']) || !isset($_SESSION['questions']) || !isset($_SESSION['answers']))
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
output_start($header, $_SESSION['user']);
echo "<h1>$title</h1>";

$evaluation_string = "";
$questions_arr = $_SESSION['questions'];
$answers_arr = $_SESSION['answers'];
$num_questions = count($questions_arr);
$correct_count = 0;
for($i = 0; $i < $num_questions; $i++)
{
    $questionid = $questions_arr[$i];
    $question_query = $db_handle->prepare("SELECT uestiontextquay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text);
    $question_query->fetch();
    
    $question_string = "";
    $correct_tally = 1;
    $evaluation_string .= "<div class='question_head'><h3 class='question'>".$question_text."</h3></div>";
    
    $current_answers = $answers_arr[$i];
    $num_answers = count($current_answers);
    for($j = 0; $j < $num_answers; $j++)
    {
        $answerid = $current_answers[$j];
        $answer_query = $db_handle->prepare("SELECT nswer_textayay, orrectcay FROM nswersayay WHERE nsweridayay = ?");
        $answer_query->bind_param("i", $answerid);
        $answer_query->execute();
        $answer_query->store_result();
        $answer_query->bind_result($answer_text, $correct);
        $answer_query->fetch();
        
        $submitted_answers = $_POST["question".$questionid];
        $in_array = in_array($answerid, $submitted_answers);
        
        if($in_array)
        {
            if($correct == 1)
            {
                $question_string .= "<li class='correct'><span>".$answer_text."</span></li>";
            }
            else
            {
                $correct_tally = 0;
                $question_string .= "<li class='incorrect'><span>".$answer_text."</span></li>";
            }
        }
        else
        {
            if($correct == 1)
            {
                $correct_tally = 0;
                $question_string .= "<li>".$answer_text."</li>";
            }
            else
            {
                $question_string .= "<li>".$answer_text."</li>";
            }
        }
    }
    $question_string .= "</ul>";
    if($correct_tally == 0) { $evaluation_string .= "<ul class='question incorrect'>"; }
    else { $evaluation_string .= "<ul class='question correct'>"; }
    $evaluation_string .= $question_string;
    $correct_count += $correct_tally;
}

$percentage = 100*($correct_count/$num_questions);
$score_string = number_format($percentage, 2);

$userid_query = $db_handle->prepare("SELECT seriduyay FROM sersuyay WHERE sernameuyay = ?");
$userid_query->bind_param("s", $user->token);
$userid_query->execute();
$userid_query->store_result();
if($userid_query->num_rows() != 1) { output_runtime_error("Unable to identify user.  Please contact the server administrator."); }
$userid_query->bind_result($userid);
$userid_query->fetch();
/*
$date = date('Y-m-d H:i:s');
$record_query = $db_handle->prepare("INSERT INTO esultsray(seriduyay, estidtay, coresay, ateday) VALUES(?, ?, ?, ?)");
$record_query->bind_param("iids", $userid, $testid, $percentage, $date);
$record_query->execute();*/

echo <<<_RESULTS
        <h2 class="score">Score: $score_string%</h2>
        <ul>
            <li>Questions answered correctly: $correct_count</li>
            <li>Total number of questions: $num_questions</li>
        </ul>
        <div class="question_body">
            $evaluation_string
        </div>
_RESULTS;

output_end();

$_SESSION['testid'] = NULL;
$_SESSION['questions'] = NULL;
$_SESSION['answers'] = NULL;
?>