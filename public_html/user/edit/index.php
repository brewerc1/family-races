<?php
/**
 * Page to Edit User Profile
 * 
 * This page mirrors the layout of the profile page and allows the user to edit
 * their profile data. 
 * DB is updated when the 'save' button is clicked.
 * If a photo is uploaded, the user's current photo is removed from /uploads/
 * as the new image is saved. Profile images are saved as 'user_id.file_type'.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Edit User Profile";

// include the menu javascript for the template
$javascript = '';

if (!isset($_SESSION["id"])){
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

///// DEBUG
$debug = debug();
///// end DEBUG

// logged in user
$user_id = filter_var(trim($_SESSION['id']), FILTER_SANITIZE_NUMBER_INT);
$update_time_stamp = strtotime($_SESSION['update_time']); // cache busting

// State Select Array
$state_array = array(	
    "AK" => "Alaska", "AL" => "Alabama", "AR" => "Arkansas", "AZ" => "Arizona",
    "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut",
    "DC" => "District of Columbia", "DE" => "Delaware", "FL" => "Florida",	
    "GA" => "Georgia", "HI" => "Hawaii", "IA" => "Iowa", "ID" => "Idaho",	
    "IL" => "Illinois", "IN" => "Indiana", "KS" => "Kansas", "KY" => "Kentucky",	
    "LA" => "Louisiana", "MA" => "Massachusetts", "MD" => "Maryland",	
    "ME" => "Maine", "MI" => "Michigan", "MN" => "Minnesota", "MO" => "Missouri",	
    "MS" => "Mississippi", "MT" => "Montana", "NC" => "North Carolina",	
    "ND" => "North Dakota", "NE" => "Nebraska",	"NH" => "New Hampshire",	
    "NJ" => "New Jersey", "NM" => "New Mexico",	"NV" => "Nevada", "NY" => "New York",	
    "OH" => "Ohio",	"OK" => "Oklahoma",	"OR" => "Oregon", "PA" => "Pennsylvania",	
    "PR" => "Puerto Rico", "RI" => "Rhode Island", "SC" => "South Carolina",	
    "SD" => "South Dakota",	"TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah",	
    "VA" => "Virginia", "VT" => "Vermont", "WA" => "Washington", "WI" => "Wisconsin",	
    "WV" => "West Virginia", "WY" => "Wyoming");

// Check if "save" button was clicked
if(isset($_POST['save_button'])){
    //User Photo Upload
    //TODO: Impliment Cropper or similar plugin
    $photo_value = $_SESSION['photo'];
    if ($_FILES['profile_photo']['error'] == 0 && isset($_FILES['profile_photo'])) {
        $name = $_FILES['profile_photo']['name'];
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        $target_file = $target_dir . basename($_FILES['profile_photo']['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $extensions_arr = array("jpg","jpeg","png","gif");

        if(in_array($image_file_type, $extensions_arr)){
            $unlink_result=unlink($_SERVER['DOCUMENT_ROOT'] . $photo_value);
            $debug = debug($unlink_result); 
            if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $user_id .".". $image_file_type))
            $photo_value = "/uploads/$user_id.$image_file_type"; 
        }
    }
    // First Name Text
    if(!isset($_POST['first_name'])){
        $first_name_value = filter_var(trim($_SESSION['first_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    } else {
        $first_name_value = filter_var(trim($_POST['first_name']),  FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // Last Name Text
    if(!isset($_POST['last_name'])){
       $last_name_value = filter_var(trim($_SESSION['last_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else { 
        $last_name_value = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // Motto Text
    if(!isset($_POST['motto'])){
        $motto_value = filter_var(trim($_SESSION['motto']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
        $motto_value = filter_var(trim($_POST['motto']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // Email Text
    if(!isset($_POST['email'])){
        $email_value = filter_var(filter_var(trim($_SESSION['email']), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
    } else {
        $email_value = filter_var(filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
    }

    // City Text
    if(!isset($_POST['city'])){
        $city_value = filter_var(trim($_SESSION['city']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
        $city_value = filter_var(trim($_POST['city']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // State Text
    if(isset($_POST['state']) && array_key_exists($_POST['state'], $state_array)){
        $state_value = filter_var(trim($_POST['state']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
        $state_value = filter_var(trim($_SESSION['state']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // PDO to update the DB 
    $update_preferences_sql = 
    'UPDATE user SET 
    first_name = :first_name_value, last_name = :last_name_value, motto = :motto_value,
    email = :email_value, city = :city_value, state = :state_value, photo = :photo_value 
    WHERE id = :user_id';

    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['first_name_value' => $first_name_value, 'last_name_value' => $last_name_value,
    'motto_value' => $motto_value, 'email_value' => $email_value, 'city_value' => $city_value, 'state_value' => $state_value,
    'user_id' => $user_id, 'photo_value' => $photo_value ]);
    
    //requery DB to update $_SESSION. Ensures $_SESSION is always in sync with DB.
    if ($update_preferences_result){    
        $update_session_sql = 
        "SELECT first_name, last_name, motto, email, city, state, photo, update_time
        FROM user WHERE id = :user_id";
        $update_session_result = $pdo->prepare($update_session_sql);
        $update_session_result->execute(['user_id' => $user_id]);
        $row = $update_session_result->fetch();
        $_SESSION['photo'] = $row['photo'];
        $_SESSION['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['motto'] = $row['motto'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['city'] = $row['city'];
        $_SESSION['state'] = $row['state'];
        $_SESSION['update_time'] = $row['update_time'];
        $update_time_stamp = strtotime($row["update_time"]);
        header("Location: /user/");
        exit;
    }
}

?>
{header}
{main_nav}
    <main role="main">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            <section class="form-row">
                <div class="form-group col">
                    <img class="rounded-circle" id="user_profile_photo" src="<?php echo "{$_SESSION['photo']}?$update_time_stamp" ?>" alt="My Photo">
                </div>
                <div id="photo_upload" class="form-group col-sm-8 d-flex">
                    <input class="d-inline" type="file" accept="image/*" class="form-control-file" id="profile_photo" name="profile_photo">
                </div>
            </section>

            <section id="user_meta">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >First Name:</label> 
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $_SESSION['first_name'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >Last Name:</label> 
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $_SESSION['last_name'] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email" class="col-form-label" >Email:</label> 
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email'] ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city" class="col-form-label" >City:</label> 
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $_SESSION['city'] ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="state" class="col-form-label" >State:</label> 
                        <select class="form-control" id="state" name="state">
                            <?php
                            foreach ($state_array as $key => $value) {
                                if($_SESSION['state'] == $key){
                                    $state_selected_tag = 'selected';
                                } else {
                                    $state_selected_tag = '';
                                }
echo <<<ENDOPTION
                                <option value="$key" $state_selected_tag>$value</option>
ENDOPTION;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="motto" class="col-form-label" >Motto:</label> 
                        <textarea class="form-control" id="motto" name="motto" rows="2"><?php echo $_SESSION['motto'] ?></textarea>
                    </div>
                </div>

            </section><!-- END user_meta -->
            <div class="form-row mt-5">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary btn col-sm-5" name="save_button">Save</button>
                    <a class="text-secondary d-block mt-2 text-center" href="/user/">Cancel</a>
                </div>
            </div>
        </form>
    </main>
{footer}
<?php ob_end_flush(); ?>
