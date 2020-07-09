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
                    <input type="submit" name="createAccount-btn" value="Create Account"></input>
            </form>
    
    
    
    </div>
    {footer}
<?php ob_end_flush(); ?>
