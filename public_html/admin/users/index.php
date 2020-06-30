<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/family-races/public_html/bootstrap.php');
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>Admin User Management Page</title>

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
        <section>
            <h1>User Management</h1>
            <form method="post" action="../admin/invite.php" id="invite_form">
                <input type="email" name="email_to_invite" placeholder="Invite a New User" required>
                <button type="submit" form="invite_form" name="send_email_btn" value="Submit">+</button>
            </form>
        </section>
        
        <section>
            <h2>Current Users</h2>
            <div class="user-row">
                <a href="../user/user_profile.php?u=1"><img src=" " alt="photo"></a><span>User's Name</span> Invited
            </div>
        </section>
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>>
    </footer>
</body>
</html>