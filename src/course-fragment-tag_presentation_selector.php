<?php // course-fragment-tag_presentation_selector.php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || !isset($_SESSION["course-courseid"]))
{
    exit();
}

echo "<p>Sort by: <select>";

if($_SESSION['course-fragment-tag_presentation_selector-option'] == 0) {
	echo "<option value='0' selected='selected'>Name</option><option value='1'>Quantity (asc)</option><option value='2'>Quantity (desc)</option>";
} else if($_SESSION['course-fragment-tag_presentation_selector-option'] == 1) {
	echo "<option value='0'>Name</option><option value='1' selected='selected'>Quantity (asc)</option><option value='2'>Quantity (desc)</option>";
} else {
	echo "<option value='0'>Name</option><option value='1'>Quantity (asc)</option><option value='2' selected='selected'>Quantity (desc)</option>";
}

echo "</select></p>";


?>