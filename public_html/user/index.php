<?php
/**
 * Page to display User Profile
 * 
 * This page is used to display any user profile.
 * Logged in users have access to "edit" and "settings" links.
 * User data for logged in user is stored in $_SESSION.email
 * Page checks for $_GET
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "User Profile";

// include the menu javascript for the template
$javascript = '';

if (!isset($_SESSION["id"])) {
    header("Location: /login/");

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");

}

// logged in user
$full_name = $_SESSION['first_name'].' '.$_SESSION['last_name'];
$photo = $_SESSION['photo'];
$motto = $_SESSION['motto'];
$email = $_SESSION['email'];
$city = $_SESSION['city'];
$state = $_SESSION['state'];

// get selected UID: Don't run if the GET["u"] is SESSION["id"]
// TODO: protect this better
if (isset($_GET["u"]) && ($_GET["u"] != $_SESSION["id"])) {
    $display_uid = $_GET["u"]; // Replace 1 with $_GET['u']
    $display_user_sql = "SELECT * FROM user WHERE id = :display_uid";
    $display_user_result = $pdo->prepare($display_user_sql);
    $display_user_result->execute(['display_uid' => $display_uid]);
    $num_display_user_results = $display_user_result->rowCount();
    $row = $display_user_result->fetch();

    $full_name = $row['first_name'].' '.$row['last_name'];
    $photo = $row['photo'];
    $motto = $row['motto'];
    $email = $row['email'];
    $city = $row['city'];
    $state = $row['state'];
}
 
// Links to "edit" and "settings" page
$settings_link = "<a href='./settings/?u=" . $_SESSION['id'] . "' id='settings_link' class='button'>User Settings</a>";
$edit_link = "<a href='./edit/?u=" . $_SESSION['id'] . "' id='edit_link' class='button'>Edit Profile</a> "    

?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <section id="user_head">
                <div id="profile_photo">
                    <img class="img-responsive" src="<?php echo $photo ?>" alt="User Photo"/>
                </div>
                <div id="user_name"><?php echo $full_name ?></div>
                <!-- Links not displayed if "logged in" == "displayed" -->  
                <?php
                if (!isset($_GET["u"]) || ($_GET["u"] == $_SESSION["id"])) {
echo <<< LINKS
                <div id="edit_buttons">
                    <a href="./edit" class="btn btn-primary btn-sm" id="edit_profile">Edit Profile</a> 
                    <a href="./settings/" class="btn btn-primary btn-sm" id="user_settings">User Settings</a>
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
                            <td><?php echo $motto ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $email ?></td>
                        </tr>
                        <tr>
                            <th>City:</th>
                            <td><?php echo $city ?></td>
                        </tr>
                        <tr>
                            <th>State:</th>
                            <td><?php echo $state ?></td>
                        </tr>
                    </tbody>
                </table>
            </section><!-- END user_meta -->
                
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