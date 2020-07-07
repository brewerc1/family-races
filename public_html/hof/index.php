<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

$page_title = "Hall of Fame";
$javascript = <<<HERE
jQuery("#hof").addClass("active");
HERE;
// turn on output buffering 
ob_start('template');
?>
{header}
{main_nav}
    <h1>HOF Page</h1>
    
{footer}
<?php ob_end_flush(); ?>
