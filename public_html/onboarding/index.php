<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
 
// start a session
session_start();
if (isset($_SESSION ['id'])) {
    http_response_code(404);
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

//$notification = array();

// Check if the CreateAccount button is clicked
if (isset($_POST['createAccount-btn'])) {

    // create vars and trim for email and code and password and password2

//Validation Email filed filled and email exist
    if ((!empty($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    }else{
        $notification ['email'] = 'Email is Required and must be valid';
        exit;
    }
//Validation Code
    if(!empty($_POST['code'])){
        $code = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
        
    } else{
        $notification ['code'] = 'Code Required';
        exit;
    }
//Validation Password
    if(!empty($_POST['password']) && !empty($_POST['confirmPassword'])) {
        $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
        $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
        if($password != $confirmPassword){
        }
    }else{ 
        $notification ['password'] = 'Password Required';
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
        //$errors['db_error'] = "Database error: Failed to Register";
    }
    }


?>

{header}
<main>
    <h1>Sign Up</h1>
        <div>  
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>"  method="post">
                <div class="form-group">
                    <input  type="email" required class="form-control" name ="email" id="email"  value="<?php echo $getemail ?>" placeholder="Enter Email"></input>
                </div>
                <div class="form-group">
                    <input  type="textbox" required class="form-control" name="code" id="code"  value="<?php echo $getcode ?>"placeholder="Enter Code"></input>
                </div>
                <div class="form-group">
                    <input  type="password"  required class="form-control" name="password" id="password" placeholder="Enter Password"></input>
                </div>
                <div class="form-group">
                    <input  type="password" required class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password"></input>
                </div>
                    <input type="submit" class="btn btn-primary" name="createAccount-btn" value="Create Account"></input>
            </form>
            <p>Already have a Account? <a href="/login/">Login</a></p>
    
    
        </div>
</main>
    {footer}
<?php ob_end_flush(); ?>
