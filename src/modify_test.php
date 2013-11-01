<?php //modify_test.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";
require_once "sanitize.php";

function add_questions($testid, $test_name)
{
    global $db_handle;
    $_SESSION['testid'] = $testid;
    $_SESSION['test_name'] = $test_name;
    
    $header = "<link rel='stylesheet' type='text/css' href='create_test.css'/>";
    $header.= "<script type='text/javascript' src='create_test.js'></script>";
    output_start($header, $_SESSION['user']);
    
    $resource_string = "<select name='resource1'><option value='' selected='selected'>Select a resource</option>";
    $resource_query = $db_handle->prepare("SELECT esourceidray, esourcenameray FROM esourcesray");
    $resource_query->execute();
    $resource_query->store_result();
    $resource_query->bind_result($resourceid, $resource_name);
    for($i = 0; $i < $resource_query->num_rows(); $i++)
    {
        $resource_query->fetch();
        $resource_string .= "<option value='$resourceid'>$resource_name</option>";
    }
    $resource_string .= "</select>";

    echo <<<_FORM
    <h2>$test_name</h2>
    <form method="post" onsubmit="return validate_input();" action="add_questions_test.php">
    <h3>New Questions</h3>
        <div id="questions">
            <div class="question_head"><h4>Question 1:</h4><textarea name="questions[]" cols="60" rows="3"></textarea></div>
            <label><input type="checkbox" name="order1" value="1"> Preserve answer order?</label><br/>
            $resource_string
            <div class="answer_list">
                <input type="checkbox" name="q1_correct[]" value="1" />
                <label>Answer 1: <input class="answer" type="text" name="q1_answers[]"
                     size="100"/></label>
                <br />
                <input type="checkbox" name="q1_correct[]" value="2" />
                <label>Answer 2: <input class="answer" type="text" name="q1_answers[]"
                    size="100"/></label>
                <br />
                <input type="checkbox" name="q1_correct[]" value="3" />
                <label>Answer 3: <input class="answer" type="text" name="q1_answers[]"
                    size="100"/></label>
                <br />
                <input type="checkbox" name="q1_correct[]" value="4" />
                <label>Answer 4: <input class="answer" type="text" name="q1_answers[]"
                    size="100"/></label>
                <br />
            </div>
            <img src="add.png" class="a_adder" alt="[ADD ANSWER]" />
            <img src="minus.png" class="a_remover" alt="[REMOVE ANSWER]" />
        </div>
        <br />
        <img src="add.png" class="q_adder" alt="[ADD ANSWER]" />
        <img src="minus.png" class="q_remover" alt="[REMOVE ANSWER]" />
        <br />
        <br />
        <input type="submit" value="Add Questions" />
        </form>
_FORM;
    output_end();
}

function remove_questions($testid, $test_name)
{
    global $db_handle;
    $_SESSION['testid'] = $testid;
    $_SESSION['test_name'] = $test_name;
    
    $question_query = $db_handle->prepare("SELECT uestionidquay, uestion_textquay, rderoyay FROM uestionsquay WHERE estidtay = ?");
    $question_query->bind_param($testid);
    $question_query->execute();
    //$questions_query
}

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}
else if(!(isset($_POST['action']) && isset($_POST['testid']) && intval($_POST['testid'])))
{
    output_error("", $SESSION_['user'], "Missing pieces of the submitted form. <a href='https://". $_SERVER["HTTP_HOST"] . "/select_modify_test.php'>Try again.</a>");
}

$test_query = $db_handle->prepare("SELECT * FROM eststay WHERE estidtay = ?");
$test_query->bind_param("i", $_POST['testid']);
$test_query->execute();
$test_query->store_result();
if($test_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Test could not be located. <a href='https://". $_SERVER["HTTP_HOST"] . "/select_modify_test.php'>Try again.</a>"); }

$test_query->bind_result($testid, $test_name);
$test_query->fetch();

if($_POST['action'] == "add") { add_questions($testid, $test_name); }
else if($_POST['action'] == "remove") { remove_questions($testid, $test_name); }
else { output_error("", $_SESSION['user'], "Invalid action requested. <a href='https://". $_SERVER["HTTP_HOST"] . "/select_modify_test.php'>Try again.</a>"); }

?>