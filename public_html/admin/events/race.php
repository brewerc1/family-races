<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";
$javascript = "";

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

if (!isset($_GET["r"]) && !isset($_GET["q"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$request_array = array(
    1 => "window_closed",
    2 => "cancelled"
);

$update = array(
    3 => "update"
);

$delete = array(
    4 => "Delete"
);

/**
 * @param $int
 * @return mixed
 */
function validateInt($int) {
    $int_options = array("options" =>
        array("min_range" =>1 ));

    $in = filter_var(trim($int), FILTER_SANITIZE_NUMBER_INT);
    return filter_var($in, FILTER_VALIDATE_INT, $int_options);
}

$q = validateInt($_GET["q"]);
$race_number = validateInt($_GET["r"]);

if (key_exists($q, $request_array)) {

    $query = "SELECT ". $request_array[$q] ." FROM race WHERE race_number = :race_number";
    $race = $pdo->prepare($query);
    if ($race->execute(['race_number' => $race_number])) {
        if ($race->rowCount() > 0) {
            $respond = $race->fetch()[$request_array[$q]];
            $respond = $respond ? 0 : 1;

            $update_query = "UPDATE race SET ". $request_array[$q] ." = :respond WHERE race_number = :race_number";
            $stmt = $pdo->prepare($update_query);
            if ($stmt->execute(["respond" => $respond, 'race_number' => $race_number])) {

                $query = "SELECT ". $request_array[$q] ." FROM race WHERE race_number = :race_number";
                $race = $pdo->prepare($query);
                if ($race->execute(['race_number' => $race_number])) {
                    if ($race->rowCount() > 0) {
                        $respond = $race->fetch()[$request_array[$q]];
                        if ($q == 1) {
                            if ($respond) {
echo <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> betting window is closed.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
                            } else {
echo <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> betting window is opened.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
                            }
                        } elseif ($q == 2) {
                            if ($respond) {
                                echo <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> is canceled.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
                            } else {
                                echo <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> is uncancelled.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
                            }
                        }
                    }
                }

            }
        }

    }
} else {

    if (!isset($_GET["e"])) {
        header("HTTP/1.1 401 Unauthorized");
        // An error page
        //header("Location: error401.php");
        exit;
    }
    $event_id = validateInt($_GET["e"]);

    if (key_exists($q, $update)) {

        $alert = <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> is updated.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;


        if (isset($_POST["horse_array"])) {

            $query = "DELETE FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
            $horses = $pdo->prepare($query);
            if ($horses->execute(["race_event_id" => $event_id, "race_race_number" => $race_number])) {
                //, win_purse, place_purse, show_purse
                $query = "INSERT INTO horse ( race_event_id, race_race_number, horse_number ) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($query);

                $horses = $_POST["horse_array"];
                foreach ($horses as $horse) {
                    if (!empty($horse)) {
                        $horse = filter_var($horse, FILTER_SANITIZE_STRING);
                        $success = $stmt->execute([$event_id, $race_number, $horse]);
                    }
                }

                if ($success) echo $alert;

            }

        }
    } elseif (key_exists($q, $delete)) {
echo <<< HTML
        <div class="floating-alert alert alert-success alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          <strong>Race $race_number</strong> is deleted.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
    }
}
