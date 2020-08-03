<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Events";

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}
$debug = debug();

?>
{header}
{main_nav}

    <main role="main">
        <section>
            <h1>Events</h1>
            <ul class="list-unstyled text-center mt-5">
                <li><a class="btn btn-primary mb-4" href="./create.php">Create New Event</a></li>
            </ul>

            <h2>Previous Events</h2>
                <ul class="list-unstyled mt-3 text-center">
                    <?php
                    $query = "SELECT id, name, status FROM event ORDER BY id DESC";
                    $events = $pdo->prepare($query);
                    if ($events->execute()) {
                        if ($events->rowCount() > 0) {
                            $row = $events->fetchAll();
                            $index = 0;
                            while ($index < count($row)) {
                                $event_id = $row[$index]["id"];
                                $event_name = $row[$index]["name"];
                                $event_status = $row[$index]["status"];

                                $completed = "";

                                if ($event_status) {
                                    $completed = "<span class='badge badge-primary badge-pill' id='invited_badge'>completed</span>";
                                    echo "<li>$event_name $completed</li>";
                                } else {
                                    echo "<li><a href=\"./manage.php?e=$event_id\">$event_name</a> </li>";
                                }
                                $index++;
                            }
                        } else {
                            echo "<li>There is no event.</li>";
                        }
                    } else {

                        echo "<li>Something went wrong, <span>please logout and log back in</span>.</li>";
                    }

                    ?>
                </ul>

        </section>
    </main>

{footer}
<?php ob_end_flush(); ?>
