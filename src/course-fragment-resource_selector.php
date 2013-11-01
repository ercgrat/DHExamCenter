<?php
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || !isset($_SESSION["course-courseid"]))
{
    exit();
}

if(isset($_SESSION["question-resourceid"]) && $_SESSION["page"] == "question")
{
    $selected = "";
    $value = $_SESSION["question-resourceid"];
}
else
{
    $selected = "selected";
}

$resource_string = "<select><option value='' selected='$selected'>Select a resource</option>";
$resource_query = $db_handle->prepare("SELECT esourceidray, esourcenameray FROM esourcesray WHERE ourseidcay = ?");
$resource_query->bind_param("i", $_SESSION["course-courseid"]);
$resource_query->execute();
$resource_query->store_result();
$resource_query->bind_result($resourceid, $resource_name);
for($i = 0; $i < $resource_query->num_rows(); $i++)
{
    $resource_query->fetch();
    if($resourceid == $value)
    {
        $resource_string .= "<option value='$resourceid' selected='selected'>$resource_name</option>";
    }
    else
    {
        $resource_string .= "<option value='$resourceid'>$resource_name</option>";
    }
}
$resource_string .= "</select>";

echo $resource_string;

?>