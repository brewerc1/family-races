<?php
/**
 * Page to display User Profile
 * 
 * This page is used to display any user profile.
 * Logged in users have access to "edit" and "settings" links.
 * User data for logged in user is stored in $_SESSION.email
 * Page checks for $_GET
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Profile";

// include the menu javascript for the template
$javascript = '';

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

///// DEBUG
$debug = debug();
///// end DEBUG

// logged in user
$full_name = filter_var(trim($_SESSION['first_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS).' '.filter_var(trim($_SESSION['last_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$user_id = filter_var(trim($_SESSION['id']), FILTER_SANITIZE_NUMBER_INT);
$photo = $_SESSION['photo'];
$motto = filter_var(trim($_SESSION['motto']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var(filter_var(trim($_SESSION['email']), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
$city = filter_var(trim($_SESSION['city']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$state = filter_var(trim($_SESSION['state']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$update_time_stamp = strtotime($_SESSION['update_time']); // cache busting

// get selected UID: Don't run if the GET["u"] is SESSION["id"]
if (isset($_GET["u"]) && ($_GET["u"] != $_SESSION["id"])) {
    $user_id = filter_var(trim($_GET["u"]), FILTER_SANITIZE_NUMBER_INT);
    $display_user_sql = "SELECT * FROM user WHERE id = :user_id";
    $display_user_result = $pdo->prepare($display_user_sql);
    $display_user_result->execute(['user_id' => $user_id]);
    $num_display_user_results = $display_user_result->rowCount();
    
    if($num_display_user_results > 0){
        $row = $display_user_result->fetch();

        $full_name = $row['first_name'].' '.$row['last_name'];
        $user_id = $row['id'];
        $photo = $row['photo'];
        $motto = $row['motto'];
        $email = $row['email'];
        $city = $row['city'];
        $state = $row['state'];
        $update_time_stamp = strtotime($row['update_time']); // convert to timestamp for cache-busting
    }
}

// populate array of user placements
$records_sql = <<< ENDSQL
SELECT event_standings.*, event.name
FROM event_standings, event
WHERE event_standings.event_id = event.id
ORDER BY event_standings.event_id DESC, event_standings.earnings DESC
ENDSQL;
$records_result = $pdo->prepare($records_sql);
$records_result->execute();
$num_records_result = $records_result->rowCount();
$user_records_array = array();


if ($num_records_result > 0 ){
    // Grab the entire array to sort and process
    $records_array = $records_result->fetchall(PDO::FETCH_GROUP);
    // Determine how the user placed in each event
    foreach ($records_array as $event_id => $grouped_array) {
        $placement = 1;
        foreach ($grouped_array as $key => $user_row) {
            if ($user_row['user_id'] == $user_id){
                // Add a record to the tracking array
                $user_records_array += [$event_id => array('event_name' => $user_row['name'],'placement' => $placement, 'earnings' => $user_row['earnings'])];   
            } else {
                $placement += 1 ;
            }
        }
    }
} else {
    // array is empty
}
?>
{header}
{main_nav}
    <main role="main">

        <section class="row" id="user_head">
            <div class="group col-sm-5">
                <img class="rounded-circle" id="user_profile_photo" src="<?php echo "$photo?$update_time_stamp" ?>" alt="My Photo">
            </div>
            <div id="user_name" class="group col-sm-7">
                <h1>
                    <?php echo $full_name ?>
                </h1>
            </div>
<?php
if (!isset($_GET["u"]) || $_GET["u"] == $_SESSION["id"]){
    echo <<< LINKS
                <div id="edit_buttons" class="btn-group col-sm-7 ml-sm-auto text-center" role="group" aria-label="Profile Controls">
                    <a href="/user/edit/" class="btn btn-primary btn-sm" id="edit_profile">Edit Profile</a> 
                    <a href="/user/settings/" class="btn btn-primary btn-sm" id="user_settings">Settings</a>
                    <a href="/user/settings/reset.php" class="btn btn-primary btn-sm" id="user_settings">Reset Password</a>
                </div>
LINKS;
}
 ?>           
        </section> <!-- END user_head -->

            <section id="user_meta">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Motto:</th>
                            <td><?php echo $motto ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $email ?></td>
                        </tr>
                        <tr>
                            <th>City:</th>
                            <td><?php echo $city ?></td>
                        </tr>
                        <tr>
                            <th>State:</th>
                            <td><?php echo $state ?></td>
                        </tr>
                    </tbody>
                </table>
            </section><!-- END user_meta -->
                
            <section id="user_records" class="mt-4">
                <h2>Event Records</h2>
                <table class="table">
                    <tbody>
                        <?php 
                        // Takes the array of user's event placements, formats and output to table
                        foreach ($user_records_array as $record) {
                            foreach ($record as $key => $value) {
                                $placement = $record['placement'];
                                switch ($placement) {
                                    case '1':
                                        $placement = "1st";
                                        break;
                                    case '2':
                                        $placement = "2nd";
                                        break;
                                    case '3':
                                        $placement = "3rd";
                                        break;
                                    default:
                                        $placement = $placement."th";
                                        break;
                                }
                                $earnings = "$".$record['earnings'];
                            }
echo <<< ENDRECORD
                        <tr>
                            <td>{$record['event_name']} Placement: {$placement} with {$earnings}</td> <!-- Placeholder -->
                        </tr>
ENDRECORD;
                        }
                        ?>         
                    </tbody>
                </table>
            </section> <!-- END user_records -->
    </main>
{footer}
<?php ob_end_flush(); ?>