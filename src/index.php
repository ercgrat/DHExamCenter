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
		<li>Version 2.0 - September 19, 2014
			<ul>
				<li>New features polished and prepared for the new school year.</li>
				<li>Class-level capabilities have been extended to students with TA status - this includes inviting students and appointing other TA's.</li>
				<li>The progress page was revamped with brand new features and access to even more information!
					<ul>
						<li>The question-specific progress page was added!
							<ul>
								<li>Answer-level scores are presented with donut graphs to allow easy assessment of how students respond to each question.</li>
								<li>Navigation links are available on this page to edit or delete the question, as well as to see other questions with the same tags.</li>
							</ul>
						</li>
						<li>Students can now see their own topic-level score compared to the score of their entire class.</li>
						<li>TA's now have access to question-level progress information.</li>
					</ul>
				</li>
				<li>The tag page now makes it easier to access any tag and see which ones you are viewing.</li>
				<li>Minor and major bugs were fixed site-wide.</li>
			</ul>
		</li>
	</ul>
_CONTENT;

output_end();
?>