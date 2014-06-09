<?php //instructor-fragment-courses.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 1)
{
    exit();
}

if($_SESSION["user"]->role == 2) { $course_query = $db_handle->prepare("SELECT oursetitlecay, ourseidcay, amenay FROM oursescay, nstiyay WHERE oursescay.nstidiyay = nstiyay.nstidiyay GROUP BY amenay, oursetitlecay"); }
else { $course_query = $db_handle->prepare("SELECT oursetitlecay, ourseidcay, amenay FROM oursescay, nstiyay WHERE oursescay.seriduyay = ? AND oursescay.nstidiyay = nstiyay.nstidiyay GROUP BY amenay, oursetitlecay"); }
$course_query->bind_param("i", $_SESSION['user']->id);
if(!$course_query->execute()) { output_runtime_error("Problem retrieving courses."); }
$course_query->store_result();
$course_query->bind_result($course_title, $courseid, $inst_name);
if($course_query->num_rows() > 0) {

    $course_query->fetch();
    $current_inst = $inst_name;
    
    $class_query = $db_handle->prepare("SELECT lassnamecay, lassidcay, nsessioniyay FROM lassescay WHERE ourseidcay = ?");
    $class_query->bind_param("i", $courseid);
    if(!$class_query->execute()) { output_runtime_error("Problem retrieving classes."); }
    $class_query->store_result();
    $class_query->bind_result($class_name, $classid, $insession);
    $class_query->fetch();
	
	$course_count = 1;
	$row_class = "variation1";
    if($insession == 1) {
        echo "<table class='information'><tr class='header'><th>Institution</th><th>Course title</th><th>Class sessions</th></tr><tr class='$row_class'><td>$inst_name</td><td><a href='course.php?id=$courseid'>$course_title</a></td><td><a href='class.php?id=$classid'>$class_name</a></td></tr>";
    } else {
        echo "<table class='information'><tr class='header'><th>Institution</th><th>Course title</th><th>Class sessions</th></tr><tr class='$row_class'><td>$inst_name</td><td><a href='course.php?id=$courseid'>$course_title</a></td><td>$class_name <span class='note'>(ended)</span></td></tr>";
    }
	
    while ($class_query->fetch())
    {
        if($insession == 1) {
            echo "<tr class='$row_class'><td></td><td></td><td><a href='class.php?id=$classid'>$class_name</a></td></tr>";
        } else {
            echo "<tr class='$row_class'><td></td><td></td><td>$class_name <span class='note'>(ended)</span></td></tr>";
        }
    }
	
	$course_count++;
    while($course_query->fetch()) {
	
		if($course_count % 2 == 1) {
			$row_class = "variation1";
		} else {
			$row_class = "variation2";
		}
		
        $class_query = $db_handle->prepare("SELECT lassnamecay, lassidcay, nsessioniyay FROM lassescay WHERE ourseidcay = $courseid ORDER BY nsessioniyay DESC");
        if(!$class_query->execute()) { output_runtime_error("Problem retrieving classes."); }
        $class_query->store_result();
        $class_query->bind_result($class_name, $classid, $insession);
        if(!$class_query->fetch()) {
            $class_name = "";
        }
    
        if($inst_name != $current_inst) {
            $current_inst = $inst_name;
            if($insession == 1) {
                echo "<tr class='$row_class'><td>$inst_name</td><td><a href='course.php?id=$courseid'>$course_title</a></td><td><a href='class.php?id=$classid'>$class_name</a></td></tr>";
            } else {
                echo "<tr class='$row_class'><td>$inst_name</td><td><a href='course.php?id=$courseid'>$course_title</a></td><td>$class_name <span class='note'>(ended)</span></td></tr>";
            }
        } else {
            if($insession == 1) {
                echo "<tr class='$row_class'><td></td><td><a href='course.php?id=$courseid'>$course_title</a></td><td><a href='class.php?id=$classid'>$class_name</a></td></tr>";
            } else {
                echo "<tr class='$row_class'><td></td><td><a href='course.php?id=$courseid'>$course_title</a></td><td>$class_name <span class='note'>(ended)</span></td></tr>";
            }
        }
        
        while ($class_query->fetch()) {
            if($insession == 1) {
                echo "<tr class='$row_class'><td></td><td></td><td><a href='class.php?id=$classid'>$class_name</a></td></tr>";
            } else {
                echo "<tr class='$row_class'><td></td><td></td><td>$class_name <span class='note'>(ended)</span></td></tr>";
            }
        }
    }
    echo "</table>";
	
} else {
    echo "<p>You are not teaching any courses.</p>";
}

?>