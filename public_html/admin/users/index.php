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

// Process Reset Email or Resend Invite
if (isset($_POST['submit'])){
    // If it's reset email
    if (!empty($_POST['reset_email'])){
        $uid = trim($_POST['hidden_id']);
        $email = trim($_POST['reset_email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ".$_SERVER["PHP_SELF"]."m=10&s=warning");
        } else{

        $update_user_sql = "UPDATE user SET email = :email WHERE id = :uid";
        $update_user_result = $pdo->prepare($update_user_sql);
        $update_user_result->execute(['uid' => $uid, 'email' => $email]);
        }

    }
    //if its resend invite
    if (!empty($_POST['resend_invite'])){
        $uid = trim($_POST['hidden_id']);
        $email = trim($_POST['resend_invite']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ".$_SERVER["PHP_SELF"]."m=10&s=warning");
        } else {
            // We know this user exists so we use the passed hidden_id to update email and/or invite code
                try {
                    $unique_code = generateCode();
                } catch (Exception $e) {
                    header("Location: ./?m=6&s=warning");
                    exit; //
                }
                // write to the db
                $sql = "UPDATE user SET email = :email, invite_code = :invite_code WHERE id = :uid";
                if (!$pdo->prepare($sql)->execute(['email' => $email, 'invite_code' => $unique_code, 'uid' => $uid])) {
                    header("Location: ./?m=6&s=warning");
                    exit;
                } else {
                    // send invite
                    $host = $_SERVER['SERVER_NAME'];
                    $invite_email_body = "<p>" . $_SESSION["site_invite_email_body"] . " <ul> <li>Code: $unique_code</li> <li><a href=\"http://$host/onboarding/?email=$email&code=$unique_code\">family race</a></li> </ul> </p>";

                    if (!sendEmail($_SESSION["site_email_server"], $_SESSION["site_email_server_account"],
                        $_SESSION["site_email_server_password"], $_SESSION["site_email_server_port"],
                        $_SESSION["site_email_from_name"], $_SESSION["site_email_from_address"],
                        $_SESSION["site_invite_email_subject"], $invite_email_body, $email)) {

                        header("Location: ./?m=8&s=warning");
                        exit;

                    } else {
                        header("Location: ./?m=9&s=success");
                        exit;
                    }
                }               


            /* POST to invite_user
            $url = './invite_user.php';
            $field = array(
                'email' => $email
            );
            $postvars = http_build_query($field);
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($field));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

            $result = curl_exec($ch);
            curl_close($ch);
            */
        }
    }
}

// include the menu javascript for the template
$javascript = <<< JAVASCRIPT
$('.admin_switch').each(function(index){
    ($(this).bind('click', function(){  
        var user = this.name;
        if ($(this).prop('checked')){ var ischecked = 1 
        } else {ischecked = 0}
        console.log(user);
        console.log(ischecked);
        $.ajax(
            {
                url:"./update_user.php",
                type: "POST",
                data:
                {
                    "id": user,
                    "checked": ischecked
                },
                success:function(data)
                {
                    $('#ajax_alert').html(data);
                }
            }
        );

    }));
});


JAVASCRIPT;



///// DEBUG
$debug = debug($_POST);
///// end DEBUG


// Deactivate / Reactivate / Delete Invite link Handling

// deactivate a registered user
if(!empty($_GET["u"]) && $_GET['u'] != 1 && !empty($_GET['mode']) && $_GET['mode'] == 'deactivate' && $_SESSION['admin'] == 1 ){
    $uid = trim($_GET['u']);
    // PDO to update the DB
    $update_preferences_sql = "UPDATE user SET inactive = 1 WHERE id = :uid";
    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['uid' => $uid]);
 
    // confirm update
    header("Location: ".$_SERVER["PHP_SELF"]."?m=16&s=success");
    }
 
// reactivate a registered user
if(!empty($_GET["u"]) && !empty($_GET['mode']) && $_GET['mode'] == 'reactivate' && $_SESSION['admin'] == 1 ){
    $uid = trim($_GET['u']);
    // PDO to update the DB
    $update_preferences_sql = "UPDATE user SET inactive = 0 WHERE id = :uid";
    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['uid' => $uid]);
 
    // confirm update
    header("Location: ".$_SERVER["PHP_SELF"]."?m=17&s=success");
}
    
// delete an invite
if(!empty($_GET["u"]) && $_GET['u'] != 1 && !empty($_GET['mode']) && $_GET['mode'] == 'delete' && $_SESSION['admin'] == 1){
    $uid = trim($_GET['u']);
    // PDO to update the DB
    $update_preferences_sql = "DELETE FROM user WHERE id = :uid";
    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['uid' => $uid]);
 
    // confirm update
    header("Location: ".$_SERVER["PHP_SELF"]."?m=18&s=success");
}



// SQL to fetch user data

$display_user_sql = "SELECT id, first_name, last_name, photo, email, invite_code, update_time, admin, inactive FROM user";
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

                            if(!empty($invited)) { // user invited no sign up
                                $title = 'Delete Invite';
                                $action = $_SERVER['PHP_SELF']."?u={$row['id']}&mode=delete";
                                $message="Are you sure you want to delete the invite to {$row['email']}?";
                                $value ="Resend Invite";
                                $field_name = "resend_invite";
                            }else{
                            
                            if($row['inactive'] == 0){ // active user deactivated
                                $title = 'Deactivate User';
                                $action = $_SERVER['PHP_SELF']."?u={$row['id']}&mode=deactivate";
                                $message="Are you sure you want to deactivate {$row['first_name']} {$row['last_name']}?";
                                $value = "Reset Email";
                                $field_name = "reset_email";
                            }else{ //deactivated user reactivated
                                $title = 'Reactivate User';
                                $action = $_SERVER['PHP_SELF']."?u={$row['id']}&mode=reactivate";
                                $message="Are you sure you want to reactivate {$row['first_name']} {$row['last_name']}?";
                                $value = "Reset Email";
                                $field_name = "reset_email";
                            }
                            }

                            // output row of user data
$output = <<< ENDUSER
                <li class="list-group-item">
                    <div class="media">
                        <a href="/user/?u={$row["id"]}">
                            <img src="$photo" alt="$alt" class="rounded-circle">
                        </a>
                        <div class="media-body"><span class="user_name d-inline-block px-3" data-toggle="collapse" href="#user_{$row['id']}_collapse">$name</span> {$invited}</div>
                    </div>
                    <div class="collapse" id="user_{$row['id']}_collapse">
                    <div class="card card-body">
                      <form class="mt-5" id="email_form" action="{$_SERVER["PHP_SELF"]}" method="post" >
                            <div class="form-group">
                                <input class="form-control" type="text" name="{$field_name}" value="{$row['email']}">
                                <input type="hidden" id="hidden_id" name="hidden_id" value="{$row['id']}">
                            </div>
                        <div class="form-group">
ENDUSER;



    $output .= <<< ENDUSER
                            <input class="btn btn-primary" type="submit" name="submit" value="$value">
ENDUSER;

if ($row['id'] != 1) { // not admin user 1
    $output .= <<< ENDUSER
                            <a class="ml-4" href="#" 
                                data-toggle="modal" 
                                data-target="#mainModal" 
                                data-title="$title" 
                                data-message="$message"
                                data-button-primary-text="$title" 
                                data-button-primary-action="window.location.href='$action'" 
                                data-button-secondary-text="Cancel" 
                                data-button-secondary-action="" 
                                >$title</a>
                            </div>
ENDUSER;
}
if(empty($invited) && $row['id'] != 1){ // not admin user 1
$output .= <<< ENDUSER
                            <div class="form-group custom-control custom-switch custom-switch-lg">
                                <input class="custom-control-input admin_switch" type="checkbox" id="{$row['id']}admin" name="{$row['id']}" {$user_admin_check}>
                                <label class="custom-control-label" for="{$row['id']}admin"> Admin </label>
                            </div>
                            <div id="ajax_alert"></div>
                            </form>
ENDUSER;
}
$output .= <<< ENDUSER
                      
                    </div>
                </li>
ENDUSER;                  
                    echo $output;    
                        } //Closes the loop for database users
                    } else {
                        echo "0 results";
                    }//if return is greater than 0
                    ?>
                </ul>
        </section> <!-- END display_current_users -->

    </main>

{footer}
<?php ob_end_flush(); ?>
