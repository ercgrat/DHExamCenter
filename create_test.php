<?php
require_once 'login.php';
require_once 'sanitize.php';
require_once 'layout.php';
require_once 'authenticate.php';

function test_exists($test_name)
{
    global $db_handle;
    $testid_query = $db_handle->prepare("SELECT estidtay FROM eststay WHERE est_nametay = ?");
    $testid_query->bind_param($test_name);
    $testid_query->execute();
    $testid_query->store_result();
    if($testid_query->num_rows() != 1) { return FALSE; }
    else { return TRUE; }
}

/*
 *  Construct MySQL queries for the test, all questions, and all answers, sanitizing all user input.
 *  Loop while questions exist, determined by the set status of the $_POST arrays.
 */
function generate_test()
{
    global $db_handle;
    if(!isset($_POST["test_name"])) { output_error("Unable to process new test title.  Please contact the system administrator."); }
    else { $test_name =  sanitizeMySQL($_POST["test_name"]); }
    if(test_exists($test_name)) { output_error("Test name already exists!"); }
    
    $test_query = $db_handle->prepare("INSERT INTO eststay(est_nametay) VALUES(?)");
    $test_query->bind_param("s", $test_name);
    $test_query->execute();
    
    $testid_query = $db_handle->prepare("SELECT estidtay FROM eststay WHERE est_nametay = ?");
    $testid_query->bind_param("s", $test_name);
    $testid_query->execute();
    $testid_query->store_result();
    if($testid_query->num_rows() != 1) { output_error("Failed to create test name."); }
    $testid_query->bind_result($testid);
    $testid_query->fetch();
    
    if(isset($_POST["questions"])) { $questions = $_POST["questions"]; }
    else { output_error("Failed to locate submitted questions."); }
    
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
            
            $question_query = $db_handle->prepare("INSERT INTO uestionsquay(estidtay, uestion_textquay, esourceidray, rderoyay) VALUES(?, ?, ?, ?)");
            $question_query->bind_param("isii", $testid, $question_text, $resourceid, $order);
            $question_query->execute();
            
            $questionid_query = $db_handle->prepare("SELECT uestionidquay FROM uestionsquay WHERE uestion_textquay = ?");
            $questionid_query->bind_param("s", $question_text);
            $questionid_query->execute();
            $questionid_query->store_result();
            if($questionid_query->num_rows() != 1) { output_error("Failed to create test questions."); }
            $questionid_query->bind_result($questionid);
            $questionid_query->fetch();
            
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
            }
            $q_no++;
        }
        else
        {
            break;
        }
    }
    
    return $testid;
}

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
if(!($_SESSION['user']->role == 1))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}
if(!isset($_SESSION['creating_test']))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/pick_test.php");
    exit();
}

$testid = generate_test();
$_SESSION['creating_test'] = NULL;

output_start("", $_SESSION['user']);
echo "<p>Test creation successful! <a href='take_test.php?testid=$testid'>Take the test.</a></p>";
output_end();
?>