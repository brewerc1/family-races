<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// turn on output buffering
ob_start('template');

// start a session
session_start();

// Set the page title for the template
$page_title = "Your Profile";

if (!isset($_SESSION["id"])){
  header("Location: /login/");
   exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}


if (isset($_POST['next-btn'])) {
    if(!empty($_POST['first_name'])) {
        $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    } else {
        $notification = "Please fill in a first name";
        exit;
    }
    if(!empty($_POST['last_name'])){
        $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    } else {
        $notification = "Please fill in a last name";
        exit;
    }
    if(!empty($_POST['city'])){
        $city = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
    } else{
        $city = "";
    }
    if(!empty($_POST['state'])){
        $state = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    } else{
        $state = "";
    }
    if(!empty($_POST['motto'])){
        $motto = filter_var(trim($_POST['motto']), FILTER_SANITIZE_STRING);
    } else {
        $motto = "";
    }
    $sqlUpdate = "UPDATE user SET
    first_name = :first_name, last_name = :last_name, city =:city, state = :state, motto= :motto 
    WHERE id = {$_SESSION['id']}";
    $update = $pdo->prepare($sqlUpdate);
    $update->execute(['first_name' => $first_name,
                    'last_name' => $last_name, 
                    'city' => $city, 
                    'state' => $state, 
                    'motto'=> $motto]);
        if ($update) {
        //Update Session Variables
        $updateSession ="SELECT * FROM user WHERE id = {$_SESSION['id']}";
        $updateSessionResult = $pdo->prepare($updateSession);
        $updateSessionResult->execute(['user_id' => $user_id]);
        $row= $updateSessionResult->fetch();
        $_SESSION ['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['city'] = $row['city'];
        $_SESSION['state'] = $row['state'];
        $_SESSION['motto'] = $row['motto'];
        header('Location:/onboarding/step3.php');
                
            } else {
                        //$errors['db_error'] = "Database error: Failed to Register";
                    }

}

?>
{header}
{main_nav}
<h1>Your Profile</h1>


<form action="<?php echo $_SERVER["PHP_SELF"]; ?>"  method="post">
                <div class="form-group">
                    <p>First name and last name are REQUIRED even if you choose to skip the rest!</p>
                    <input  type="text"  required class= "form-control" id="first_name" name="first_name"  placeholder="First Name"></input>
                </div>
                <div class="form-group">
                    <input  type="text"  required class= "form-control" id="last_name" name="last_name"  placeholder="Last Name"></input>
                </div>
                <div class="form-group">
                    <input  type="text"  class= "form-control" id="city" name="city" placeholder="City"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="state" name="state" placeholder="State"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="motto" name="motto" placeholder="Motto"></input>
                </div>
                    <input type="submit" class="btn btn-primary" name="next-btn" value="Next"></input>
    </form>
    {footer}
<?php ob_end_flush(); ?>
