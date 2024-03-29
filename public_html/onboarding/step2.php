<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// Set the page title for the template
$page_title = "Your Profile";

$debug = debug($_POST);

if (empty($_SESSION["id"])){
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
    if(!empty($_POST['state']) && array_key_exists($_POST['state'], $state_array)){
        $state = (trim($_POST['state']));
    } else{
        $state = "";
    }
    if(!empty($_POST['motto'])){
        $motto = filter_var(trim($_POST['motto']), FILTER_SANITIZE_STRING);
    } else {
        $motto = "";
    }
    $update_user_sql = "UPDATE user SET first_name = :first_name, last_name = :last_name, city =:city, state = :state, motto= :motto WHERE id = {$_SESSION['id']}";
    $update_user_query = $pdo->prepare($update_user_sql);
    $update_user_query->execute([
		'first_name' => $first_name,
		'last_name' => $last_name, 
		'city' => $city, 
		'state' => $state, 
		'motto'=> $motto]
	);
	if ($update_user_query) {
		//Update Session Variables
		$update_user_session_sql = "SELECT * FROM user WHERE id = {$_SESSION['id']}";
		$update_user_session_query = $pdo->prepare($update_user_session_sql);
		$update_user_session_query->execute();
		$update_user_session_results = $update_user_session_query->fetch();
		$_SESSION['first_name'] = $update_user_session_results['first_name'];
		$_SESSION['last_name'] = $update_user_session_results['last_name'];
		$_SESSION['city'] = $update_user_session_results['city'];
		$_SESSION['state'] = $update_user_session_results['state'];
		$_SESSION['motto'] = $update_user_session_results['motto'];
		$_SESSION['photo'] = $update_user_session_results['photo'];
		header('Location:/onboarding/step3.php');
			exit;
	} else {
		header("Location: {$_SERVER['PHP_SELF']}?m=6&s=warning");
		exit;
	}
}
?>
{header}
{main_nav}
<main role="main" id="onboarding_page">
    <h1 class="mb-5 sticky-top">Your Profile</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <p class="text-center">Take a moment to fill out your profile. Your first and last name are required.</p>
            <section>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="first_name" class="col-form-label">First Name:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required maxlength="40">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="last_name" class="col-form-label">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required maxlength="40">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city" class="col-form-label">City:</label>
                        <input type="text" class="form-control" id="city" name="city" maxlength="40">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="state" class="col-form-label">State:</label>
                        <select class="form-control" id="state" name="state">
                        <option disabled selected>Choose one</option>
                            <?php
                            foreach ($state_array as $key => $value) {
echo <<<ENDOPTION
                                <option value="$key">$value</option>\n
ENDOPTION;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="motto" class="col-form-label" >Motto:</label>
                        <textarea class="form-control" id="motto" name="motto" maxlength="200"></textarea>
                    </div>
                </div>
            </section>
            <div class="text-center">
                <input type="submit" class="btn btn-primary mb-4" name="next-btn" value="Next">
            </div>
        </form>
</main>
{footer}
<?php ob_end_flush(); ?>
