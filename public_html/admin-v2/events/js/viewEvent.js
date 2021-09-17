const params = new URLSearchParams(window.location.search);

const results = new Vue({
  el: "#app",
  data() {
    return {
      lastRace: -1,
      lastRacePage: -1,
      eventId: params.get("e"),
      raceId: 1,
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
      this.lastRacePage = race.data.numberOfPages;
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
    async repopulateResults() {
      this.toggleLoading();
      await this.fetchRace();
      await this.fetchResults();
      this.toggleLoading();
    },
    async nextRace() {
      this.raceId++;
      await this.repopulateResults();
    },
    async previousRace() {
      this.raceId--;
      await this.repopulateResults();
    },
    async setLastRace() {
      let requestURL = `/api/races?e=${this.eventId}&pg=1`;
      let race = await fetch(requestURL);
      race = await race.json();
      this.lastRacePage = race.data.numberOfPages;
      requestURL = `/api/races?e=${this.eventId}&pg=${this.lastRacePage}`;
      race = await fetch(requestURL);
      race = await race.json();
      race = race.data.races[race.data.races.length - 1];
      this.lastRace = race.race_number;
    },
    mapResults(results) {
      this.enteredResults.win = results.top_horses[0].id;
      this.enteredResults.place = results.top_horses[1].id;
      this.enteredResults.show = results.top_horses[2].id;
      this.enteredResults.win_purse = results.win;
      this.enteredResults.place_purse = results.place;
      this.enteredResults.show_purse = results.show;
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
    await this.setLastRace();
  },
});
