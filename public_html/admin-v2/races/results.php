<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Events";

if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

if ($_SESSION["admin"] != 1) {
    header("Location: /races/");
    exit;
}

// Start Server Side Rendering Logic
$eventId = (int) $_GET["e"];
$currentRace = (int) $_GET["r"];

// Get Event Name
$query = "SELECT name FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(['eventId' => $eventId]);
$eventName = $stmt->fetch()["name"];

// Get next race that is closed
$query = "SELECT race_number FROM race WHERE race_number > :currentRace AND event_id = :eventId AND window_closed = 1 ORDER BY race_number ASC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentRace' => $currentRace, "eventId" => $eventId]);
$nextRace = $stmt->fetch();
$nextRaceId = -1;

if (isset($nextRace["race_number"])) {
    $nextRaceId = $nextRace["race_number"];
}

// Get previous race that is closed
$query = "SELECT race_number FROM race WHERE race_number < :currentRace AND event_id = :eventId AND window_closed = 1 ORDER BY race_number DESC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentRace' => $currentRace, "eventId" => $eventId]);
$prevRace = $stmt->fetch();
$prevRaceId = -1;

if (isset($prevRace["race_number"])) {
    $prevRaceId = $prevRace["race_number"];
}

$nextRaceURL = "";
$prevRaceURL = "";

if ($nextRaceId != -1) {
    $nextRacePg = ($nextRaceId / 10) + 1;
    $nextRaceURL = "/admin-v2/races/results.php?e={$eventId}&r={$nextRaceId}&pg=1";
}

if ($prevRaceId != -1) {
    $prevRacePage = ($prevRaceId / 10) + 1;
    $prevRaceURL = "/admin-v2/races/results.php?e={$eventId}&r={$prevRaceId}&pg=1";
}
// End Server Side Rendering Logic

// If this is a past event, add query param to let Vue know
$query = "SELECT status FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(["eventId" => $eventId]);
$event = $stmt->fetch();

if (isset($event["status"]) && !isset($_GET["status"])) {
    header('Location: ' . $_SERVER["REQUEST_URI"] . "&status=" . $event["status"]);
}

?>

{header}
{main_nav}
<main role="main" id="app">
    <!-- Alerts -->
    <div class="alert alert-success custom-alert" role="alert" v-show="showSuccessAlert">
        Results Saved Successfully.
    </div>
    <div class="alert alert-danger custom-alert" role="alert" v-show="showFailureAlert">
        Failed to Save Results.
    </div>
    <!-- End Alerts -->
    <h1 class="sticky-top padded-top-header pl-4 mb-5 text-left header-blurred">
        <a href="../events/" class="font-lighter">Events</a>
        <span class="font-lighter"> > </span>
        <a id="event-name" class="font-lighter" href="../events/manage.php?<?php echo "e=" . $_GET["e"] . "&r=" . $_GET["r"] . "&pg=" . $_GET["pg"]; ?>">
            <?php echo $eventName ?>
        </a>
        <span class="font-lighter"> > </span>
        <span id="race-name">Race <?php echo $_GET["r"] ?> </span>
    </h1>
    <section id="race" class="mt-5" style="max-width: 1000px">
        <div id="race-loader" v-if="loading">
            <div id="loader-container">
                <div class="lds-ring" id="loader">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        <div id="scoreboard">
            <h2>Race <?php echo $_GET["r"]; ?> Results</h2>
            <div class="board-wrapper contain-width">
                <a class="mr-4 font-weight-bold control-btn btn
                  <?php echo $prevRaceId == -1 ? 'disabled dim' : ''; ?>" href="<?php echo $prevRaceURL ?>">
                    <span>
                        < </span>
                </a>
                <div id="message">
                    <table class="table table-borderless scoreboard">
                        <thead>
                            <tr id="title_row">
                                <td colspan="4"><img src="/images/kc-logo-white.svg" alt="Family Races logo">Family Races</td>
                            </tr>
                            <tr>
                                <th scope="col">Horse#</th>
                                <th scope="col">Win</th>
                                <th scope="col">Place</th>
                                <th scope="col">Show</th>
                            </tr>
                        </thead>
                        <!-- Winner -->
                        <tbody>
                            <tr>
                                <td>
                                    <select class="race-result w-100" v-model="enteredResults.win" @change="updateResults()">
                                        <option v-for="horse in availableWinHorses" :key="horse.id" :value="horse.id">
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="position-relative">
                                    <input class="w-100" v-model="enteredResults.win_purse[0]" @change="updateResults()">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.win_purse[1]" @change="updateResults()">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.win_purse[2]" @change="updateResults()">
                                </td>
                            </tr>
                            <!-- Place -->
                            <tr>
                                <td>
                                    <select class="race-result w-100" v-model="enteredResults.place" @change="updateResults()">
                                        <option v-for="horse in availablePlaceHorses" :key="horse.id" :value="horse.id">
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="board-blank"></td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.place_purse[0]" @change="updateResults()">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.place_purse[1]" @change="updateResults()">
                                </td>

                            </tr>
                            <!-- Show -->
                            <tr>
                                <td>
                                    <select class="race-result w-100" v-model="enteredResults.show" @change="updateResults()">
                                        <option v-for="horse in availableShowHorses" :key="horse.id" :value="horse.id">
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="board-blank"></td>
                                <td class="board-blank"></td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.show_purse[0]" @change="updateResults()">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="mr-4 font-weight-bold control-btn btn
                  <?php echo $nextRaceId == -1 ? 'disabled dim' : ''; ?>" href="<?php echo $nextRaceURL ?>">
                    <span>
                        > </span>
                </a>
            </div>
        </div>
        <div class="mt-4 mb-4 contain-width">
            <h2>Horses in this race</h2>
            <div v-for="horse in sortedHorses" :key="horse.id" class="pb-4 font-weight-bold">

                <span v-if="horse.id === enteredResults.win" class="gold">
                    <i class="fas fa-trophy"></i>
                </span>
                <span v-else-if="horse.id === enteredResults.place" class="silver">
                    <i class="fas fa-trophy"></i>
                </span>
                <span v-else-if="horse.id === enteredResults.show" class="bronze">
                    <i class="fas fa-trophy"></i>
                </span>
                {{ horse.horse_number }}
            </div>
        </div>
    </section>
</main>
{footer}
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script type="text/javascript" src="./js/results.js"></script>
<?php ob_end_flush(); ?>