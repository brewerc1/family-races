<?php
/**
 * Update user admin field
 * 
 * This script handles the upload of a user photo, including deleting the old
 * profile photo, writing the new profile photo, updating the DB, and updating
 * the session variables.
 * It return HTML responses on sucess and fail conditions.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// start a session
session_start();

// turn on output buffering
ob_start('template');

// set the page title for the template
$page_title = "Update User";


    if(!empty($_SESSION['id']) && $_SESSION['admin'] == 1 && $_POST['id'] != 1 && !empty($_POST['id']) && ($_POST['checked'] == 0 || $_POST['checked'] == 1)) {

        $user_id = trim($_POST['id']);
        $admin = trim($_POST["checked"]); 

        
            $update_user_sql = "UPDATE user SET admin = :admin WHERE id = :user_id";
            $update_user_result = $pdo->prepare($update_user_sql);
            $update_user_result->execute(['user_id' => $user_id, 'admin' => $admin]);
        
            // Force updated user's SESSION to refresh?

        // Looks like we made it.
        echo <<< ALERT
    <div id="photo_ajax_message_wrapper" class="alert alert-success alert-dismissible fade show" role="alert">
    <span id="photo_ajax_message">Setting Changed.</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
ALERT;
    } else {
        echo <<< ALERT
    <div id="photo_ajax_message_wrapper" class="alert alert-warning alert-dismissible fade show" role="alert">
    <span id="photo_ajax_message">Unrecoverable error.</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
ALERT;
    }
?>