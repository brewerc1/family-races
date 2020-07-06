<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
$page_title = "Races";
//$javascript = ‘’;
// turn on output buffering 
ob_start('template');
?>
{header}
{main_nav}
    <h1>HOF Page</h1>
    
{footer}
<?php ob_end_flush(); ?>
