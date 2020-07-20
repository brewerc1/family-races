<?php
/**
 * Page to display Hall of Fame
 * 
 * This page displays the Hall of Fame for the current event, and all prior events.
 * Logged in users view this page.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// Set the page title for the template
$page_title = "Hall of Fame";

// Include the race picker javascript
$javascript = '';

///// DEBUG
$debug = debug("");
///// end DEBUG


// Gather data for this page
// SQL to retrieve Hall of Fame related data
$hof_sql = 'SELECT event.*, user.first_name, user.last_name, user.update_time
FROM event, user
WHERE event.champion_id = user.id AND event.status = 1 ORDER BY event.id DESC';
$hof_result = $pdo->prepare($hof_sql);
$hof_result->execute();
$num_hof_results = $hof_result->rowCount();

if($num_hof_results > 0){

$current_champ_row = $hof_result->fetch();
$num_hof_results -= 1;
}
?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <h1>Hall of Fame</h1>
            <section id="current_champion">
                <?php
echo <<< ENDCURRENT
                <div class="card text-center">
                    <h2 class="card-header">Current Champion</h2>
                    <h5 class="card-title">{$current_champ_row['name']}</h5>
                    <div class="card-body">
                        <a href="/user/?u={$current_champ_row['champion_id']}">
                            <img src="{$current_champ_row['champion_photo']}" alt="Photo of HOF winner">
                        </a>
                    </div>
                    <ul class="list-group">
                        <li class= "list-group-item">{$current_champ_row['first_name']} {$current_champ_row['last_name']}
                            <span class="badge badge-primary badge-pill" id="purse_badge">{$current_champ_row['champion_purse']}</span>
                        </li>
                    </ul>
                </div>
ENDCURRENT;
            ?>
            </section> <!-- END current_champion -->

            <section id="prior_champions">
                <h2>Prior Champions</h2>
                <?php
                if ($num_hof_results > 0){
                    while ($row = $hof_result->fetch()){
echo <<< ENDCURRENT
                        <div class="card text-center">
                            <h5 class="card-title">{$row['name']}</h5>
                            <div class="card-body">
                                <a href="/user/?u={$row['champion_id']}">
                                    <img src="{$row['champion_photo']}" alt="Photo of HOF winner">
                                </a>
                            </div>
                            <ul class="list-group">
                                <li class= "list-group-item">{$row['first_name']} {$row['last_name']}
                                    <span class="badge badge-primary badge-pill" id="purse_badge">{$row['champion_purse']}</span>
                                </li>
                            </ul>
                        </div>
ENDCURRENT;
                    }
                } else {
                    
                }
            ?>
            </section>
        </div>

{footer}
<?php ob_end_flush(); ?>
