<?php
/**
 * Page to Display User Profile
 * 
 * This page displays the profile of a selected user.
 * The user ID of the correct user is recieved with $_GET['u'].
 * Page also check if the logged in user is the displayed user, enabling the "edit" and "settings links.
 */


require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// get selected UID
$display_uid = $_GET['u']; // Replace 1 with $_GET['u']
$display_user_sql = "SELECT * FROM user WHERE id = :display_uid";
$display_user_result = $pdo->prepare($display_user_sql);
$display_user_result->execute(['display_uid' => $display_uid]);
$num_display_user_results = $display_user_result->rowCount();
$row = $display_user_result->fetch();

// TODO: interact with session variables to determine logged in user, if user is admin, maintain session, etc.

// TODO: Check session variable for logged in user. If "logged in" == "displayed", enable links to "settings" and "edit"

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>User Profile</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <nav id="main-navigation">
        <h1>Main Navigation</h1>
        <ul>
            <li>Menu 1</li>
            <li>Menu 2</li>
            <li>Menu 3</li>
            <li>Menu 4</li>
            <li>Menu 5</li>
        </ul>
    </nav>
    <main role="main">
        <section id="user_info">
            <div>
            <img src="<?php echo $row['photo'] ?>" alt="User Photo"/> 
            <!-- Links not displayed if "logged in" == "displayed" -->  
            <a href="./edit" class="button">Edit Profile</a> 
            <a href="./settings/?u=<?php echo $display_uid?>" class="button">User Settings</a> 
            <p><?php echo $row['first_name'].' '.$row['last_name'] ?> </p>
            </div>
            <div>
                <p>MOTTO: <?php echo $row['motto'] ?></p>
                <p>EMAIL: <?php echo $row['email']  ?></p>
                <p>CITY: <?php echo $row['city'] ?></p>
                <p>STATE: <?php echo $row['state'] ?></p>

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