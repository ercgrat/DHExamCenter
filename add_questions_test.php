<?php // add_questions_test.php

require_once "login.php";
require_once "authenticate.php";
require_once "sanitize.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}
if(!isset($_SESSION["testid"]) || !isset($_SESSION["test_name"]))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/select_modify_test.php");
    exit();
}

$header = "<link rel='stylesheet' type='text/css' href='take_test.css'/>";
$header .= "<script type='text/javascript' src='corrections.js'></script>";
output_start($header, $_SESSION['user']);

$questions = $_POST["questions"];
$testid = $_SESSION["testid"];
$q_no = 1;
while(true) //Iterate over the questions submitted, break when the POST array doesn't exist
{
    if(isset($questions[$q_no-1]) && isset($_POST["q".$q_no."_answers"]) && isset($_POST["q".$q_no."_correct"]))
    {
        $question_text = sanitizeMySQL($questions[$q_no-1]); // Retrieve and sanitize question text.
        if(isset($_POST["resource".$q_no]))
        {
            if($_POST["resource".$q_no] == "" || !is_numeric($_POST["resource".$q_no])) { $resourceid = NULL; }
            else { $resourceid = $_POST["resource".$q_no]; }
        }
        if(isset($_POST["order".$q_no])) { $order = 1; }
        else { $order = 0; }
        
        $question_query = $db_handle->prepare("INSERT INTO uestionsquay(estidtay, uestion_textquay, rderoyay) VALUES(?, ?, ?)");
        $question_query->bind_param("isi", $testid, $question_text, $order);
        if(!$question_query->execute()) { output_runtime_error("Failed to insert test question."); }
        
        $questionid_query = $db_handle->prepare("SELECT uestionidquay FROM uestionsquay WHERE uestion_textquay = ?");
        $questionid_query->bind_param("s", $question_text);
        $questionid_query->execute();
        $questionid_query->store_result();
        if($questionid_query->num_rows() != 1) { output_runtime_error("Failed to retrieve test question."); }
        $questionid_query->bind_result($questionid);
        $questionid_query->fetch();
        
        $resource_link_query = $db_handle->prepare("INSERT INTO esourcelinksray (esourceidray, uestionidquay) VALUES(?, ?)");
        $resource_link_query->bind_param("ii", $resourceid, $questionid);
        if(!$resource_link_query->execute()) { output_runtime_error("Failed to update resource-question link."); }
        
        echo "<h3 class='question'>".$q_no.". ".$question_text."</h3>";
        echo "<ul class='question list'>";
        
        $answers = $_POST["q".$q_no."_answers"]; // Retrieve array of answer text.
        $correct = $_POST["q".$q_no."_correct"];
        for ($i = 0; $i < count($answers); $i++)
        {
            $answer_text = sanitizeMySQL($answers[$i]); // Retrieve and sanitize answer text.
            // Check for value present in correct array to determine correctness
            if(in_array($i + 1, $correct)) { $truth = 1; }
            else { $truth = 0; }
            
            $answer_query = $db_handle->prepare("INSERT INTO nswersayay(uestionidquay, nswer_textayay, orrectcay) VALUES(?, ?, ?)");
            $answer_query->bind_param("isi", $questionid, $answer_text, $truth);
            $answer_query->execute();
            
            if($truth) { echo "<li class='correct'>".$answer_text."</li>"; }
            else { echo "<li>".$answer_text."</li>"; }
        }
        $q_no++;
        echo "</ul>";
    }
    else
    {
        break;
    }
}

$_SESSION["testid"] = NULL;
$_SESSION["test_name"] = NULL;

output_end();

?>