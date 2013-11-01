<?php //pick_test.php
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

output_start("", $_SESSION['user']);

$test_query = $db_handle->prepare("SELECT estidtay, est_nametay FROM eststay");
$test_query->execute();
$test_query->store_result();
$test_query->bind_result($testid, $test_name);

echo "<h2>Select Assessment</h2>";
echo "<ul id='test_list'>";

for($i = 1; $i <= $test_query->num_rows(); $i++)
{
    $test_query->fetch();
    echo "<li>$i. <a class='implied_link' href='take_test.php?testid=$testid'>$test_name</a></li>";
}
echo "</ul>";

output_end();
?>