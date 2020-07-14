<?php
/**
 * Page to Edit User Profile
 * 
 * Page Decription
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
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

///// DEBUG
$debug = debug("");
///// end DEBUG

// logged in user
$user_id = $_SESSION['id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$full_name = $_SESSION['first_name'].' '.$_SESSION['last_name'];
$photo = $_SESSION['photo'];
$motto = $_SESSION['motto'];
$email = $_SESSION['email'];
$city = $_SESSION['city'];
$state = $_SESSION['state'];

// State Select Array
$state_array = array(
    "AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL",
    "IN","KS","KY","LA","MA","MD","ME","MI","MN","MO","MS","MT","NC","ND","NE",
    "NH","NJ","NM","NV","NY","OH","OK","OR","PA","PR","RI","SC","SD","TN","TX",
    "UT","VT","WA","WI","WV","WY");

// Check if "save" button was clicked
if(isset($_POST['save_button'])){
    echo "<pre>";
    print_r($_FILES);
    var_dump($_POST);
    echo "</pre>";
    // TODO: User Photo Upload
    // this will look just like the upload a photo challenge
    if(!isset($_FILES['profile_photo'])){
        $photo_value = $_SESSION['photo'];
        echo "path1";
    } else {
        echo "path2";
        $name = $_FILES['profile_photo']['name'];
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        $target_file = $target_dir . basename($_FILES['profile_photo']['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $extensions_arr = array("jpg","jpeg","png","gif");

        if(in_array($image_file_type, $extensions_arr)){
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $user_id);
            $photo_value = "/uploads/$name"; 
        }
    }
    // First Name Text
    if(!isset($_POST['first_name'])){
        $first_name_value = $_SESSION['first_name']; 
    } else {
        $first_name_value = htmlentities($_POST['first_name'], ENT_QUOTES);
    }

    // Last Name Text
    if(!isset($_POST['last_name'])){
       $last_name_value = $_SESSION['last_name'];
    } else {
        //$last_name_value = htmlentities($_POST['last_name'], ENT_QUOTES);
        $last_name_value = filter_var( $_POST['last_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //$last_name_value = $_POST['last_name'];
    }

    // Motto Text
    if(!isset($_POST['motto'])){
        $motto_value = $_SESSION['motto'];
    } else {
        $motto_value = htmlentities($_POST['motto'], ENT_QUOTES);
    }

    // Email Text
    if(!isset($_POST['email'])){
        $email_value = $_SESSION['email'];
    } else {
        $email_value = filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
    }

    // City Text
    if(!isset($_POST['city'])){
        $city_value = $_SESSION['city'];
    } else {
        $city_value = htmlentities($_POST['city'], ENT_QUOTES);
    }

    // State Text
    if(isset($_POST['state']) && in_array($_POST['state'], $state_array)){
        $state_value = htmlentities($_POST['state'], ENT_QUOTES);
    } else {
        $state_value = $_SESSION['state'];
    }



    // PDO to update the DB 
    $update_preferences_sql = 
    'UPDATE user SET 
    photo = :photo_value, first_name = :first_name_value, last_name = :last_name_value,
    motto = :motto_value, email = :email_value, city = :city_value, state = :state_value
    WHERE id = :user_id';

    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['first_name_value' => $first_name_value, 'last_name_value' => $last_name_value,
    'motto_value' => $motto_value, 'email_value' => $email_value, 'city_value' => $city_value, 'state_value' => $state_value,
    'user_id' => $user_id, 'photo_value' => $photo_value ]);
    
    //requery DB to update $_SESSION. Ensures current settings are always displayed
    if ($update_preferences_result){    
    $update_session_sql = 
    "SELECT first_name, last_name, motto, email, city, state, photo
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
    }
}

?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <form action="./index.php" method="post" enctype="multipart/form-data">
                <section id="user_head">
                    <div class="form-group row">
                        <div class="col-auto" id="profile_photo">
                            <label for="profile_photo">
                                <img class="img-fluid" src="<?php echo $photo ?>" alt="User Photo"/>
                            </label>
                            <input type="file" class="form-control-file" id="profile_photo" name="profile_photo">
                        </div>
                        <div class="col-auto" id="user_name">
                            <label for="first_name" class="col-form-label sr-only">First Name </label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $_SESSION['first_name'] ?>">
                            <label for="last_name" class="col-form-label sr-only">First Name </label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $_SESSION['last_name'] ?>">
                        </div>
                    </div>
                    <!-- Links not displayed if "logged in" == "displayed" -->  
                    <?php
                    if (!isset($_GET["u"]) || ($_GET["u"] == $_SESSION["id"])) {
echo <<< LINKS
                    <div id="edit_buttons">
                        <a href="../index.php" class="btn btn-primary btn-sm active" id="edit_profile">Edit Profile</a> 
                        <a href="../settings/" class="btn btn-primary btn-sm disabled" id="user_settings">User Settings</a>
                    </div>
LINKS;
                            }
                    ?>         
                </section> <!-- END user_head -->

                <section id="user_meta">
                    <!-- trying to just use a form row to mimic the displayed table  -->
                    <div class="form-group row">
                        <div class="col-auto">
                            <label for="motto" class="col-form-label" >Motto:</label> 
                            <input type="text" class="form-control" id="motto" name="motto" value="<?php echo $_SESSION['motto'] ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-auto">
                            <label for="email" class="col-form-label" >Email:</label> 
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email'] ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-auto">
                            <label for="city" class="col-form-label" >City:</label> 
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo $_SESSION['city'] ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-auto">
                            <label for="state" class="col-form-label" >State:</label> 
                            <select class="form-control" id="state" name="state">
                                <?php
                                foreach ($state_array as $value) {
                                    if($_SESSION['state'] == $value){
                                        $state_selected_tag = 'selected';
                                    } else {
                                        $state_selected_tag = '';
                                    }
echo <<<ENDOPTION
                                    <option value="$value" $state_selected_tag>$value</option>
ENDOPTION;
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                </section><!-- END user_meta -->

                <button type="submit" class="btn btn-primary btn-block" name="save_button">Save</button> <a href="../index.php" >Cancel</a>
           
            </form>
            <section id="user_records">
                <h1>Keenland Records</h1>
                <table class="table">
                    <tbody>
                        <tr>
                            <!-- TODO: Need to create 'records' field in user table. -->                  
                            <td><?php //echo $row['records'] ?></td>
                            <td>Reunion 2022: 9th place</td> <!-- Placeholder -->
                        </tr>
                    </tbody>
                </table>
            </section> <!-- END user_records -->
        </div> <!-- END container -->
    </main>
{footer}
<?php ob_end_flush(); ?>