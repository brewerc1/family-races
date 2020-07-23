<?php


require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

 //Authorization System
//Secure resource: invited user with email and valid code only
//if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    //header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    //exit;
//}

//$email = $_GET["email"];
//$code = $_GET["code"];

//$query = "SELECT * FROM user WHERE invite_code = :invite_code";

//$invite_code = $pdo->prepare($query);
//$invite_code->execute(['invite_code' => $code]);

//if ($invite_code->rowCount() != 1) {
    //header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    //exit;
//}
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
 
// turn on output buffering
ob_start('template');
 
// start a session
session_start();
 
// Test for authorized user
//if (!isset($_SESSION["id"])) {
    //header("Location: /login/");
    //exit;
//} elseif ($_SESSION["id"] == 0) {
    //header("Location: /login/");
    //exit;
//}
 
// Set the page title for the template
$page_title = "Login";
 
// Include the race picker javascript
$javascript = '';
 
// Get UID
//$uid = $_SESSION['id'];
 
///// DEBUG
//$debug = debug();
///// end DEBUG

$notification = array();
// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {
    //$email = trim($_POST ['email']);
    //$code = trim($_POST ['code']);
//Validation Email filed filled and email exist
    if ((empty($_POST['email'])) && (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $notification ['email'] = 'Email is Required and must be valid';
        exit;
    }
//Check if email exist
    //if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       // $errors ['email'] = 'Email address is invalid';
   // }
//Validation Code
    if(empty($_POST['code'])){
        $notification ['code'] = 'Code Required';
        exit;
    }
//Validation Password
    if(empty($_POST['password'])) {
        $notification ['password'] = 'Password Required';
        exit;
    }
//Check if passwords and confirmation match
    if($_POST['password'] !== $_POST['confirmPassword']) {
        $notification ['password'] = 'The two passwords do not match';
        exit;
    }
    $password= trim($_POST['password']);
    $email= trim($_POST['email']);
    $code = trim($_POST['code']);
    $photo = '/images/no-user-image.jpg';
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
                SET password= :password, photo=:photo, invite_code = NULL
                WHERE invite_code = :code AND email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['password'=> $password, 'photo' => $photo, 'code' => $code, 'email' => $email]);

        }
        else{
            $notification ['db_error'] = "The code and or email you provided do not match the values in the database. Please Try Again!";

        }


    if ($stmt) {
        //Update Session Variables
        $updateSession ="SELECT * FROM user WHERE id = :user_id";
        $updateSessionResult = $pdo->prepare($updateSession);
        $updateSessionResult->execute(['user_id' => $user_id]);
        $row= $updateSessionResult->fetch();
        $_SESSION ['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['photo'] = $row['photo'];
        header('Location:/onboarding/step2.php/');

    } else {
        //$errors['db_error'] = "Database error: Failed to Register";
    }
    }


?>

{header}
<h1>Sign Up</h1>
  <div>  
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>"  method="post">
                <div class="form-group">
                    <input  type="email" required class="form-control" name ="email" id="email"  placeholder="Enter Email"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" required class="form-control" name="code" id="code"  placeholder="Enter Code"></input>
                </div>
                <div class="form-group">
                    <input  type="password"  required class="form-control" name="password" id="password" placeholder="Enter Password"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" required class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password"></input>
                </div>
                    <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
            </form>
            <p>Already have a Account? <a href="/login/">Login</a></p>
    
    
    </div>
    {footer}
<?php ob_end_flush(); ?>
