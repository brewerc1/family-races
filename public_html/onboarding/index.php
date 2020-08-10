<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
 
// start a session
session_start();

// if there is a session id, then this user should not be on this page.
if (isset($_SESSION ['id'])) {
    header('Location:/races/');
    exit;
}

if (!empty($_GET['email'])){
    $email = filter_var(trim(rawurldecode($_GET['email'])), FILTER_SANITIZE_EMAIL);
} else {
	$email= "";
}

if (!empty($_GET['code'])) {
    $code = filter_var(trim($_GET['code']), FILTER_SANITIZE_STRING);
} else {
	$code = "";
}

// Set the page title for the template
$page_title = "Sign Up";
 
// Include the race picker javascript
$javascript = '';
 
///// DEBUG
//$debug = debug();
///// end DEBUG

// Load site settings and event session variables
$site_settings_sql = "SELECT * FROM site_settings";
$site_settings_query = $pdo->prepare($site_settings_sql);
if($site_settings_query->execute()) {
    if($site_settings_query->rowCount() > 0) {
		$site_update_session_results = $site_settings_query->fetch();
		foreach($site_update_session_results as $key => $value){
			$_SESSION["site_$key"] = $value;
		}
    }
}

// Current event session variable: Please check if it's set
$event_sql = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
$current_event_query = $pdo->prepare($event_sql);
if($current_event_query->execute()) {
     if($current_event_query->rowCount() > 0) {
         $_SESSION["current_event"] = $current_event_query->fetch()["id"];
     }
}

// Check if the form has been submitted
if(isset($_POST['create_account_button'])) {
	// Validate email exist and is valid
    if ((!empty($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    }else{
        header("Location: {$_SERVER['PHP_SELF']}?m=2&s=warning");
        exit;
    }
	// Validate invite code
    if(!empty($_POST['code'])){
        $code = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
        
    } else{
        header("Location: {$_SERVER['PHP_SELF']}?m=21&s=warning");
        exit;
    }
	// Validate password
    if(!empty($_POST['password']) && !empty($_POST['password_confirm'])) {
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
		$password_confirm = filter_var($_POST['password_confirm'], FILTER_SANITIZE_STRING);
		echo "$email : $code<br>";
        if($password != $password_confirm){
            header("Location: {$_SERVER['PHP_SELF']}?m=5&s=warning");
            exit; 
        }
    }else{ 
        header("Location: {$_SERVER['PHP_SELF']}?m=21&s=warning");
        exit;
    }

	// confirm that the submitted code and email match something in the user table
    $code_check_sql = "SELECT email, invite_code, id FROM user WHERE email = :email and invite_code = :code";
    $code_check_query = $pdo->prepare($code_check_sql);
    $code_check_query->execute(['email' => $email, 'code' => $code]);
	$code_check_results = $code_check_query->fetch();
	
    if($code_check_results) {
		// set the user id to the corresponding row id from the database
		$user_id = $code_check_results['id'];

		// has the password
    	$hashed_pwd = password_hash(hash_hmac($hash_algorithm, $password, $pepper), PASSWORD_BCRYPT);

		// update the database with the new password and remove the invite code
    	$update_user_sql = "UPDATE user 
            SET password = :password, invite_code = NULL
            WHERE invite_code = :code AND email = :email";
        $update_user_query = $pdo->prepare($update_user_sql);
        $update_user_query->execute(['password'=> $hashed_pwd, 'code' => $code, 'email' => $email]);

		if($update_user_query) {
	        //Update Session Variables
	        $update_session_user_sql = "SELECT * FROM user WHERE id = :user_id";
	        $update_session_user_query = $pdo->prepare($update_session_user_sql);
	        $update_session_user_query->execute(['user_id' => $user_id]);
			$update_session_user_results = $update_session_user_query->fetch();
			
			// update the session variable
			foreach($update_session_user_results as $key => $value){
				$_SESSION [$key] = $value;
			}
	        header('Location:/onboarding/step2.php');
			exit;
	    } else {
	        header("Location: {$_SERVER['PHP_SELF']}?m=6&s=warning");
	        exit;
	    }

    } else {
        $notification = "The code and or email you provided do not match the values in the database. Please Try Again!";
    }
}
?>

{header}
	<main id="onboarding_page">
		<h1 class="mb-5 sticky-top"><?php echo $page_title;?></h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<div class="form-group">
	            <input  type="email" required class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="Enter Email">
			</div>
			<div class="form-group">
				<input type="textbox" required class="form-control" name="code" id="code" value="<?php echo $code; ?>" placeholder="Enter Code">
			</div>
			<div class="form-group">
				<input type="password" required class="form-control" name="password" id="password" placeholder="Enter Password">
			</div>
			<div class="form-group">
				<input type="password" required class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm Password">
			</div>
				<input type="submit" class="btn btn-primary" name="create_account_button" value="Create Account">
		</form>
		<p class="text-center">Already have a Account? <a href="/login/">Login</a></p>
	</main>
{footer}
<?php ob_end_flush(); ?>
