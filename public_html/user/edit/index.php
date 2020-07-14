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

// logged in user
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$full_name = $_SESSION['first_name'].' '.$_SESSION['last_name'];
$photo = $_SESSION['photo'];
$motto = $_SESSION['motto'];
$email = $_SESSION['email'];
$city = $_SESSION['city'];
$state = $_SESSION['state'];
    

?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <form>
                <section id="user_head">
                    <div class="form-group" id="profile_photo">
                    <label for="profile_photo">
                        <img class="img-fluid" src="<?php echo $photo ?>" alt="User Photo"/></label>
                        <input type="file" class="form-control-file" id="profile_photo">
                    </div>
                    <div class="form-group" id="user_name"><?php  ?>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="<?php echo $_SESSION['first_name'] ?>">
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?php echo $_SESSION['last_name'] ?>">
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
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Motto:</th>
                                <td><input type="text" class="form-control" id="motto" name="motto" placeholder="<?php echo $_SESSION['motto'] ?>"></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><input type="text" class="form-control" id="email" name="email" placeholder="<?php echo $_SESSION['email'] ?>"></td>
                            </tr>
                            <tr>
                                <th>City:</th>
                                <td><input type="text" class="form-control" id="city" name="city" placeholder="<?php echo $_SESSION['city'] ?>"></td>
                            </tr>
                            <tr>
                                <th>State:</th>
                                <td><input type="text" class="form-control" id="state" name="state" placeholder="<?php echo $_SESSION['state'] ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </section><!-- END user_meta -->
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