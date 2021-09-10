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


$eventId = (int) $_GET["e"];
$query = "SELECT name FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(['eventId'=>$eventId]);
$eventName = $stmt->fetch()["name"];

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
        <a id="event-name" class="font-lighter"
            href="../events/manage.php?<?php echo "e=".$_GET["e"]."&r=".$_GET["r"]."&pg=".$_GET["pg"];?>"
        >
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
        <div id="scoreboard" >
            <h2>Race <?php echo $_GET["r"]; ?> Results</h2>
            <div class="board-wrapper contain-width">
                <a 
                  class="mr-4 font-weight-bold control-btn btn"
                  :class="{'disabled dim': raceId==1}"
                  :href="previousRace"
                >
                    <span><</span>
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
                                    <select
                                        class="race-result w-100"
                                        v-model="enteredResults.win"
                                        @change="updateResults()"
                                    >
                                        <option
                                            v-for="horse in availableWinHorses" 
                                            :key="horse.id"
                                            :label="horse.horse_number"
                                            :value="horse.id"
                                        >
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="position-relative">
                                    <input 
                                        class="w-100"
                                        v-model="enteredResults.win_purse[0]"
                                        @change="updateResults()"
                                    >
                                </td>
                                <td>
                                    <input
                                        class="w-100"
                                        v-model="enteredResults.win_purse[1]"
                                        @change="updateResults()"
                                    >
                                </td>
                                <td>
                                    <input
                                        class="w-100"
                                        v-model="enteredResults.win_purse[2]"
                                        @change="updateResults()"
                                    >
                                </td>
                            </tr>
                            <!-- Place -->
                            <tr>
                                <td>
                                    <select
                                        class="race-result w-100"
                                        v-model="enteredResults.place"
                                        @change="updateResults()"
                                    >
                                        <option
                                            v-for="horse in availablePlaceHorses" 
                                            :key="horse.id"
                                            :label="horse.horse_number"
                                            :value="horse.id"
                                        >
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="board-blank"></td>
                                <td>
                                    <input
                                        class="w-100"
                                        v-model="enteredResults.place_purse[0]"
                                        @change="updateResults()"
                                    >
                                </td>
                                <td>
                                    <input 
                                        class="w-100"
                                        v-model="enteredResults.place_purse[1]"
                                        @change="updateResults()"
                                    >
                                </td>

                            </tr>
                            <!-- Show -->
                            <tr>
                                <td>
                                    <select
                                        class="race-result w-100"
                                        v-model="enteredResults.show"
                                        @change="updateResults()"
                                    >
                                        <option
                                            v-for="horse in availableShowHorses" 
                                            :key="horse.id"
                                            :label="horse.horse_number"
                                            :value="horse.id"
                                        >
                                            {{ horse.horse_number }}
                                        </option>
                                    </select>
                                </td>
                                <td class="board-blank"></td>
                                <td class="board-blank"></td>
                                <td>
                                    <input 
                                        class="w-100"
                                        v-model="enteredResults.show_purse[0]"
                                        @change="updateResults()"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="ml-4 font-weight-bold control-btn btn"
                    :href="nextRace"
                    :class="{'disabled dim': nextRace==''}"
                >
                    <span>></span>
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