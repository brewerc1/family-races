<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');


// SQL to fetch user data

$display_user_sql = "SELECT id, first_name, last_name, photo, email, invite_code FROM user";
$display_user_result = $pdo->prepare($display_user_sql);
$display_user_result->execute();
$num_display_user_results = $display_user_result->rowCount();
$row = $display_user_result->fetch();

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
             <?php

                if ($num_display_user_results > 0) {
                    $invited = "";

                    // loop through DB return
                    while($row = $display_user_result->fetch()) {

                        // handle user with invite but hasn't accepted
                        if(!empty($row["invite_code"])) {
                            $invited = "<span class='invited_chip'>pending</span>";
                            $name = $row["email"];
                        } else {
                            $name = $row["first_name"] . ' ' . $row["last_name"];
                        }
                        // handle missing photo
                        if(empty($row["photo"])) {
                            $photo = "https://races.informatics.plus/images/no-user-image.jpg";
                        } else {
                            $photo = $row["photo"];
                        }

                        // output row of user data
echo <<< ENDUSER
            <div class="user-row">
                <a href="../user/user_profile.php?u={$row["id"]}"><img src="{$photo}" alt="photo"></a><span>{$name}</span> {$invited}
            </div>

ENDUSER;
                    } 
                } else {
                    echo "0 results";
                }         

                ?>  

            </div>
        </section>
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>>
    </footer>
</body>
</html>