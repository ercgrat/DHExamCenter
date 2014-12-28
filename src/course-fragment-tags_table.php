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

$query_option = 0;
if(isset($_POST["course-fragment-tag_presentation_selector-option"])) {
	$query_option = $_POST["course-fragment-tag_presentation_selector-option"];
	$_SESSION["course-fragment-tag_presentation_selector-option"] = $query_option;
} else {
	if(!isset($_SESSION["course-fragment-tag_presentation_selector-option"])) {
		$_SESSION["course-fragment-tag_presentation_selector-option"] = 0;
	} else {
		$query_option = $_SESSION["course-fragment-tag_presentation_selector-option"];
	}
}

if($query_option == 0) {
	$tag_query = $db_handle->prepare("SELECT agnametay, agidtay as tagid, (SELECT COUNT(*) FROM aglinkstay WHERE agidtay = tagid) FROM agstay WHERE ourseidcay = ? ORDER BY agnametay");
} else if($query_option == 1) {
	$tag_query = $db_handle->prepare("SELECT agnametay, agidtay as tagid, (SELECT COUNT(*) FROM aglinkstay WHERE agidtay = tagid) as tally FROM agstay WHERE ourseidcay = ? ORDER BY tally");
} else {
	$tag_query = $db_handle->prepare("SELECT agnametay, agidtay as tagid, (SELECT COUNT(*) FROM aglinkstay WHERE agidtay = tagid) as tally FROM agstay WHERE ourseidcay = ? ORDER BY tally DESC");
}
$tag_query->bind_param("i", $courseid);
if(!$tag_query->execute()) {
	exit();
}
$tag_query->store_result();
$tag_query->bind_result($tag, $tagid, $question_count);
if($tag_query->num_rows() > 0) {

	echo "<ul class='horizontal_list selectable_taglist'>";
    
    $table_row = 0;
    while($tag_query->fetch()) {
		echo "<li class='tag_item horizontal_item' style='display:inline-block' data-tagid='$tagid'><input type='hidden' value='$tagid'/>$tag ($question_count)</li>";
    }
    echo "</ul>";
	
} else {
    echo "<p>There are no question tags in the corpus.</p>";
}

?>