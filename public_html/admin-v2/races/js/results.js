const params = new URLSearchParams(window.location.search);

const results = new Vue({
  el: "#app",
  data() {
    return {
      nextRace: "",
      previousRace: "",
      showSuccessAlert: false,
      showFailureAlert: false,
      eventId: params.get("e"),
      raceId: params.get("r"),
      page: params.get("pg"),
      enteredResults: {
        win: -1,
        show: -1,
        place: -1,
        win_purse: [0, 0, 0],
        place_purse: [0, 0],
        show_purse: [0],
      },
      event: {},
      race: {},
      horses: [],
      loading: true,
    };
  },
  computed: {
    availableWinHorses: function () {
      const placeId = this.enteredResults.place;
      const showId = this.enteredResults.show;
      return this.horses.filter(horse => horse.id != placeId && horse.id != showId);
    },
    availablePlaceHorses: function () {
      const winId = this.enteredResults.win;
      const showId = this.enteredResults.show;
      return this.horses.filter(horse => horse.id != winId && horse.id != showId);
    },
    availableShowHorses: function () {
      const winId = this.enteredResults.win;
      const placeId = this.enteredResults.place;
      return this.horses.filter(horse => horse.id != winId && horse.id != placeId);
    },
  },
  methods: {
    async fetchEvent() {
      const requestURL = `/api/events?e=${this.eventId}&pg=${this.page}`;
      let event = await fetch(requestURL);
      event = await event.json();
      event = event.data.events.filter((event) => event.id == this.eventId)[0];
      this.event = event;
    },
    async fetchRace() {
      const requestURL = `/api/races?e=${this.eventId}&r=${this.raceId}`;
      let race = await fetch(requestURL);
      race = await race.json();
      this.race = race.data.races[0];
      this.horses = this.race.horses;
    },
    async fetchResults() {
      try {
        const requestURL = `/api/results/?e=${this.eventId}&r=${this.raceId}`;
        let results = await fetch(requestURL);
        results = await results.json();
        this.mapResults(results.data);
      } catch (e) {
        console.log("Error retrieving results (results not yet entered)");
      }
    },
    async updateResults() {
      if (!this.allFieldsFilledOut()) return;
      const data = this.buildRequestObject();
      const requestURL = `/api/results/?e=${this.eventId}&r=${this.raceId}`;
      this.toggleLoading();

      let response = await fetch(requestURL, {
        method: "PUT",
        headers: {
          "Content-type": "application/json",
        },
        body: JSON.stringify(data),
      });

      response = await response.json();
      console.log(response);

      if (response.statusCode === 200) this.showSuccess();
      else this.showFailure();

      this.toggleLoading();
    },
    async setPrevAndNextRaces() {
      let baseURL = `/admin-v2/races/results.php?`
      const nextParams = new URLSearchParams();
      const prevParams = new URLSearchParams();
      if (this.raceId === 1) this.previousRace = ""
      else {
        prevParams.append("e", this.eventId);
        prevParams.append("r", Number.parseInt(this.raceId) - 1)
        if (Number.parseInt(this.raceId) % 10 == 0) {
          prevParams.append("pg", Number.parseInt(this.page) - 1);
        } else {
          prevParams.append("pg", this.page);
        }
        prevParams.append("name", this.event.name);
        this.previousRace = baseURL + prevParams.toString();
      }
      const requestURL = `/api/races?e=${this.eventId}&r=${Number.parseInt(this.raceId) + 1}`;
      let race = await fetch(requestURL);
      race = await race.json();
      if (race.data?.rowReturned === 1) {
        nextParams.append("e", this.eventId);
        nextParams.append("r", Number.parseInt(this.raceId) + 1)
        if (race.numberOfPages > 1 && Number.parseInt(this.raceId) % 10 == 0) {
          nextParams.append("pg", Number.parseInt(this.page) + 1);
        } else {
          nextParams.append("pg", this.page);
        }
        nextParams.append("name", this.event.name);
        this.nextRace = baseURL + nextParams.toString();
      } else {
        this.nextRace = "";
      }
    },
    mapResults(results) {
      this.enteredResults.win = results.top_horses[0].id;
      this.enteredResults.place = results.top_horses[1].id;
      this.enteredResults.show = results.top_horses[2].id;
      this.enteredResults.win_purse = results.win;
      this.enteredResults.place_purse = results.place;
      this.enteredResults.show_purse = results.show;
    },
    buildRequestObject() {
      const winHorse = { 
        id: this.enteredResults.win,
        race_event_id: this.eventId,
        race_race_number: this.raceId,
        finish: "win",
        win_purse: this.enteredResults.win_purse[0],
        place_purse: this.enteredResults.win_purse[1],
        show_purse: this.enteredResults.win_purse[2]
      }
      const placeHorse = {
        id: this.enteredResults.place,
        race_event_id: this.eventId,
        race_race_number: this.raceId,
        finish: "place",
        win_purse: null,
        place_purse: this.enteredResults.place_purse[0],
        show_purse: this.enteredResults.place_purse[1]
      }
      const showHorse = { 
        id: this.enteredResults.show,
        race_event_id: this.eventId,
        race_race_number: this.raceId,
        finish: "show",
        win_purse: null,
        place_purse: null,
        show_purse: this.enteredResults.show_purse[0]
      }
      const data = { horses: [winHorse, placeHorse, showHorse] };
      return data
    },
    allFieldsFilledOut() {
      const res = this.enteredResults
      const win = res.win != -1 && res.win_purse[0] > 0 && res.win_purse[1] > 1 && res.win_purse[2] > 0;
      const place = res.place != -1 && res.place_purse[0] > 0 && res.place_purse[1] > 0;
      const show = res.show != -1 && res.show_purse[0] > 0;
      return win && place && show;
    },
    async showSuccess() {
      this.showSuccessAlert = true;
      await this.delay(3000);
      this.showSuccessAlert = false;
    },
    async showFailure() {
      this.showFailureAlert = true;
      await this.delay(3000);
      this.showFailureAlert = false;
    },
    async delay(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },
    toggleLoading() {
      this.loading = !this.loading;
    },
  },
  async mounted() {
    await this.fetchEvent();
    await this.fetchRace();
    await this.fetchResults();
    await this.setPrevAndNextRaces();
    this.toggleLoading();
  },
});