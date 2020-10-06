<?php
/**
 * Page to display Credits Screen
 * 
 * This page displays a credits screen
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// Set the page title for the template
$page_title = "Credits";

$meta = "";

// Include the race picker javascript
$javascript = <<< JAVASCRIPT
JAVASCRIPT;

// array of splash images as [<key>], credit as [<key>][0] and the optimal background positioning as [<key>][1]
$background_images = array(
    'horse-3880448_1920.jpg' => array('Clarence Alford','right top'),
    'horses-3811270_1920.jpg' => array('Clarence Alford','center top'),
    'horses-3817727_1920.jpg' => array('Clarence Alford','right top'),
);
// Randomize the array keys to get a random image filename
$random_image = array_rand($background_images);

?>
{header}
<?php if(isset($_SESSION['id'])){?>
{main_nav}
<?php
}
?>
<main role="main" id="credits_page" class="">
	<div class="row justify-content-center">
		<div class="mt-5">
			<a href="/login/">
				<div id="logo_wrapper" class="animate__animated animate__bounceOuts">
			        <svg id="logo" class="animate__animated animate__flip" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 559.47 264.1">
			            <defs>
			                <style>.cls-1{fill:#000;}</style>
			                <filter id="svg_dropshadow" x="0" y="0" width="140%" height="140%">
			                    <feOffset result="offOut" in="SourceAlpha" dx="2" dy="1" />
			                    <feGaussianBlur result="blurOut" in="offOut" stdDeviation="4" />
			                    <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
			                </filter>
			            </defs>
			            <path filter="url(#svg_dropshadow)" id="top_mane" class="cls-1" d="M144.36,58.29c-29.48,0-59-.06-88.43.08-5.27,0-7-1.16-5.26-6.79,4.61-15.15,4.39-15.21,20.22-15.21q100,0,200,0a38.15,38.15,0,0,1,4.24.46c-.37,1.9-2.22,2-3.47,2.79C261.44,46.08,249.79,49.83,240,56.94c-2.38,1.72-5.29,1.33-8,1.34Z" transform="translate(-4.13 -4.4)"/>
			            <path filter="url(#svg_dropshadow)" id="middle_mane" class="cls-1" d="M102.74,91.53q33.77,0,67.53,0a30.11,30.11,0,0,1,3.75.5,23.63,23.63,0,0,1-2.61,2.59c-7.12,5.24-14.37,10.3-21.39,15.67a15.42,15.42,0,0,1-9.91,3.56q-55.11-.16-110.23,0c-5.6,0-7.38-1.41-5.37-7,6.54-18.29,1.36-15.07,20.71-15.27C64.39,91.37,83.57,91.53,102.74,91.53Z" transform="translate(-4.13 -4.4)"/>
			            <path filter="url(#svg_dropshadow)" id="short_mane" class="cls-1" d="M59.42,147.93c14.65,0,29.31,0,44,0,1.32,0,3.38-.79,3.76,1,.19.86-1.45,2.26-2.44,3.22-4.27,4.12-9,7.87-12.8,12.33-4.38,5.09-9.61,5.94-15.88,5.86-22.49-.28-45-.17-67.47-.07-3.89,0-5.14-.89-4.05-5C9.05,148,8.94,147.93,26.77,147.93Z" transform="translate(-4.13 -4.4)"/>
			            <path filter="url(#svg_dropshadow)" id="jaw" class="cls-1" d="M428.59,200.15c-34.88-1.21-57.34-23.46-60.35-59.77-.35-4.18-.41-8.42-1.21-12.51-1.36-7,1.92-7.75,8-8.26,9.47-.78,14.27,1.16,13.38,11.89-.47,5.63,1.07,11.5,2.21,17.16,4.21,20.93,16.63,31.71,38,33.29,6.68.5,13.36.52,12.42,10.46C440.35,200.17,440.21,200.55,428.59,200.15Z" transform="translate(-4.13 -4.4)"/>
			            <path filter="url(#svg_dropshadow)" id="neck" class="cls-1" d="M450.07,21.32c.22,2.75-2,2.94-3.75,3.3-27.52,5.69-54,14.79-80.14,24.89-59,22.83-116.26,49.3-167.16,87.54A815,815,0,0,0,85.44,240c-7,7.71-13.15,16.21-19.35,24.61-2.83,3.82-5.45,5.22-9.7,2.31-3.81-2.63-7.83-4.95-11.76-7.41-3.33-2.09-1.41-4.18.09-6.13,17.42-22.63,35.17-44.84,56.72-64,21.77-19.32,41.24-41.28,65.3-58,32.06-22.26,65.17-42.82,100-60.67A880.26,880.26,0,0,1,378,22.55c17.26-6,34.31-12.68,52.19-16.8l1.27-.31C446,2.25,450.5,6.1,450.07,21.32Z" transform="translate(-4.13 -4.4)"/>
			            <path filter="url(#svg_dropshadow)" id="nose" class="cls-1" d="M533.94,179.21c-1-1.11-1.71-2.14-2.62-3L449.08,95.29c-11.16-11-22.22-22.09-33.46-33-2.8-2.72-2.65-4.61.3-7,14.59-11.9,10.33-12.06,23.33.61q61.26,59.68,122.3,119.61c2.54,2.49,2.78,4.69.36,7.48q-21,24.17-41.74,48.49c-1.88,2.19-3.29,2.5-5.78.72-16.08-11.47-16.14-11.43-3.53-26.14,7.09-8.26,14.26-16.44,21.38-24.66C532.79,180.75,533.27,180.07,533.94,179.21Z" transform="translate(-4.13 -4.4)"/>
			        </svg>
			        <h1 class="fade-in animate__animated animate__tada" id="logo_text"><?php echo $_SESSION['site_name'];?></h1>
			    </div>
			</a>
		</div>
	</div>
	<div class="py-3">
		<p>This web application was developed by <a href="https://informatics.nku.edu">College of Informatics</a> students from <a href="https://nku.edu">Northern Kentucky University</a>, in a special topics experiential learning course taught during the Summer 2020 term.</p>
		<div class="justify-content-center animate__animated animate__fadeIn animate__delay-1s">
			<img src="/images/iphone-2-up-on-white.jpg" alt="Photo of this site displayed on two iPhones" class="w-100">
		</div>
		<p>Experiential learning courses place students in real-world scenarios using industry-standard tools &mdash; working with clients to scope and deliver an end-to-end solution &mdash; in order to be better prepared for entry into the workplace.</p>
		<p>The students in this course worked with the client to develop:</p>
		<ul>
			<li>requirements documentation;</li>
			<li><a class="text-info" href="https://github.com/brewerc1/family-races/blob/master/Documentation/Wireframes.pdf">wireframes</a></li>
			<li><a class="text-info" href="https://github.com/brewerc1/family-races/blob/master/Documentation/ERD.pdf">entity relationship diagrams</a></li>
			<li><a class="text-info" href="https://github.com/brewerc1/family-races/blob/master/Documentation/sample_dataset.sql">SQL</a></li>
			<li><a class="text-info" href="https://github.com/brewerc1/family-races">source code</a>
		</ul>
		<p>The team of students created this mobile-first web application based in PHP, JQuery, Bootstrap, CSS and HTML. It consists of features such as:</p>
		<ul>
			<li>Private system using email for invites</li>
			<li>User management</li>
			<li>Multiple user levels</li>
			<li>User profiles</li>
			<li>Mobile camera photo uploads with user-controlled cropping</li>
			<li>Event and race management interface</li>
			<li>Multi-season results tracking per user</li>
			<li>Hall of fame</li>
		</ul>
	</div>
	<div class="animate__animated animate__fadeIn animate__delay-2s">
		<img src="/images/iphone-array.jpg" class="full-width" alt="Screen captures of the mobile app arranged in an array">
	</div>
	<div>	
		<div class="my-5" id="team">
			<h1>The Team</h1>
			<div class="row mt-4 mb-5">
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Josh Hannon</strong> is a senior Business Information Systems student with a focus in Health Information Systems. His interests are sports, working out and hanging out with friends.</p>
					<div class="linkedin"><a class="text-info" href="https://www.linkedin.com/in/josh-hannon15"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Josh's LinkedIn profile</a></div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Jonathan Makunga</strong> is a junior majoring in both Computer Science and Information Technology with a minor in Information Systems.</p>
					<div class="linkedin"><a class="text-info" href="https://www.linkedin.com/in/makungaj1"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Jonathan's LinkedIn profile</a></div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Robert Ruwe</strong> is a senior majoring in Computer Science with an Information Security minor. Robert spent 8 years in the army as an Explosive Ordnance Disposal Technician. He and his wife are expecting their second child in November.</p>
					<div class="linkedin"><a class="text-info" href="https://www.linkedin.com/in/robruwe/"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Rob's LinkedIn profile</a></div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Ken Ryumae</strong> is a rising senior majoring in Computer Science with a minor in Mathematics. While he is not coding, Ken likes to compose music, play games, and cook.</p>
					<div class="linkedin"><a class="text-info" href="https://www.linkedin.com/in/ken-ryumae"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Ken's LinkedIn profile</a></div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Elizabeth Schnuck</strong> is a senior with a major in Computer Information Technology and a minor is Information Systems. She has experience with HTML, Java, Ruby, SQL and JavaScript.</p>
					<div class="linkedin"><a class="text-info" href="https://linkedin.com/in/elizabeth-schnuck"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Liz's LinkedIn profile</a></div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-5">
					<p><strong>Chris Brewer</strong> is the instructor of this course, a Computer Science faculty member, and is the Project Innovation Coordinator for Informatics+ in the College of Informatics.</p>
					<div class="linkedin"><a class="text-info" href="https://www.linkedin.com/in/cbrewer"><img src="/images/linkedin-bug.png" alt="LinkedIn logo" class="linkedin-bug mr-2">Chris' LinkedIn profile</a></div>
				</div>
			</div>
		</div><!-- END #team -->
		<?php if(!isset($_SESSION['id'])){?>
		<div>
			<a href="#" onclick="window.history.back();" class="btn btn-primary">Back</a>
		</div>
		<?php
		}
		?>
	</div>
</main>
{footer}
<?php ob_end_flush(); ?>
