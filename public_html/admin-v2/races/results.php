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
?>

{header}
{main_nav}
<main role="main" id="app">
    <!-- May need to change ids -->
    <h1 class="sticky-top padded-top-header pl-4 mb-5" id="manage-race-page-header">
		<a href="../events/" class="font-lighter">Events</a>
		<span class="font-lighter"> > </span> 
		<a id="event-name" class="font-lighter" :href="`../events/manage.php?e=${race.event_id}&r=${race.race_number}&pg=<?php echo $_GET["pg"]; ?>`">{{ event.name }}</a>
        <span class="font-lighter"> > </span> 
        <span id="race-name">Race <?php echo $_GET["r"] ?> </span> 
	</h1>
	<section id="race" class="mt-5" style="max-width: 1000px">

		<div id="race-loader" v-if="loading">
			<div id="loader-container">
				<div class="lds-ring" id="loader"><div></div><div></div><div></div><div></div></div>
			</div>
		</div>

        <div id="scoreboard">

            <h2>Results</h2>

            <div id="message"><table class="table table-borderless scoreboard">
                <!-- Row A -->
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
                <!-- Row B -->
                <tbody><tr id="first">
                    <td>
                        <select id="win-result" class="race-result w-100"  v-model="results.top_horses[0].horse_number" @change="updateResults()" required>
                            <option v-for="(horse, index) in horses" :key="index" v-if="showHorse('win', horse.horse_number)">{{ horse.horse_number }}</option>
                        </select>
                    </td>
                    <td class="position-relative">
                        <input type="text" class="w-100" v-model="results.win[0]">
                    </td>
                    <td>
                        <input type="text" class="w-100" v-model="results.win[1]">
                    </td>
                    <td>
                        <input type="text" class="w-100" v-model="results.win[2]">
                    </td>
                </tr>
                <!-- Row C -->
                <tr id="second">
                    <td>
                        <select id="place-result" class=" race-result" v-model="results.top_horses[1].horse_number" @change="updateResults()">
                            <option v-for="(horse, index) in horses" :key="index" v-if="showHorse('place', horse.horse_number)">{{ horse.horse_number }}</option>
                        </select>
                    </td>
                    <td></td>
                    <td class="">
                        <input type="text" class="w-100" v-model="results.place[0]">
                    </td>
                    <td class="">
                        <input type="text" class="w-100" v-model="results.place[1]">
                    </td>
                </tr>
                <!-- Row D -->
                <tr id="third">
                    <td>
                        <select id="show-result" class=" race-result" v-model="results.top_horses[2].horse_number" @change="updateResults()">
                            <option v-for="(horse, index) in horses" :key="index" v-if="showHorse('show', horse.horse_number)">{{ horse.horse_number }}</option>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="">
                        <input type="text" class="w-100" v-model="results.show[0]">
                    </td>
                </tr>
                </tbody></table></div>
        </div>

        <div id="horses" class="mt-4">
            <h2>Horses in this race</h2>
            <div v-for="(horse, index) in horses" :key="index" class="pt-2">
                {{ horse.horse_number }}
            </div>
        </div>

	</section>
</main>
{footer}
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script type="text/javascript" src="./js/results.js"></script>
<?php ob_end_flush(); ?>