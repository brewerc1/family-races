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

// Authentication  and Authorization System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

// SQL to fetch user data

$display_user_sql = "SELECT id, first_name, last_name, photo, email, invite_code FROM user";
$display_user_result = $pdo->prepare($display_user_sql);
$display_user_result->execute();
$num_display_user_results = $display_user_result->rowCount();
$row = $display_user_result->fetch();

// TODO: interact with session variables to determine logged in user, if user is admin, maintain session, etc.

// Notification System
$notification = "";
if (isset($_GET["message"])) {
    $notification = $_GET["message"];
}

include '../../template/header.php';
?>
    <main role="main">
        <section id="User_invite">
            <h1>User Management</h1>
            <form method="POST" action="./invite_user.php" id="invite_form">
                <input type="email" name="email" placeholder="Invite a New User" required>
                <button type="submit" form="invite_form" name="invite" >+</button>
            </form>
            <!-- Notification: use css or javascript to display only for few minutes-->
            <span class="notification">
                <?php
                echo $notification;
                ?>
            </span>
        </section><!-- END user invite section -->

        <section id="display_current_users"> 
            <h2>Current Users</h2>     
             <?php

                if ($num_display_user_results > 0) {
                    $invited = "";

                    // loop through DB return
                    while($row = $display_user_result->fetch()) {

                        // handle user with invite but hasn't accepted
                        if(!empty($row["invite_code"])) {
                            $invited = "<span class='invited_chip'>pending</span>";
                            $name = $row["email"];
                        } else {
                            $name = $row["first_name"] . ' ' . $row["last_name"];
                        }
                        // handle missing photo
                        if(empty($row["photo"])) {
                            $photo = "https://races.informatics.plus/images/no-user-image.jpg";
                        } else {
                            $photo = $row["photo"];
                        }

                        // output row of user data
echo <<< ENDUSER
            <div class="user-row">
                <a href="../../user/?u={$row["id"]}"><img src="{$photo}" alt="photo"></a><span>{$name}</span> {$invited}
            </div>
ENDUSER;
                    } 
                } else {
                    echo "0 results";
                }         

                ?>  

            </div>
        </section> <!-- END display_current_users -->

    </main>

<?php
include '../../template/footer.php';
?>
