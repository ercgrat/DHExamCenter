<?php //student.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if ($_SESSION["user"]->role != 0)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] ."/index.php");
    exit();
}

$header = "<script type='text/javascript' src='course-fragment-tags_table.js'></script>";
output_start($header, $_SESSION["user"]);

$inst_query = $db_handle->prepare("SELECT amenay FROM nstiyay WHERE nstidiyay = ?");
$inst_query->bind_param("i", $_SESSION["user"]->inst);
$inst_query->execute();
$inst_query->store_result();
$inst_query->bind_result($inst_name);
$inst_query->fetch();
echo "<h2>$inst_name</h2>";

$ta_query = $db_handle->prepare("SELECT lassidcay FROM lasslinkscay WHERE seriduyay = ? AND oleray = 1");
$ta_query->bind_param("i", $_SESSION["user"]->id);
$ta_query->execute();
$ta_query->store_result();
$ta_query->bind_result($classid);
if($ta_query->num_rows() > 0)
{
    echo "<h3>Courses you TA</h3>";
    echo "<table class='information'><tr><th>Course</th><th>Class session</th></tr>";
    $classes = array();
    while($ta_query->fetch())
    {
        $class_query = $db_handle->prepare("SELECT ourseidcay, nsessioniyay, lassnamecay FROM lassescay WHERE lassidcay = ?");
        $class_query->bind_param("i", $classid);
        $class_query->execute();
        $class_query->store_result();
        $class_query->bind_result($courseid, $insession, $classname);
        $class_query->fetch();
        
        $class = array($courseid, $insession, $classname);
        array_push($classes, $class);
    }
    
	$class_count = 0;
	$row_style = "";
    foreach($classes as $class)
    {
        $courseid = $class[0];
        $insession = $class[1];
        $classname = $class[2];
		if($class_count % 2 == 0) {
			$row_style = "variation1";
		} else {
			$row_style = "variation2";
		}
    
        $title_query = $db_handle->prepare("SELECT oursetitlecay FROM oursescay WHERE ourseidcay = ?");
        $title_query->bind_param("i", $courseid);
        $title_query->execute();
        $title_query->store_result();
        $title_query->bind_result($course_title);
        $title_query->fetch();
        
        if($insession == 1)
        {
            echo "<tr class='$row_style'><td><a class='title2' href='https://$root/course.php?id=$courseid'>$course_title</a></td><td>$classname</td></tr>";
        }
        else
        {
            echo "<tr class='$row_style'><td><span class='disabled'>$course_title</span></td><td><span class='disabled'>$classname <span class='note'>(ended)</span></span></td></tr>";
        }
		$class_count++;
    }
    echo "</table>";
}

echo "<h3>Classes</h3>";

$course_query = $db_handle->prepare("SELECT DISTINCT oursescay.ourseidcay, oursescay.oursetitlecay FROM oursescay JOIN lassescay ON oursescay.ourseidcay = lassescay.ourseidcay JOIN lasslinkscay ON lassescay.lassidcay = lasslinkscay.lassidcay WHERE lasslinkscay.seriduyay = ?");
$course_query->bind_param("i", $_SESSION["user"]->id);
$course_query->execute();
$course_query->store_result();
$course_query->bind_result($courseid, $course_title);

if($course_query->num_rows() == 0) {
	echo "<p>You are not participating in any classes.</p>";
} else {
	echo "<p>Select topic tags from one of your classes to take a short assessment focused on those topics.</p>";
	while($course_query->fetch()) {
		$class_query = $db_handle->prepare("SELECT lasslinkscay.lassidcay, lassescay.lassnamecay, lassescay.nsessioniyay FROM lasslinkscay JOIN lassescay ON lasslinkscay.lassidcay = lassescay.lassidcay WHERE lasslinkscay.seriduyay = ? AND lassescay.ourseidcay = ?");
		$class_query->bind_param("ii", $_SESSION["user"]->id, $courseid);
		$class_query->execute();
		$class_query->store_result();
		$class_query->bind_result($classid, $class_name, $insession);
		
		while($class_query->fetch()) {
			if($insession == 1) {
				echo "<h4>$course_title: $class_name</h4>";

				echo "<form method='post' action='exam.php'>";
				$_SESSION["course-courseid"] = $courseid;
				require "course-fragment-tags_table.php";
				$_SESSION["course-courseid"] = NULL;
				echo "<input class='submit_type' type='submit' value='Start Exam'/>";
				echo "<input name='courseid' type='hidden' value='$courseid'/>";
				echo "<input name='classid' type='hidden' value='$classid'/>";
				echo "</form>";
			} else {
				echo "<h4 class='disabled'>$class_name</h4>";
			}
		}
	}
}
output_end();
?>