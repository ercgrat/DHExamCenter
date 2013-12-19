<?php //progress.php
require_once "layout.php";
require_once "authenticate.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

if($_SESSION["user"]->role < 2)
{
    output_start("", $_SESSION['user']);
    echo "<p>This feature of XLearn is under construction.</p><img class='unavailable' src='unavailable.jpg' alt=''/>";
    output_end();
    exit();
}

$xml = "<root>";

$user_query = $db_handle->prepare("SELECT DISTINCT seriduyay FROM uestionresultsquay");
$user_query->execute();
$user_query->store_result();
$user_query->bind_result($userid);
while($user_query->fetch())
{
    $xml .= "<student>";
    $accuracy_query = $db_handle->prepare("SELECT orrectcay, COUNT(orrectcay) FROM uestionresultsquay WHERE seriduyay = ? GROUP BY orrectcay");
    $accuracy_query->bind_param("i", $userid);
    $accuracy_query->execute();
    $accuracy_query->store_result();
    $accuracy_query->bind_result($value, $count);
    
    $incorrect = 0;
    $correct = 0;
    $accuracy_query->fetch();
    if($value == 0) { $incorrect = $count; }
    else { $correct = $count; }
    if($accuracy_query->fetch())
    {
        if($value == 0) { $incorrect = $count; }
        else { $correct = $count; }
    }
    
    $xml .= "<correct>$correct</correct>";
    $xml .= "<incorrect>$incorrect</incorrect>";
    
    $xml .= "</student>";
}

$xml .= "</root>";

$doc = new SimpleXMLElement($xml);
echo $doc->asXML();

?>