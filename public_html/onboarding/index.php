<?php

/*
if I get post, then:
    a) make sure fields are filled in and good.
    b) query db select * from users where email=post[emakil] and code=post[code];
    c) If 0 result, then they don't match. So handle error (see login/index.php for messages for how errors work). 
    d) If I get single row result, then they match and continue processing.
    e) set session vars for id=row_result[id], email=row_result[email], session[photo]=/images/no-user-image.jpg, etc.
    f) update row with hashed password and photo to session[photo] and set invite_code to null
    g) forward (header) to step2




*/
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Authorization System
// Secure resource: invited user with email and valid code only
if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$email = $_GET["email"];
$code = $_GET["code"];

$query = "SELECT * FROM user WHERE invite_code = :invite_code";

$invite_code = $pdo->prepare($query);
$invite_code->execute(['invite_code' => $code]);

if ($invite_code->rowCount() != 1) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

?>
{header}
{main_nav}
<h1>Sign Up</h1>
  <div>  
            <form>
                <div class="form-group">
                    <input  type="email" required class="form-control" id="email"  placeholder="Enter Email"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" required class="form-control" id="code"  placeholder="Enter Code"></input>
                </div>
                <div class="form-group">
                    <input  type="password"  required class="form-control" id="password" placeholder="Enter Password"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" required class="form-control" id="confirmPassword" placeholder="Confirm Password"></input>
                </div>
                    <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
            </form>
    
    
    
    </div>
    {footer}
<?php ob_end_flush(); ?>
<?php
$notification = array();
// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {
    //$email = trim($_POST ['email']);
    //$code = trim($_POST ['code']);
//Validation Email filed filled and email exist
    if ((empty('email')) && (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $notification ['email'] = 'Email is Required and must be valid';
    }
//Check if email exist
    //if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       // $errors ['email'] = 'Email address is invalid';
   // }
//Validation Code
    if(empty('code')){
        $notification ['code'] = 'Code Required';
    }
//Validation Password
    if(empty('password')) {
        $notification ['password'] = 'Password Required';
    }
//Check if passwords and confirmation match
    //if($password !== $confirmPassword) {
        //$notification ['password'] = 'The two passwords do not match';
    //}
    //Selecting code and email from users to see if it's same as $code and $email
        $sqlcheck = "SELECT * FROM users WHERE email = $_POST[email] and invite_code = $_POST[code]";
        $stmt = $dbconnect->prepare($sqlcheck);
        $stmt->bind_param('s', $password);
        $stmt->execute();
        var_dump($stmt);
        exit;
        if($stmt < 0) {
            $sql = "UPDATE users SET password='$_POST[passowrd]' WHERE invite_code = $_POST[code] AND email = $_POST[email]";
            $stmt = $dbconnect->prepare($sql);
            $stmt->bind_param('s', $password);
            $stmt->execute();
        }
        else{
            $notification ['db_error'] = "The code and or email you provided do not match the values in the database. Please Try Again!";

        }


    //exit;
    
    //Check for errors before writing to database
    if (count($errors === 0)){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $verified = FALSE;

        $sql = "UPDATE users SET password='$_POST[passowrd]' WHERE invite_code = $_POST[code] AND email = $_POST[email]";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bind_param('s', $password);
        $stmt->execute();

    if ($stmt->execute()) {
        //Login user 
        $user_id = $dbconnect->insert_id;
        $_SESSION ['id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;

    } else {
        $errors['db_error'] = "Database error: Failed to Register";
    }
    }
}


?>
