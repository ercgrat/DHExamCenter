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
        be able to create an account and access the learning materials offered here, but visitors will soon be able to see
        snapshots of what is available for registered users.
    </p>
    <p>
        The eXam Center is designed to give students a quick self-assessment system that provides immediate testing feedback.
        Students can pick from a range of topic tags and will be given a random selection of questions from the corpus
        of questions available in the XLearn database tagged with some combination of the selected tags.
        Topics include a number of XML-based languages as well as web-based languages pertinent to the practice of "digital humanities,"
        language-specific functions and concepts, and general computational terminology.
    </p>
    <h3>Development</h3>
    <p>
        At the current stage of development, eXam Center provides a small corpus of questions based on various XML technologies.
        Soon in the future, students will be able to track their progress according to a multitude of metrics. These will include topic-level
        success, broad question-level success both as a single measurement and as improvement over time, and eventually topic suggestions
        and predictive performance indicators. Instructors will be able to identify question and answer-level success both across segments of
        the student body and for individuals, aiding their effort to address gaps in student understanding of the material.
    </p>
_BODY;

output_end();
?>