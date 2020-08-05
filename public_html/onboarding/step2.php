<<<<<<< HEAD
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

// State Select Array
$state_array = array(	
	"AK" => "Alaska",
	"AL" => "Alabama",
	"AR" => "Arkansas",
	"AZ" => "Arizona",
	"CA" => "California",
	"CO" => "Colorado",
	"CT" => "Connecticut",
	"DC" => "District of Columbia",
	"DE" => "Delaware",
	"FL" => "Florida",
	"GA" => "Georgia",
	"HI" => "Hawaii",
	"IA" => "Iowa",
	"ID" => "Idaho",
	"IL" => "Illinois",
	"IN" => "Indiana",
	"KS" => "Kansas",
	"KY" => "Kentucky",
	"LA" => "Louisiana",
	"MA" => "Massachusetts",
	"MD" => "Maryland",
	"ME" => "Maine",
	"MI" => "Michigan",
	"MN" => "Minnesota",
	"MO" => "Missouri",
	"MS" => "Mississippi",
	"MT" => "Montana",
	"NC" => "North Carolina",
	"ND" => "North Dakota",
	"NE" => "Nebraska",
	"NH" => "New Hampshire",
	"NJ" => "New Jersey",
	"NM" => "New Mexico",
	"NV" => "Nevada",
	"NY" => "New York",
	"OH" => "Ohio",
	"OK" => "Oklahoma",
	"OR" => "Oregon",
	"PA" => "Pennsylvania",
	"PR" => "Puerto Rico",
	"RI" => "Rhode Island",
	"SC" => "South Carolina",
	"SD" => "South Dakota",
	"TN" => "Tennessee",
	"TX" => "Texas",
	"UT" => "Utah",	
	"VA" => "Virginia",
	"VT" => "Vermont",
	"WA" => "Washington",
	"WI" => "Wisconsin",
	"WV" => "West Virginia",
	"WY" => "Wyoming"
);
if (isset($_POST['next-btn'])) {
    if(!empty($_POST['first_name'])) {
        $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?m=19&s=warning");
        exit;
    }
    if(!empty($_POST['last_name'])){
        $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?m=20&s=warning");        
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
                header("Location: ".$_SERVER['PHP_SELF']."?m=6&s=warning");
                exit;
                    }

}

?>
{header}
{main_nav}
<main role="main" id="onboarding_page">
    <h1 class="mb-5 sticky-top">Your Profile</h1>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>"  method="post">
            <p class="text-center">First name and last name are REQUIRED even if you choose to skip the rest.</p>
            <section>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >First Name:</label>
                        <input  type="text"  required class="form-control" id="first_name" name="first_name"></input>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >Last Name:</label>
                        <input  type="text"  required class="form-control" id="last_name" name="last_name"></input>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city" class="col-form-label" >City:</label>
                        <input  type="text"  class="form-control" id="city" name="city"></input>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="state" class="col-form-label" >State:</label>
                        <input  type="text" class="form-control" id="state" name="state"></input>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="motto" class="col-form-label" >Motto:</label>
                        <textarea  class="form-control" id="motto" name="motto"></textarea>
                    </div>
                </div>
            </section>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" name="next-btn" value="Next"></input>
            </div>
        </form>
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
        header("Location: ".$_SERVER['PHP_SELF']."?m=19&s=warning");
        exit;
    }
    if(!empty($_POST['last_name'])){
        $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?m=20&s=warning");        
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
                header("Location: ".$_SERVER['PHP_SELF']."?m=6&s=warning");
                exit;
                    }

}

?>
{header}
{main_nav}
<main role="main" id="onboarding_page">
    <h1 class="mb-5 sticky-top">Your Profile</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="POST">
            <p class="text-center">Take a moment to fill out your profile. Your first and last name are required.</p>
            <section>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="first_name" class="col-form-label">First Name:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="last_name" class="col-form-label">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city" class="col-form-label">City:</label>
                        <input type="text" class="form-control" id="city" name="city">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="state" class="col-form-label">State:</label>
                        <input type="text" class="form-control" id="state" name="state">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="motto" class="col-form-label" >Motto:</label>
                        <textarea class="form-control" id="motto" name="motto"></textarea>
                    </div>
                </div>
            </section>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" name="next-btn" value="Next">
            </div>
        </form>
</main>
    {footer}
<?php ob_end_flush(); ?>
>>>>>>> 14e7f9a93d626aaa6123d7e70def889716eb9955
