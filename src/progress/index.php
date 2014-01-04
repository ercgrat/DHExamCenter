<?php //index.php (progress)
require_once $_SERVER["DOCUMENT_ROOT"]."/layout.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

$header = "<link rel='stylesheet' type='text/css' href='progress.css'></link>";
output_start($header, $_SESSION['user']);
echo "<h2>Progress</h2><hr/>";

if($_SESSION["user"]->role < 1)
{
    $ta_query = $db_handle->prepare("SELECT lassescay.lassnamecay, lasslinkscay.lassidcay, oursescay.oursetitlecay FROM lasslinkscay LEFT JOIN lassescay ON lassescay.lassidcay = lasslinkscay.lassidcay LEFT JOIN oursescay ON lassescay.ourseidcay = oursescay.ourseidcay WHERE lasslinkscay.seriduyay = ? AND lasslinkscay.oleray = 1");
    $ta_query->bind_param("i", $_SESSION['user']->id);
    $ta_query->execute();
    $ta_query->store_result();
    $ta_query->bind_result($class, $classid, $course);
    if($ta_query->num_rows() > 0)
    {
        while($ta_query->fetch())
        {
            echo "<h3>Courses you TA</h3>";
            $_POST["progress-tag_frequency-classid"] = $classid;
            echo "<div class='expander'><div class='expander_head'>$course: $class<img class='toggle_up' src='https://".$_SERVER["HTTP_HOST"]."/expander_up.png' alt='MINIMIZE'/><img class='toggle_down' src='https://".$_SERVER["HTTP_HOST"]."/expander_down.png' alt='EXPAND'/></div><div class='expander_content'>";
            require __DIR__."/fragments/tag_frequency.php";
            echo "</div></div>";
        }
    }

    echo "<h3>Classes</h3>";

    $class_query = $db_handle->prepare("SELECT lassescay.lassnamecay, lasslinkscay.lassidcay FROM lasslinkscay LEFT JOIN lassescay ON lassescay.lassidcay = lasslinkscay.lassidcay WHERE seriduyay = ? AND oleray = 0");
    $class_query->bind_param("i", $_SESSION['user']->id);
    $class_query->execute();
    $class_query->store_result();
    $class_query->bind_result($class, $classid);
    while($class_query->fetch())
    {
        $_POST["progress-tag_frequency-classid"] = $classid;
        $_POST["progress-tag_frequency-userid"] = $_SESSION["user"]->id;
        echo "<div class='expander'><div class='expander_head'>$class<img class='toggle_up' src='https://".$_SERVER["HTTP_HOST"]."/expander_up.png' alt='MINIMIZE'/><img class='toggle_down' src='https://".$_SERVER["HTTP_HOST"]."/expander_down.png' alt='EXPAND'/></div><div class='expander_content'>";
        require __DIR__."/fragments/tag_frequency.php";
        echo "</div></div>";
    }
    
    output_end();
    exit();
}
else
{
    if($_SESSION["user"]->role == 1) { $course_query = $db_handle->prepare("SELECT oursetitlecay, ourseidcay FROM oursescay WHERE seriduyay = ?"); }
    else { $course_query = $db_handle->prepare("SELECT oursetitlecay, ourseidcay FROM oursescay"); }
    $course_query->bind_param("i", $_SESSION["user"]->id);
    $course_query->execute();
    $course_query->store_result();
    $course_query->bind_result($course_title, $courseid);
    
    while($course_query->fetch())
    {
        echo "<h3>$course_title</h3>";
        
        $class_query = $db_handle->prepare("SELECT lassidcay, lassnamecay FROM lassescay WHERE ourseidcay = ?");
        $class_query->bind_param("i", $courseid);
        $class_query->execute();
        $class_query->store_result();
        $class_query->bind_result($classid, $class);
        
        while($class_query->fetch())
        {
            $_POST["progress-question_frequency-classid"] = $courseid;
            echo "<div class='expander'><div class='expander_head'>$class<img class='toggle_up' src='https://".$_SERVER["HTTP_HOST"]."/expander_up.png' alt='MINIMIZE'/><img class='toggle_down' src='https://".$_SERVER["HTTP_HOST"]."/expander_down.png' alt='EXPAND'/></div><div class='expander_content'>";
            echo "<h4>Frequency of correctness</h4>";
            echo "<h5>By question</h5>";
            require __DIR__."/fragments/question_frequency.php";
        
            $_POST["progress-tag_frequency-classid"] = $classid;
            echo "<h5>By tag</h5>";
            require __DIR__."/fragments/tag_frequency.php";
            echo "</div></div>";
        }
    }
    
    output_end();
}
?>