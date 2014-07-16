<?php //administrate-fragment-current_faculty_table.php
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || $_SESSION['user']->role < 2)
{
    exit();
}

echo "<table>";
$inst_query = $db_handle->prepare("SELECT amenay, nstidiyay FROM nstiyay GROUP BY amenay");
if(!$inst_query->execute()) { output_runtime_error("Error querying for institutions."); }
$inst_query->store_result();
$inst_query->bind_result($name, $instid);
while($inst_query->fetch())
{
    echo "<tr><td>$name</td>";
    
    $instructor_query = $db_handle->prepare("SELECT ullnamefay, ccountayay FROM sersuyay WHERE nstidiyay = ? AND oleray >= 1");
    $instructor_query->bind_param("i", $instid);
    if(!$instructor_query->execute()) { output_runtime_error("Error querying for instructors."); }
    $instructor_query->store_result();
    $instructor_query->bind_result($full_name, $account);
    
    $instructor_count = 0;
    while($instructor_query->fetch())
    {
        $instructor_count++;
        if($instructor_count == 1)
        {
            echo "<td>$full_name</td><td>$account</td></tr>";
        }
        else
        {
            echo "<tr><td></td><td>$full_name</td><td>$account</td></tr>";
        }
    }
    if($instructor_count == 0)
    {
        echo "<td></td></tr>";
    }
}
echo "</table>";

?>