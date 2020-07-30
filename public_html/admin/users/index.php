<?php

/**
 * Admin page for user management
 * 
 * This page fetches and displays data for all users. 
 * Registered users are displayed as a picture, first name, last name.
 * Invited users are displayed with their email, and a pending tag. 
 * A user can be invited by entering an email.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "User Management";

// include the menu javascript for the template
$javascript = '';

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

// SQL to fetch user data

$display_user_sql = "SELECT id, first_name, last_name, photo, email, invite_code, update_time, admin FROM user";
$display_user_result = $pdo->prepare($display_user_sql);
$display_user_result->execute();
$num_display_user_results = $display_user_result->rowCount();

?>
{header}
{main_nav}
    <main role="main">
        <h1>User Management</h1>
        <section id="User_invite" class="mt-3 mb-4" method="post" >
            <form method="POST" action="./invite_user.php" id="invite_form">
                <div class="form-row align-items-center justify-content-center">
                    <div class="col-auto">
                        <label class="sr-only" for="inlineFormInputName">Email address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@</div>
                            </div>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email address to invite" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary font-weight-bold" form="invite_form" name="invite" >Invite</button>
                    </div>
                </div>
            </form>
        </section><!-- END user invite section -->

        <section id="display_current_users">
                <ul class="user-list list-group list-group-flush">
                <?php
                    if ($num_display_user_results > 0) {

                        // loop through DB return
                        while($row = $display_user_result->fetch()) {
                            // Has to be inside the loop so in every iteration, it's gonna be empty
                            $invited = "";
                            $update_time_stamp = strtotime($row["update_time"]); // convert to timestamp for cache-busting
                            // handle user with invite but hasn't accepted
                            if(!is_null($row["invite_code"])) {
                                $invited = "<span class='badge badge-primary badge-pill float-right px-2' id='invited_badge'>pending</span>";
                                $name = $row["email"];
                            } else {
                                $name = $row["first_name"] . ' ' . $row["last_name"];
                            }
                            // handle missing photo
                            if(empty($row["photo"])) {
                                $photo = "/images/no-user-image.jpg"; // do not cache-bust this image
                                $alt = "This user has no photo";
                            } else {
                                $photo = $row["photo"] ."?$update_time_stamp"; // cache-bust this image
                                $alt = "A photo of $name";
                            }
                            if ($row['admin']=== 1) {
                                $user_admin_check = "checked";
                            } else {
                                $user_admin_check ="";
                            }
                            // output row of user data
echo <<< ENDUSER
                <li class="list-group-item">
                    <div class="media">
                        <a href="/user/?u={$row["id"]}">
                            <img src="$photo" alt="$alt" class="rounded-circle">
                        </a>
                        <div class="media-body"><span class="user_name d-inline-block px-3" data-toggle="collapse" href="#user_{$row['id']}_collapse">$name</span> {$invited}</div>
                    </div>
                    <div class="collapse" id="user_{$row['id']}_collapse">
                    <div class="card card-body">
                      <form>
                            <div class="form-group">
                                <input type="text" id="edit_email" name="edit_email" value="{$row['email']}">
                                
                            </div>
                            <div class="form-group">
                            <input type="submit" id="reset_email" value="Reset Email">
                            <a href="#" 
                            data-toggle="modal" 
                            data-target="#mainModal" 
                            data-title="Delete User" 
                            data-message="Are you sure you want to delete {$row['first_name']} {$row['last_name']}?"
                            data-button-primary-text="Delete User" 
                            data-button-primary-action="window.location.href='<?php echo {$_SERVER['PHP_SELF']}; ?>'" 
                            data-button-secondary-text="Cancel" 
                            data-button-secondary-action="" 
                                >Delete User</a>
                            </div>
                            <div class="form-group custom-control custom-switch custom-switch-lg">
                        <input class="custom-control-input" type="checkbox" id="admin" name="admin" {$user_admin_check}>
                        <label class="custom-control-label" for="sound_fx"> Admin </label>
                    </div>
                      </form>
                    </div>
                </li>
ENDUSER;
                        } 
                    } else {
                        echo "0 results";
                    }
                    ?>  
                </ul>
        </section> <!-- END display_current_users -->

    </main>

{footer}
<?php ob_end_flush(); ?>
