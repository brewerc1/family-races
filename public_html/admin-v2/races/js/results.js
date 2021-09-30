const params = new URLSearchParams(window.location.search);

const results = new Vue({
  el: "#app",
  data() {
    return {
      showSuccessAlert: false,
      showFailureAlert: false,
      eventId: params.get("e"),
      raceId: params.get("r"),
      page: params.get("pg"),
      status: Number.parseInt(params.get("status")), // 1 = event closed, 0 = event open
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
      return this.horses.filter(
        (horse) => horse.id != placeId && horse.id != showId
      );
    },
    availablePlaceHorses: function () {
      const winId = this.enteredResults.win;
      const showId = this.enteredResults.show;
      return this.horses.filter(
        (horse) => horse.id != winId && horse.id != showId
      );
    },
    availableShowHorses: function () {
      const winId = this.enteredResults.win;
      const placeId = this.enteredResults.place;
      return this.horses.filter(
        (horse) => horse.id != winId && horse.id != placeId
      );
    },
    sortedHorses: function () {
      if (this.loading) return [];
      if (this.enteredResults.win === -1) return this.horses;
      const win = this.horses.filter(
        (horse) => horse.id === this.enteredResults.win
      );
      const place = this.horses.filter(
        (horse) => horse.id === this.enteredResults.place
      );
      const show = this.horses.filter(
        (horse) => horse.id === this.enteredResults.show
      );
      const otherHorses = this.horses.filter(
        (horse) =>
          horse.id !== this.enteredResults.win &&
          horse.id !== this.enteredResults.place &&
          horse.id !== this.enteredResults.show
      );
      const result = [...otherHorses];
      if (show.length > 0) result.unshift(show[0]);
      if (place.length > 0) result.unshift(place[0]);
      if (win.length > 0) result.unshift(win[0]);
      return result;
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
      this.restrictPurseSizes();

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

      if (response.statusCode === 200) this.showSuccess();
      else this.showFailure();

      this.toggleLoading();
      if (this.status === 1) this.recalculateResults();
    },
    async recalculateResults() {
      const requestURL = `/api/recalculate/?e=${this.eventId}`;
      const request = await fetch(requestURL, {
        method: "PUT",
        headers: {
          "Content-type": "application/json",
        },
      });
      if (request.status !== 200) console.log("Error recalculating results.");
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
        show_purse: this.enteredResults.win_purse[2],
      };
      const placeHorse = {
        id: this.enteredResults.place,
        race_event_id: this.eventId,
        race_race_number: this.raceId,
        finish: "place",
        win_purse: null,
        place_purse: this.enteredResults.place_purse[0],
        show_purse: this.enteredResults.place_purse[1],
      };
      const showHorse = {
        id: this.enteredResults.show,
        race_event_id: this.eventId,
        race_race_number: this.raceId,
        finish: "show",
        win_purse: null,
        place_purse: null,
        show_purse: this.enteredResults.show_purse[0],
      };
      const data = { horses: [winHorse, placeHorse, showHorse] };
      return data;
    },
    allFieldsFilledOut() {
      const res = this.enteredResults;
      const win =
        res.win != -1 &&
        res.win_purse[0] > 0 &&
        res.win_purse[1] > 1 &&
        res.win_purse[2] > 0;
      const place =
        res.place != -1 && res.place_purse[0] > 0 && res.place_purse[1] > 0;
      const show = res.show != -1 && res.show_purse[0] > 0;
      return win && place && show;
    },
    restrictPurseSizes() {
      const largestAcceptableValue = 9999.99;
      this.enteredResults.win_purse.forEach((value, i) => {
        this.enteredResults.win_purse[i] = Math.min(
          largestAcceptableValue,
          Number.parseFloat(value).toFixed(2)
        );
      });
      this.enteredResults.place_purse[0] = Math.min(
        largestAcceptableValue,
        Number.parseFloat(this.enteredResults.place_purse[0]).toFixed(2)
      );
      this.enteredResults.place_purse[1] = Math.min(
        largestAcceptableValue,
        Number.parseFloat(this.enteredResults.place_purse[1]).toFixed(2)
      );
      this.enteredResults.show_purse[0] = Math.min(
        largestAcceptableValue,
        Number.parseFloat(this.enteredResults.show_purse[0]).toFixed(2)
      );
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
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    toggleLoading() {
      this.loading = !this.loading;
    },
  },
  async mounted() {
    await this.fetchEvent();
    await this.fetchRace();
    await this.fetchResults();
    this.toggleLoading();
  },
});
