<<<<<<< HEAD
<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
 
// start a session
session_start();
if (isset($_SESSION ['id'])) {
    header('Location:/races/');
    exit;
} 
if (!empty($_GET['email'])){
    $getemail = filter_var(trim($_GET['email']), FILTER_SANITIZE_EMAIL);
} else { $getemail= "";}
if (!empty($_GET['code'])) {
    $getcode = filter_var(trim($_GET['code']), FILTER_SANITIZE_STRING);
} else {$getcode = "";}

// Set the page title for the template
$page_title = "Sign Up";
 
// Include the race picker javascript
$javascript = '';
 
///// DEBUG
//$debug = debug();
///// end DEBUG



// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {

// create vars and trim for email and code and password and password2

//Validation Email filed filled and email exist
    if ((!empty($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    }else{
        header("Location: ".$_SERVER['PHP_SELF']."?m=2&s=warning");
        exit;
    }
//Validation Code
    if(!empty($_POST['code'])){
        $code = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
        
    } else{
        header("Location: ".$_SERVER['PHP_SELF']."?m=21&s=warning");
        exit;
    }
//Validation Password
    if(!empty($_POST['password']) && !empty($_POST['confirmPassword'])) {
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
        $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
        if($password != $confirmPassword){
            header("Location: ".$_SERVER['PHP_SELF']."?m=5&s=warning");
            exit; 
        }
    }else{ 
        header("Location: ".$_SERVER['PHP_SELF']."?m=21&s=warning");
        exit;
    }

    //Selecting code and email from users to see if it's same as $code and $email
        $sqlcheck = "SELECT email, invite_code, id FROM user WHERE email = :email and invite_code = :code";
        $stmt1 = $pdo->prepare($sqlcheck);
        $stmt1->execute(['email' => $email, 'code' => $code]);
        $stmt1_Result=$stmt1->rowCount();
        $row = $stmt1->fetch();
        $user_id = $row['id'];
        //echo var_dump($stmt);
        //exit;
        if($stmt1) {
        $sql = "UPDATE user 
                SET password = :password, invite_code = NULL
                WHERE invite_code = :code AND email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['password'=> $password, 'code' => $code, 'email' => $email]);
        }
        else{
            $notification ['db_error'] = "The code and or email you provided do not match the values in the database. Please Try Again!";
        }


    if ($stmt) {
        //Update Session Variables
        $updateSession = "SELECT * FROM user WHERE id = :user_id";
        $updateSessionResult = $pdo->prepare($updateSession);
        $updateSessionResult->execute(['user_id' => $user_id]);
        $row= $updateSessionResult->fetch();
        $_SESSION ['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['photo'] = $row['photo'];
        header('Location:/onboarding/step2.php');

    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?m=6&s=warning");
        exit;
    }
    }


?>

{header}
	<main id="onboarding_page">
		<h1 class="mb-5 sticky-top">Sign Up</h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<div class="form-group">
	            <input  type="email" required class="form-control" name="email" id="email" value="<?php echo $getemail ?>" placeholder="Enter Email"></input>
	        </div>
	        <div class="form-group">
	            <input type="textbox" required class="form-control" name="code" id="code" value="<?php echo $getcode ?>"placeholder="Enter Code"></input>
	        </div>
	        <div class="form-group">
	            <input type="password" required class="form-control" name="password" id="password" placeholder="Enter Password"></input>
	        </div>
	        <div class="form-group">
	            <input type="textbox" required class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password"></input>
	        </div>
	            <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
	    </form>
	    <p class="text-center">Already have a Account? <a href="/login/">Login</a></p>
	</main>
    {footer}
<?php ob_end_flush(); ?>
=======
<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
 
// start a session
session_start();
if (isset($_SESSION ['id'])) {
    header('Location:/races/');
    exit;
} 
if (!empty($_GET['email'])){
    $getemail = filter_var(trim($_GET['email']), FILTER_SANITIZE_EMAIL);
} else { $getemail= "";}
if (!empty($_GET['code'])) {
    $getcode = filter_var(trim($_GET['code']), FILTER_SANITIZE_STRING);
} else {$getcode = "";}

// Set the page title for the template
$page_title = "Sign Up";
 
// Include the race picker javascript
$javascript = '';
 
///// DEBUG
//$debug = debug();
///// end DEBUG

// Load site settings and event session variables
$query = "SELECT * FROM site_settings";
$site_setts = $pdo->prepare($query);
if ($site_setts->execute() ) {
    if ($site_setts->rowCount() > 0) {
        $site_row = $site_setts->fetch();
        $_SESSION["site_name"] = $site_row["name"];
        $_SESSION["site_sound_fx"] = $site_row["sound_fx"];
        $_SESSION["site_voiceovers"] = $site_row["voiceovers"];
        $_SESSION["site_terms_enable"] = $site_row["terms_enable"];
        $_SESSION["site_terms_text"] = $site_row["terms_text"];
        $_SESSION["site_default_horse_count"] = $site_row["default_horse_count"];
        $_SESSION["site_memorial_race_enable"] = $site_row["memorial_race_enable"];
        $_SESSION["site_memorial_race_name"] = $site_row["memorial_race_name"];
        $_SESSION["site_memorial_race_number"] = $site_row["memorial_race_number"];
        $_SESSION["site_welcome_video_url"] = $site_row["welcome_video_url"];
        $_SESSION["site_invite_email_subject"] = $site_row["invite_email_subject"];
        $_SESSION["site_invite_email_body"] = $site_row["invite_email_body"];
        $_SESSION["site_email_server"] = $site_row["email_server"];
        $_SESSION["site_email_server_port"] = $site_row["email_server_port"];
        $_SESSION["site_email_server_account"] = $site_row["email_server_account"];
        $_SESSION["site_email_server_password"] = $site_row["email_server_password"];
        $_SESSION["site_email_from_name"] = $site_row["email_from_name"];
        $_SESSION["site_email_from_address"] = $site_row["email_from_address"];
    }
}

// Current event session variable: Please check if it's set
$query = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
$current_event = $pdo->prepare($query);
if ($current_event->execute()) {
     if ($current_event->rowCount() > 0) {
         $_SESSION["current_event"] = $current_event->fetch()["id"];
     }
}

// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {

// create vars and trim for email and code and password and password2

//Validation Email filed filled and email exist
    if ((!empty($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    }else{
        header("Location: ".$_SERVER['PHP_SELF']."?m=2&s=warning");
        exit;
    }
//Validation Code
    if(!empty($_POST['code'])){
        $code = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
        
    } else{
        header("Location: ".$_SERVER['PHP_SELF']."?m=21&s=warning");
        exit;
    }
//Validation Password
    if(!empty($_POST['password']) && !empty($_POST['confirmPassword'])) {
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
        $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
        if($password != $confirmPassword){
            header("Location: ".$_SERVER['PHP_SELF']."?m=5&s=warning");
            exit; 
        }
    }else{ 
        header("Location: ".$_SERVER['PHP_SELF']."?m=21&s=warning");
        exit;
    }

    //Selecting code and email from users to see if it's same as $code and $email
        $sqlcheck = "SELECT email, invite_code, id FROM user WHERE email = :email and invite_code = :code";
        $stmt1 = $pdo->prepare($sqlcheck);
        $stmt1->execute(['email' => $email, 'code' => $code]);
        $stmt1_Result=$stmt1->rowCount();
        $row = $stmt1->fetch();
        $user_id = $row['id'];
        $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $password, $pepper), PASSWORD_BCRYPT);
        //echo var_dump($stmt);
        //exit;
        if($stmt1) {
        $sql = "UPDATE user 
                SET password = :password, invite_code = NULL
                WHERE invite_code = :code AND email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['password'=> $hashed_pwd, 'code' => $code, 'email' => $email]);
        }
        else{
            $notification ['db_error'] = "The code and or email you provided do not match the values in the database. Please Try Again!";
        }


    if ($stmt) {
        //Update Session Variables
        $updateSession = "SELECT * FROM user WHERE id = :user_id";
        $updateSessionResult = $pdo->prepare($updateSession);
        $updateSessionResult->execute(['user_id' => $user_id]);
        $row= $updateSessionResult->fetch();
        $_SESSION ['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['photo'] = $row['photo'];
        $_SESSION["first_name"] = $user_row["first_name"];
        $_SESSION["last_name"] = $user_row["last_name"];
        $_SESSION["create_time"] = $user_row["create_time"];
        $_SESSION["update_time"] = $user_row["update_time"];
        $_SESSION["city"] = $user_row["city"];
        $_SESSION["state"] = $user_row["state"];
        $_SESSION["motto"] = $user_row["motto"];
        $_SESSION["sound_fx"] = $user_row["sound_fx"];
        $_SESSION["voiceovers"] = $user_row["voiceovers"];
        $_SESSION["admin"] = $user_row["admin"];
        header('Location:/onboarding/step2.php');

    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?m=6&s=warning");
        exit;
    }
    }


?>

{header}
	<main id="onboarding_page">
		<h1 class="mb-5 sticky-top">Sign Up</h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<div class="form-group">
	            <input  type="email" required class="form-control" name="email" id="email" value="<?php echo $getemail ?>" placeholder="Enter Email"></input>
	        </div>
	        <div class="form-group">
	            <input type="textbox" required class="form-control" name="code" id="code" value="<?php echo $getcode ?>"placeholder="Enter Code"></input>
	        </div>
	        <div class="form-group">
	            <input type="password" required class="form-control" name="password" id="password" placeholder="Enter Password"></input>
	        </div>
	        <div class="form-group">
	            <input type="textbox" required class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password"></input>
	        </div>
	            <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
	    </form>
	    <p class="text-center">Already have a Account? <a href="/login/">Login</a></p>
	</main>
    {footer}
<?php ob_end_flush(); ?>
>>>>>>> master
