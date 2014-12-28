<?php //about.php
require_once "layout.php";
require_once "login.php";

session_start();
session_regenerate_id();

output_start("", $_SESSION['user']);

echo <<<_BODY
    <h2>About</h2>
    <h3>Synopsis</h3>
    <p>Welcome to DH eXam Center.  This site is intended as a learning resource for students of digital humanities courses whose instructors would like to provide
	a responsive, online supplement to their in-class material.  Only students of instructors who sign up with eXam Center will be able to create an account and access
	the learning materials offered here, but visitors can still view the material created by those courses.</p>
    <p>The eXam Center is designed to give students a self-assessment system that provides immediate testing feedback.  Students can pick from a range of question
    topic tags - as labeled by their instructors - and then be given a random set of questions under those topics from the corpus of questions created by their instructor in the eXam database.
	After taking practice exams, students have the ability to look at their past test results and study their progress by topic and in comparison with other students.</p>
	<p>The questions that instructors create with the course dashboard tool should be engaging and somewhat difficult. Two primary goals of eXam Center are for students to
	have a way of assessing their knowledge, allowing them to focus studying on weaker areas, and for students to have an additional way to practice using their knowledge outside of the classroom.
	Instructors that use eXam Center can respond to this activity by reviewing the progress of their classes and students by topic, and tailor their classroom environment to account
	for observed performance in different subject areas.</p>
    <h3>Development</h3>
    <p>
        At the current stage of development, eXam Center allows instructors as well as their designated teaching assistants to build a corpus of questions
		with topic tags that their students can take exams on. Students are able to see a couple progress metrics, and soon will be able to track their
		progress according to a multitude of them. These will include topic-level success, broad question-level success both as a single measurement
		and as improvement over time, and eventually topic suggestions and predictive performance indicators. Instructors will be able to identify
		question and answer-level success both across segments of the student body and for individuals, aiding their effort to address gaps in student
		understanding of their course material.
    </p>
    <h4>Update Notes</h4>
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
		<li>Version 1.6 - August 9, 2014
			<ul>
				<li>Instructor search feature enhanced significantly by adding the ability to search by any number of topic tags.</li>
				<li>Question-level results donut added to the results page display after taking an exam.</li>
				<li>UI for the results page improved.</li>
			</ul>
		</li>
		<li>Version 1.5 - July 16, 2014
			<ul>
				<li>Progress feature expanded to include answer-level results for each question. This additional result data is now collected when students answer questions.</li>
				<li>A password reset request feature was added.</li>
				<li>Question rendering issues were fixed.</li>
			</ul>
		</li>
		<li>Version 1.4 - June 8, 2014
			<ul>
				<li>Complete UI overhaul.</li>
				<li>Code redesign and fixing of minor bugs across the site.</li>
			</ul>
		</li>
		<li>Version 1.3 - May 27, 2014
			<ul>
				<li>Password change feature added.</li>
				<li>Site description modified to accommodate new development goals.</li>
			</ul>
		</li>
        <li>Version 1.2 - January 2, 2014
            <ul>
                <li>Progress feature added. Students can access summaries of their performance in terms of topic tags. Instructors can view tag-based and question-based class-level performance.</li>
                <li>The navigation menu was simplified and updated to include icons.</li>
            </ul>
        </li>
        <li>Version 1.1 - December 17th, 2013
            <ul>
                <li>Page access and privileges updated to accommodate the disabling of class sessions.</li>
                <li>Students may now be added to future sections of a course by the instructor.</li>
                <li>Terminated courses are listed on student and instructor dashboards.</li>
                <li>Results interface updated to appear less cluttered.</li>
            </ul>
        </li>
        <li>Version 1.0 - November 2013
            <ul>
                <li>System is available for Pitt students only, and with no ways to measure progress. Basic functionality.</li>
            </ul>
        </li>
    </ul>
_BODY;

output_end();
?>