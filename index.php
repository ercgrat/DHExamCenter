<?php //index.php
require_once "authenticate.php";
require_once "layout.php";

session_start();
session_regenerate_id();

$user = $_SESSION['user'];
output_start("", $_SESSION['user']);

echo <<<_CONTENT
    <h2 style="text-decoration:none">WELCOME</h2>
    <p>Welcome to DH eXam Center.  This site is a learning resource for students of Computational Methods in the Humanities, a University Honors College course taught
    by Dr. David J. Birnbaum at the University of Pittsburgh.  Only students of the course will be able to create an account and access the learning materials
    offered here, but visitors can still view the selection of practice examinations available.</p>
    <p>The eXam Center is designed to give students a quick self-assessment system that provides immediate testing feedback.  Students can pick from a range of difficulty
    options and will be given a random selection of questions from the corpus of questions available in the XLearn database.  Then, students can look at their
    past test results and study their progress.  Tests are offered in a number of XML-based languages, including XML itself, as well as web-based languages pertinent to
    the practice of "digital humanities."</p>
_CONTENT;

output_end();
?>