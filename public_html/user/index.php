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

// TODO: Check session variable for logged in user. 

// Links to "edit" and "settings" page
$settings_link = "<a href='./settings/?u=" . $_SESSION['id'] . "' id='settings_link' class='button'>User Settings</a>";
$edit_link = "<a href='./edit/?u=" . $_SESSION['id'] . "' id='edit_link' class='button'>Edit Profile</a> "    


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Skeleton HTML</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        nav#main-navigation li {
            display: inline-block;
            width: 18%;
        }
        nav#main-navigation ul {
            margin:0;
            padding:0;
        }
    </style>
</head>
<body>
<!--The main navigation menu to be displayed on most pages. Not all links work yet.-->
<nav id="main-navigation">
    <h1>Main Navigation</h1>
    <ul>
        <li><a href="http://localhost/races">Races</a></li>
        <li><a href="http://localhost/HOF/">HOF</a></li>
        <li><a href="http://localhost/faq/">FAQ</a></li>
        <li><a href="http://localhost/user/">Me</a></li>
        <?php
        if ($_SESSION['admin']) {
            echo <<< ADMIN
<li><a href= "http://localhost/admin/">Admin</a></li>
ADMIN;
        }
        ?>
        <li><a href="http://localhost/logout">Log out</a></li>
    </ul>
</nav>
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

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>