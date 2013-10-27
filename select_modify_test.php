<?php // select_modify_test.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}
else if($_SESSION['user']->role < 1)
{
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php");
    exit();
}

output_start("", $_SESSION['user']);

$test_query = $db_handle->prepare("SELECT estidtay, est_nametay FROM eststay");
$test_query->execute();
$test_query->store_result();
$test_query->bind_result($testid, $test_name);
echo <<<_FORM1
    <h2>Select Assessment</h2>
    <form method="post" action="modify_test.php">
    <h3>Edit Method</h2>
    <label><input type="radio" name="action" value="add" checked/>Add Questions</label><br/>
    <label><input type="radio" name="action" value="remove"/><del>Remove Questions</del></label>
    <h3>Test Name</h3>
    <ul id='test_list'>
_FORM1;

for($i = 1; $i <= $test_query->num_rows(); $i++)
{
    $test_query->fetch();
    echo "<li><label><input type='radio' name='testid' value='$testid'/>$i. $test_name</label></li>";
}
echo <<<_FORM2
    </ul>
    <input type="submit" value="Edit Test"/><br/>
    </form>
_FORM2;

output_end();

?>