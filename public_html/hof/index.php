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
$hof_sql = 'SELECT event.*, user.first_name, user.last_name, user.photo, user.update_time as user_update_time
FROM event, user
WHERE event.champion_id = user.id AND event.status = 1 ORDER BY event.id DESC';
$hof_result = $pdo->prepare($hof_sql);
$hof_result->execute();
$num_hof_results = $hof_result->rowCount();
?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <h1>Hall of Fame</h1>
            <section id="current_champion">
                <?php
                if($num_hof_results > 0){

                    $current_champ_row = $hof_result->fetch();
                        // $current_champ_row['champion_photo'] = ""; // test for no champ_photo
                    $num_hof_results -= 1;
                    // avoid having no image to display
                    
                    if ($current_champ_row['champion_photo'] == NULL){
                        $update_time_stamp = $current_champ_row['user_update_time'];
                        $current_champ_photo = $current_champ_row['photo']."?".$update_time_stamp;
                    } else {
                        $event_update_time_stamp = strtotime($current_champ_row['update_time']); // cache busting
                        $current_champ_photo = $current_champ_row['champion_photo']."?".$event_update_time_stamp;
                    }

echo <<< ENDCURRENT
                <div class="card text-center">
                    <h2 class="card-header">Current Champion</h2>
                    <h5 class="card-title">{$current_champ_row['name']}</h5>
                    <div class="card-body">
                        <a href="/user/?u={$current_champ_row['champion_id']}">
                            <img class="w-100" src="{$current_champ_photo}" alt="Photo of HOF winner">
                        </a>
                    </div>
                    <ul class="list-group">
                        <li class= "list-group-item">{$current_champ_row['first_name']} {$current_champ_row['last_name']}
                            <span class="badge badge-primary badge-pill" id="purse_badge">{$current_champ_row['champion_purse']}</span>
                        </li>
                    </ul>
                </div>
ENDCURRENT;
                } else {
echo <<< ENDNORESULT
<li class= "list-group-item">
    <h2 class="card-header">Current Champion</h2>
    <div class="card text-center">
        <h5 class="card-title">No Current Champion</h5>
    </div>
</li>
ENDNORESULT;
                }
            ?>
            </section> <!-- END current_champion -->

            <section id="prior_champions">
                <h2>Prior Champions</h2>
                <ul class="list-group">
                    <?php
                    if ($num_hof_results > 0){
                        while ($row = $hof_result->fetch()){
                            // avoid having no image to display

                            // $row['champion_photo'] = ""; // test for no champ_photo
                            if ($row['champion_photo'] == NULL){
                                $update_time_stamp = $row['user_update_time'];
                                $champ_photo = $row['photo']."?".$update_time_stamp;
                            } else {
                                $event_update_time_stamp = strtotime($current_champ_row['update_time']); // cache busting
                                $champ_photo = $row['champion_photo']."?".$event_update_time_stamp;
                            }
echo <<< ENDPREVIOUS
<li class= "list-group-item">
    <div class="card text-center">
        <h5 class="card-title">{$row['name']}</h5>
        <div class="card-body">
            <a href="/user/?u={$row['champion_id']}">
                <img class="w-100" src="{$champ_photo}" alt="Photo of HOF winner">
            </a>
        </div>
        <ul class="list-group">
            <li class= "list-group-item">{$row['first_name']} {$row['last_name']}
                <span class="badge badge-primary badge-pill" id="purse_badge">{$row['champion_purse']}</span>
            </li>
        </ul>
    </div>
</li>
ENDPREVIOUS;
                        }
                    } else {
echo <<< ENDNORESULT
<li class= "list-group-item">
    <div class="card text-center">
        <h5 class="card-title">No Previous Champions</h5>
    </div>
</li>
ENDNORESULT;
                    }
                ?>
            </ul>
            </section>
        </div>

{footer}
<?php ob_end_flush(); ?>
