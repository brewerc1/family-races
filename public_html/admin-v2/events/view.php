<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Manage Event";

$debug = debug($_GET);

if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

if ($_SESSION["admin"] != 1) {
    header("Location: /races/");
    exit;
}

$eventId = (int) $_GET["e"];
$query = "SELECT name, pot, date FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(['eventId' => $eventId]);
$data = $stmt->fetch();
$eventName = $data["name"];
$eventPot = $data["pot"];
$eventDate = $data["date"];

?>

{header}
{main_nav}

<main role="main" id="admin_main_events_page">
    <div class="floating-alert alert  alert-dismissible fade show fixed-top mt-5 mx-4 d-none" role="alert" id="alert">
        <span id="msg">messages will be display here.</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <h1 class="sticky-top padded-top-header mb-5" id="manage-events-page-header">
        <a href="../events/" class="font-lighter">Events</a>
        <span class="font-lighter"> > </span>
        <span id="event-name"> <?php echo $eventName; ?></span>
    </h1>

    <form id="create-event" method="POST">
        <section>
            <!-- Event Name -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-horse-head"></i></span>
                        </div>
                        <input type="text" class="form-control" id="name" name="event_name" maxlength="25" disabled="true" value="<?php echo $eventName ?>">
                    </div>
                </div>
                <!-- Event Date -->
                <div class="form-group col-md-6">
                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-auto-close="true" data-date-today-highlight="true" data-date-orientation="auto" data-date-z-index-offset="2000" data-date-clear-btn="true" data-date-today-btn="true">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" id="date" name="event_date" disabled="true" value="<?php echo $eventDate ?>">
                    </div>
                </div>
            </div>
            <!-- Event POT -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-donate"></i></span>
                        </div>
                        <input type="number" class="form-control" id="pot" name="event_pot" min="1" max="9999" disabled="true" value="<?php echo $eventPot ?>">
                    </div>
                </div>
            </div>
        </section>
    </form>

    <div class="board" id="app">
        <h2 class="mt-6" v-if="eventHasRaces">Races In This Event</h2>
        <h2 class="mt-6" v-else>No races in this event.</h2>



        <div id="scoreboard" v-show="eventHasRaces">
            <h2></h2>
            <div class="board-wrapper contain-width">
                <a class="mr-4 font-weight-bold control-btn btn" :class="{'disabled dim': raceId == 1 }" @click="previousRace()">
                    <span>
                        < </span>
                </a>
                <div id="message">
                    <table class="table table-borderless scoreboard">
                        <thead>
                            <tr id="title_row">
                                <td colspan="4"><img src="/images/kc-logo-white.svg" alt="Family Races logo">
                                    Race {{ raceId }} Results
                                </td>
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
                                    <input v-for="horse in horses" :key="horse.id" v-if="horse.id==enteredResults.win" :value="horse.horse_number" disabled="true" style="color: orange !important;" />
                                </td>
                                <td class="position-relative">
                                    <input class="w-100" v-model="enteredResults.win_purse[0]" disabled="true" style="color: orange !important;">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.win_purse[1]" disabled="true" style="color: orange !important;">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.win_purse[2]" disabled="true" style="color: orange !important;">
                                </td>
                            </tr>
                            <!-- Place -->
                            <tr>
                                <td>
                                    <input v-for="horse in horses" :key="horse.id" v-if="horse.id==enteredResults.place" :value="horse.horse_number" disabled="true" style="color: orange !important;" />
                                </td>
                                <td class="board-blank"></td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.place_purse[0]" disabled="true" style="color: orange !important;">
                                </td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.place_purse[1]" disabled="true" style="color: orange !important;">
                                </td>

                            </tr>
                            <!-- Show -->
                            <tr>
                                <td>
                                    <input v-for="horse in horses" :key="horse.id" v-if="horse.id==enteredResults.show" :value="horse.horse_number" disabled="true" />
                                </td>
                                <td class="board-blank"></td>
                                <td class="board-blank"></td>
                                <td>
                                    <input class="w-100" v-model="enteredResults.show_purse[0]" disabled="true" style="color: orange !important;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="mr-4 font-weight-bold control-btn btn" :class="{'disabled dim': raceId == lastRace }" @click="nextRace()">
                    <span>
                        > </span>
                </a>
            </div>
        </div>
        <div class="mt-2 mb-4 contain-width" v-show="eventHasRaces">
            <div class="edit-race-wrap">
                <a class="btn black-btn" v-if="eventHasRaces" :href="editRaceURL">Edit this race</a>
            </div>
            <h2 class="mb-2">Horses in this race</h2>
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

    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="js/viewEvent.js"></script>
</main>


{footer}