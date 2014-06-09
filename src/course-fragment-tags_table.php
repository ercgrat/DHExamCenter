<?php //course-fragment-tags_table.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION["user"]->logged_in || (!isset($_SESSION["course-courseid"]) && !isset($_SESSION["class-classid"]))) {
    exit();
}

if(isset($_SESSION["class-classid"])) {
    $course_query = $db_handle->prepare("SELECT ourseidcay FROM lassescay WHERE lassidcay = ?");
    $course_query->bind_param("i", $_SESSION["class-classid"]);
    if(!$course_query->execute()) { exit(); }
    $course_query->store_result();
    $course_query->bind_result($courseid);
    $course_query->fetch();
} else {
    $courseid = $_SESSION["course-courseid"];
}

$tag_query = $db_handle->prepare("SELECT agnametay, agidtay FROM agstay WHERE ourseidcay = ? ORDER BY agnametay");
$tag_query->bind_param("i", $courseid);
if(!$tag_query->execute()) {
	exit();
}
$tag_query->store_result();
$tag_query->bind_result($tag, $tagid);
if($tag_query->num_rows() > 0) {

	if($_SESSION["user"]->role == 0 && isset($_POST["student"])) {
		echo "<ul class='horizontal_list student_taglist'>";
	} else {
		echo "<ul class='horizontal_list'>";
	}
    
    $table_row = 0;
    while($tag_query->fetch()) {
        if($_SESSION["user"]->role == 0 && isset($_POST["student"])) {
			echo "<li class='tag_item horizontal_item' style='display:inline-block'><input type='hidden' value='$tagid'/>$tag</li>";
		} else {
			echo "<li class='horizontal_item'><a href=\"tag.php?id=$tagid\">$tag</a></li>";
		}
    }
    echo "</ul>";
	
} else {
    echo "<p>There are no question tags in the corpus.</p>";
}

?>