<?php //index.php
require_once "authenticate.php";
require_once "layout.php";

session_start();
session_regenerate_id();

$user = $_SESSION['user'];
output_start("", $_SESSION['user']);

echo <<<_CONTENT
    <h2>Welcome to eXam Center!</h2>
    <p>Welcome to DH eXam Center.  This site is intended as a learning resource for students of digital humanities courses whose instructors would like to provide
	a responsive, online supplement to their in-class material.  Only students of instructors who sign up with eXam Center will be able to create an account and access
	the learning materials offered here, but visitors can still view the material created by those courses.</p>
	<p>See more on the <a href="https://$root/about.php">About</a> page.</p>
    <h4>Last update</h4>
    <ul>
		<li>Version 1.5 - July 16, 2014
			<ul>
				<li>Progress feature expanded to include answer-level results for each question. This additional result data is now collected when students answer questions.</li>
				<li>A password reset request feature was added.</li>
				<li>Question rendering issues were fixed.</li>
			</ul>
		</li>
	</ul>
_CONTENT;

output_end();
?>