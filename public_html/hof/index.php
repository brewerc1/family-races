<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
$page_title = "Hall of Fame";
$javascript = <<<HERE
HERE;
// turn on output buffering 
ob_start('template');
?>
{header}
{main_nav}
    <main role="main">
    <h1>Hall of Fame</h1>
    <section>
        <h2>"Most recent event" Champion</h2>
        <a href="/.user/"><img src="/images/no-user-image.jpg" alt="Photo of HOF winner" width="100" height="100"></a>
        <p>champion name  purse</p>
    </section>
    <section>
        <h2>Prior Champions</h2>
        <!--loop to show prior winners events, pic, name and purse-->
    </section>

{footer}
<?php ob_end_flush(); ?>
