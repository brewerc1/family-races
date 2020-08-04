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
            <p class="text-white">First name and last name are REQUIRED even if you choose to skip the rest.</p>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="motto" class="col-form-label text-white" >First Name:</label>
                    <input  type="text"  required class="form-control" id="first_name" name="first_name"></input>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="motto" class="col-form-label text-white" >Last Name:</label>
                    <input  type="text"  required class="form-control" id="last_name" name="last_name"></input>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="city" class="col-form-label text-white" >City:</label>
                    <input  type="text"  class="form-control" id="city" name="city"></input>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="state" class="col-form-label text-white" >State:</label>
                    <input  type="text" class="form-control" id="state" name="state"></input>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="motto" class="col-form-label text-white" >Motto:</label>
                    <input  type="text" class="form-control" id="motto" name="motto"></input>
                </div>
                </div>
                    <input type="submit" class="btn btn-primary" name="next-btn" value="Next"></input>
    </form>
    {footer}
<?php ob_end_flush(); ?>
