<?php
/**
 * Page to display Splash Screen
 * 
 * This page displays a splash screen for 4 seconds,
 * then fowards to login.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Set the page title for the template
$page_title = "Welcome!";

$meta = "<meta http-equiv='refresh' content='4; url=/login/'>";

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
<main role="main" id="index_page" style="background-image: url('/images/photos/splash/<?php echo $random_image;?>');background-position:<?php echo $background_images[$random_image][1];?>">
    <div id="logo_wrapper" class="vertical-center animate__animated animate__bounceOuts">
        <svg id="logo" class="animate__animated animate__flip" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 559.47 264.1">
            <defs>
                <style>.cls-1{fill:#fff;}</style>
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
        <h1 class="fade-in animate__animated animate__tada" id="logo_text">Keene Challenge</h1>
    </div>
    <span id="photo_credit">Photo by <?php echo $background_images[$random_image][0];?></span>
</main>
{footer}
<?php ob_end_flush(); ?>
