<?php //about.php
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in)
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: https://". $_SERVER["HTTP_HOST"] . "/user_login.php");
    exit();
}

output_start("", $_SESSION['user']);

echo <<<_BODY
    <h2>Welcome to eXam Center!</h2>
    <h3>Synopsis</h3>
    <p>
        DH eXam Center is a learning resource for students of Computational Methods in the Humanities, a University Honors
        College course taught by Dr. David J. Birnbaum at the University of Pittsburgh. Only students of the course will
        be able to create an account and access the learning materials offered here, but visitors can still view the
        selection of practice examinations available.
    </p>
    <p>
        The eXam Center is designed to give students a quick self-assessment system that provides immediate testing feedback.
        Students can pick from a range of difficulty options and will be given a random selection of questions from the corpus
        of questions available in the XLearn database. Then, students can look at their past test results and study their progress.
        Tests are offered in a number of XML-based languages, including XML itself, as well as web-based languages pertinent to the practice of "digital humanities."
    </p>
    <h3>Development</h3>
    <p>
        At the current stage of development, eXam Center only provides questions on XPath derived from an XML document we call "Bad Hamlet".
        The document is a modified version of a TEI encoding of Shakespeare's Hamlet, and is available on the course's main webpage,
        Obdurodon, for reference.  <a href="http://dh.obdurodon.org/bad-hamlet.xml" target="_blank">View the document</a>.
        
    </p>
_BODY;

output_end();
?>