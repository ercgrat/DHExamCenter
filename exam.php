<?php //exam.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

function regenerate_exam($courseid, $header)
{
    global $db_handle;
    
    $title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
    $title_query->bind_param("i", $courseid);
    $title_query->execute();
    $title_query->store_result();
    if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Course could not be identified. <a href='student.php'>Go back</a>"); }
    $title_query->bind_result($course_title);
    $title_query->fetch();
    
    output_start($header, $_SESSION['user']);
    output_test_start($course_title);

    // Grab previously stored session variables.  The user returned to the test screen from elsewhere on the site (this is designed to prevent refreshing for easier questions).
    $questions_arr = $_SESSION['exam-questions'];
    
    $num_questions = count($questions_arr);
    for($i = 0; $i < $num_questions; $i++)
    {
        display_question($questions_arr[$i], $i-1);
    }
    output_test_end();
}

function generate_new_exam($courseid, $header)
{
    global $db_handle;
    global $db_handle_pdo;
    $_SESSION['exam-questions'] = array(); // Holds identifiers of each question, to preserve randomized order.
    $_SESSION['exam-answers'] = array(); // Holds matrices of identifiers of each answer to each question, to preserve randomized order.
    
    $title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
    $title_query->bind_param("i", $courseid);
    $title_query->execute();
    $title_query->store_result();
    if($title_query->num_rows() != 1) { output_error("", $_SESSION['user'], "Course could not be identified. <a href='student.php'>Go back</a>"); }
    $title_query->bind_result($course_title);
    $title_query->fetch();
    
    output_start($header, $_SESSION['user']);
    output_test_start($course_title);
    $tags = $_POST["tags"];
    $questionid_query_string = "FROM aglinkstay WHERE (";
    for($i = 0; $i < count($tags); $i++)
    {
        $questionid_query_string .= "agidtay = ?";
        if($i < count($tags) - 1)
        {
            $questionid_query_string .= " OR ";
        }
        else
        {
            $questionid_query_string .= ")";
        }
    }
    
    $tag_count = count($tags);
    $questions_generated = 0;
    while($tag_count > 0 && $questions_generated < 10)
    {
        $questionid_count_query = $db_handle_pdo->prepare("SELECT COUNT(uestionidquay) $questionid_query_string GROUP BY uestionidquay HAVING COUNT(uestionidquay) = $tag_count");
        for($i = 1; $i <= count($tags); $i++)
        {
            $questionid_count_query->bindValue($i, $tags[$i-1]);
        }
        $questionid_count_query->execute();
        $count = $questionid_count_query->rowCount();
        
        $range = 10 - $questions_generated;
        if($count < $range) { $range = $count; }
        
        $numbers = array();
        for($i = 0; $i < $count; $i++)
        {
            array_push($numbers, $i);
        }
        shuffle($numbers);
        $indices = array();
        for($i = 0; $i < $range; $i++)
        {
            array_push($indices, $numbers[$i]);
        }
        
        for($i = 0; $i < $range; $i++)
        {
            $questionid_query = NULL;
            $questionid_query = $db_handle_pdo->prepare("SELECT uestionidquay $questionid_query_string GROUP BY uestionidquay HAVING COUNT(uestionidquay) = $tag_count LIMIT 1 OFFSET " . $indices[$i]);
            for($j = 1; $j <= count($tags); $j++)
            {
                $questionid_query->bindValue($j, $tags[$j-1]);
            }
            $questionid_query->execute();
            $questionid_query->bindColumn(1, $questionid);
            $questionid_query->fetch();
            array_push($_SESSION["exam-questions"], $questionid);
        }
        
        $questions_generated += $range;
        $tag_count--;
    }

    foreach($_SESSION["exam-questions"] as $questionid)
    {
        display_question($questionid, 0);
    }

    output_test_end();
}

function output_test_start($title)
{
    echo <<<_START
            <h2>$title</h2>
            <div class="question_list">
                <form method="post" action="results.php">
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

function display_question($questionid, $q_index)
{
    global $db_handle;
    static $question_count = 1;
    
    $question_query = $db_handle->prepare("SELECT uestiontextquay, esourceidray, rderoyay FROM uestionsquay WHERE uestionidquay = ?");
    $question_query->bind_param("i", $questionid);
    $question_query->execute();
    $question_query->store_result();
    $question_query->bind_result($question_text, $resourceid, $preserve_order);
    $question_query->fetch();
    
    echo "<h3>Question $question_count</h3>";
    echo "<div class='question_body'><p>".htmlentities($question_text);
    if(isset($resourceid))
    {
        $resource_query = $db_handle->prepare("SELECT esourcenameray, inklay FROM esourcesray WHERE esourceidray = ?");
        $resource_query->bind_param("i", $resourceid);
        $resource_query->execute();
        $resource_query->store_result();
        $resource_query->bind_result($resource, $link);
        $resource_query->fetch();
        echo "<sub class='resource'><a href='$link' target='_blank'>($resource <img src='download.png' alt=''/>)</a></sub>";
    }
    echo "</p>";
    
    
    if($_SESSION["exam-new"])
    {
        $answer_query = $db_handle->prepare("SELECT nsweridayay, nswer_textayay, orrectcay FROM nswersayay WHERE uestionidquay = ? ORDER BY nsweridayay");
        $answer_query->bind_param("i", $questionid);
        $answer_query->execute();
        $answer_query->store_result();
        $answer_query->bind_result($answerid, $answer_text, $correct);
    
        $answers_arr = array(); // Create temporary array of answer rows to shuffle.
        while($answer_query->fetch())
        {
            $row = array($answerid, htmlentities($answer_text));
            array_push($answers_arr, $row);
            $correct_count += $correct;
        }
        if(!$preserve_order) { shuffle($answers_arr); } // Randomizes order of answers.
        array_push($_SESSION["exam-answers"], $answers_arr);
    }
    else
    {
        $answers_arr = $_SESSION["exam-answers"][$question_count - 1];
    }

    if($correct_count > 1) { $input_type = "checkbox"; }
    else { $input_type = "radio"; }
    
    echo "<ul class='answer_list'>";
    for($i= 0; $i < count($answers_arr); $i++)
    {
        echo "<li><label><input type='".$input_type."' name='question".$questionid."[]' value='".$answers_arr[$i][0]."'>".$answers_arr[$i][1]."</input></label></li>";
    }
    
    
    echo "</ul></div>";

    $question_count++;
}

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/student.php");
    exit();
}
else if($_SESSION['user']->role != 0 || !isset($_POST['courseid']))
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}

$header = "<link rel='stylesheet' type='text/css' href='exam.css'/>";

$courseid = $db_handle->real_escape_string($_POST["courseid"]);
$numeric_tags = TRUE;
$tag_string = "";
for($i = 0; $i < count($_POST["tags"]); $i++)
{
    $_POST["tags"][$i] = $db_handle->real_escape_string($_POST["tags"][$i]);
    if(!is_numeric($_POST["tags"][$i]))
    {
        $numeric_tags = FALSE;
        break;
    }
    $tag_string .= $_POST["tags"][$i];
}

if(is_numeric($courseid) && $numeric_tags)
{
    if($_SESSION['exam-courseid'] == $courseid && $_SESSION['exam-tag_string'] == $tag_string)
    {
        $_SESSION['exam-new'] = 0;
        regenerate_exam($courseid, $header);
    }
    else
    {
        $_SESSION['exam-new'] = 1;
        $_SESSION['exam-courseid'] = $courseid;
        $_SESSION['exam-tag_string'] = $tag_string;
        generate_new_exam($courseid, $header);
    }
}
else
{ 
    header("Location: https://". $_SESSION["HTTP_HOST"]. "/student.php");
}

output_end();

?>