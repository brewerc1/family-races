<?php
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
                    <input  type="email" class= "form-control" id="email"  placeholder="Enter Email"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" class= "form-control" id="code"  placeholder="Enter Code"></input>
                </div>
                <div class="form-group">
                    <input  type="password"  class= "form-control" id="password" placeholder="Enter Password"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" class= "form-control" id="confirmPassword" placeholder="Confirm Password"></input>
                </div>
                    <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
            </form>
    
    
    
    </div>
    {footer}
<?php ob_end_flush(); ?>
<?php
$errors = array();
$email = "";
$code = "";
$password = "";
$confirmPassword = "";
session_start();
// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {
    $email = trim($_POST ['email']);
    $code = trim($_POST ['code']);
//Validation Email
    if (empty('email')){
        $errors ['email'] = 'Email Required';
    }
//Check if email exist
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors ['email'] = 'Email address is invalid';
    }
//Validation Code
    if(empty('code')){
        $errors ['code'] = 'Code Required';
    }
//Validation Password
    if(empty('password')) {
        $errors ['password'] = 'Password Required';
    }
//Check if passwords and confirmation match
    if($password !== $confirmPassword) {
        $errors ['password'] = 'The two passwords do not match';
    }
    //echo "email is $email";
    //exit;
    
    //Check for errors before writing to database
    if (count($errors === 0)){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $verified = FALSE;

        $sql = "INSERT INTO users (password) WHERE invite_code = $code AND email = $email";
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
