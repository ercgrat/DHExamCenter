<?php //take_test.php
require_once 'login.php';
require_once 'layout.php';
require_once 'authenticate.php';
require_once 'sanitize.php';

function regenerate_test($header)
{
    global $db_handle;
    $testid = $_SESSION['testid'];
    
    $title_query = $db_handle->prepare("SELECT est_nametay FROM eststay WHERE estidtay = ?");
    $title_query->bind_param("i", $testid);
    $title_query->execute();
    $title_query->store_result();
    if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Test could not be located. <a href='pick_test.php'>Go back</a>"); }
    $title_query->bind_result($title);
    $title_query->fetch();
    
    output_start($header, $_SESSION['user']);
    output_test_start($title);

    // Grab previously stored session variables.  The user returned to the test screen from elsewhere on the site (this is designed to prevent refreshing for easier questions).
    $questions_arr = $_SESSION['questions'];
    $answers_arr = $_SESSION['answers'];
    
    $num_questions = count($questions_arr);
    for($i = 1; $i <= $num_questions; $i++)
    {
        $questionid = $questions_arr[$i-1];
        $question_query = $db_handle->prepare("SELECT uestion_textquay, esourceidray FROM uestionsquay WHERE uestionidquay = ?");
        $question_query->bind_param("i", $questionid);
        $question_query->execute();
        $question_query->store_result();
        if($question_query->num_rows() != 1) { output_runtime_error("A question could not be located. <a href='pick_test.php'>Go back</a>"); }
        $question_query->bind_result($question_text, $resourceid);
        $question_query->fetch();
        
        echo "<h3 class='question'>".$i.". ".$question_text;
        if(isset($resourceid))
        {
            $resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray WHERE esourceidray = ?");
            $resource_query->bind_param("i", $resourceid);
            $resource_query->execute();
            $resource_query->store_result();
            $resource_query->bind_result($resource, $link);
            $resource_query->fetch();
            echo "<sub class='resource'><a href='$link'>($resource <img src='download.png' alt=''/>)</a></sub>";
        }
        echo "</h3>";
        
        echo "<ul class='question'>";
        
        $right_query = $db_handle->prepare("SELECT COUNT(*) FROM nswersayay WHERE uestionidquay = ? AND orrectcay = TRUE");
        $right_query->bind_param("i", $questionid);
        $right_query->execute();
        $right_query->store_result();
        $right_query->bind_result($right_count);
        if($right_query->num_rows() == 0) { output_runtime_error("Failed to process question answers.  Please contact the server administrator."); }
        $right_query->fetch();
        if($right_count > 1) { $input_type = "checkbox"; }
        else { $input_type = "radio"; }
        
        $current_answers = $answers_arr[$i-1];
        $num_answers = count($current_answers);
        for($j = 0; $j < $num_answers; $j++)
        {
            $answer_query = "SELECT * FROM nswersayay WHERE nsweridayay = ".$current_answers[$j];
            $answer_result = mysqli_query($db_handle, $answer_query);
            if(mysqli_num_rows($answer_result) != 1) { output_runtime_error("Failed to retrieve question answers.  Please contact the server administrator."); }
            $answer = mysqli_fetch_row($answer_result);
            echo "<li><input type='".$input_type."' name='question".$questionid."[]' value='".$answer[0]."'>".$answer[1]."</input></li>";
        }
        echo "</ul>";
    }
    output_test_end();
}

function generate_new_test($testid, $header)
{
    global $db_handle;
    $_SESSION['questions'] = array(); // Holds identifiers of each question, to preserve randomized order.
    $_SESSION['answers'] = array(); // Holds identifiers of each answer, to preserve randomized order.
    
    $title_query = $db_handle->prepare("SELECT est_nametay FROM eststay WHERE estidtay = ?");
    $title_query->bind_param("i", $testid);
    $title_query->execute();
    $title_query->store_result();
    if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Test could not be located. <a href='pick_test.php'>Go back</a>"); }
    $title_query->bind_result($title);
    $title_query->fetch();
    
    $question_query = $db_handle->prepare("SELECT uestionidquay, uestion_textquay, esourceidray, rderoyay FROM uestionsquay WHERE estidtay = ?");
    $question_query->bind_param("i", $testid);
    $question_query->execute();
    $question_query->store_result();
    if($question_query->num_rows() == 0) { output_error("", $_SESSION['user'], "Failed to retrieve requested assessment questions.  <a href='pick_test.php'>Go back</a>."); }
    $question_query->bind_result($questionid, $question_text, $resourceid, $preserve_order);
    
    output_start($header, $_SESSION['user']);
    output_test_start($title);
    
    $num_questions = $question_query->num_rows();
    
    $questions_arr = array();
    for($i = 0; $i < $num_questions; $i++)
    {
        $question_query->fetch();
        $row = array($questionid, $question_text, $resourceid, $preserve_order);
        array_push($questions_arr, $row);
    }
    shuffle($questions_arr); // Randomizes order of questions.
    
    for($i = 1; $i <= $num_questions; $i++)
    {
        $questionid = $questions_arr[$i-1][0];
        $question_text = $questions_arr[$i-1][1];
        $resourceid = $questions_arr[$i-1][2];
        $preserve_order = $questions_arr[$i-1][3];
        $correct_count = 0;
        array_push($_SESSION['questions'], $questionid);
        echo "<h3 class='question'>".$i.". ".$question_text;
        if(isset($resourceid))
        {
            $resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray WHERE esourceidray = ?");
            $resource_query->bind_param("i", $resourceid);
            $resource_query->execute();
            $resource_query->store_result();
            $resource_query->bind_result($resource, $link);
            $resource_query->fetch();
            echo "<sub class='resource'><a href='$link'>($resource <img src='download.png' alt=''/>)</a></sub>";
        }
        echo "</h3>";
        echo "<ul class='question'>";
        
        $answer_query = $db_handle->prepare("SELECT nsweridayay, nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ? ORDER BY nsweridayay");
        $answer_query->bind_param("i", $questionid);
        $answer_query->execute();
        $answer_query->store_result();
        if($answer_query->num_rows == 0) { output_runtime_error("Failed to retrieve question answers. <a href='pick_test.php'>Go back</a>"); }
        $answer_query->bind_result($answerid, $answer_text, $correct);
        $num_answers = $answer_query->num_rows();
        
        $answers_arr = array(); // Create temporary array of answer rows to shuffle.
        for($j = 0; $j < $num_answers; $j++)
        {
            $answer_query->fetch();
            $row = array($answerid, $answer_text);
            array_push($answers_arr, $row);
            $correct_count += $correct;
        }
        if(!$preserve_order) { shuffle($answers_arr); } // Randomizes order of answers.
       
        if($correct_count > 1) { $input_type = "checkbox"; }
        else { $input_type = "radio"; }
        
        $answer_ids = array(); // Create array of ids for this question.
        for($j = 0; $j < $num_answers; $j++)
        {
            $answer = $answers_arr[$j];
            echo "<li><input type='".$input_type."' name='question".$questionid."[]' value='".$answer[0]."'>".$answer[1]."</input></li>";
            array_push($answer_ids, $answer[0]);
        }
        array_push($_SESSION['answers'], $answer_ids);
        echo "</ul>";
    }
    output_test_end();
}

function output_test_start($title)
{
    echo <<<_START
            <h1>$title</h1>
            <div class="question_body">
                <form method="post" action="evaluate_test.php">
_START;
}

function output_test_end()
{
    echo <<<_END
                <input type="submit"/>
            </form>
        </div>
_END;
}

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

$header = "<link rel='stylesheet' type='text/css' href='take_test.css'/>";

if(isset($_GET["testid"]) && is_numeric($_GET["testid"]))
{
    $testid = sanitizeMySQL($_GET["testid"]);
    if($_SESSION['testid'] == $testid)
    {
        regenerate_test($header);
    }
    else
    {
        $_SESSION['testid'] = $testid;
        generate_new_test($testid, $header);
    }
}
else
{ 
    header("Location: https://". $_SESSION["HTTP_HOST"]. "/pick_test.php");
}

output_end();
?>