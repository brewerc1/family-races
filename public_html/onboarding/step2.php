<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// turn on output buffering
ob_start('template');

// start a session
session_start();
if (isset($_POST['skip-btn'])) {
    Header('Location: ');
}
if (isset($_POST['next-btn'])) {
    if(empty($_POST['first_name'])) {
        $notification = "Please fill in a first name";
    }
    if(empty($_POST['last_name'])){
        $notification = "Please fill in a last name";
    }
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $motto = trim($_POST['motto']);
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
        header('Location:/onboarding/step3.php/');
                
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
                    <input  type="text" class= "form-control" id="first_name" name="first_name"  placeholder="First Name"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="last_name" name="last_name"  placeholder="Last Name"></input>
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
                <div>
                    <input type="sumbit" class="btn btn-primary" name="skip-btn" value="Skip"></input>
                </div>
    </form>
    {footer}
<?php ob_end_flush(); ?>
