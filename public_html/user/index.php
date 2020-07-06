<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// logged in user
$full_name = $_SESSION['first_name'].' '.$_SESSION['last_name'];
$photo = $_SESSION['photo'];
$motto = $_SESSION['motto'];
$email = $_SESSION['email'];
$city = $_SESSION['city'];
$state = $_SESSION['state'];

// get selected UID: Don't run if the GET["u"] is SESSION["id"]
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

// TODO: interact with session variables to determine logged in user, if user is admin, maintain session, etc.

// TODO: Check session variable for logged in user. If "logged in" == "displayed", enable links to "settings" and "edit"


include '../template/header.php';
?>
    <main role="main">
        <section id="user_info">
            <div>
            <img src="<?php echo $photo ?>" alt="User Photo"/>
            <!-- Links not displayed if "logged in" == "displayed" -->  
            <?php
            if (!isset($_GET["u"]) || ($_GET["u"] == $_SESSION["id"])) {
echo <<< LINKS
<a href="./edit" class="button">Edit Profile</a> 
<a href="./settings/" class="button">User Settings</a> 
LINKS;
            }
            ?>
            <p><?php echo $full_name ?> </p>
            </div>
            <div>
                <p>MOTTO: <?php echo $motto ?></p>
                <p>EMAIL: <?php echo $email  ?></p>
                <p>CITY: <?php echo $city ?></p>
                <p>STATE: <?php echo $state ?></p>

            </div>
        </section>
        
        <section id="user_records">
            <h1>Keenland Records</h1>
            <p><?php //echo $row['records'] ?></p>
        </section> <!-- END user_records -->
    </main>

<?php
include '../template/footer.php';
?>