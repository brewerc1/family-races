const params = new URLSearchParams(window.location.search);

const defaultResults = {
  win: -1,
  show: -1,
  place: -1,
  win_purse: [0, 0, 0],
  place_purse: [0, 0],
  show_purse: [0],
};

const deepClone = () => JSON.parse(JSON.stringify(defaultResults));

const findCorrectHorse = (horses, lookingFor) => {
  return horses.find((horse) => horse.id === lookingFor);
};

const findOtherHorses = (horses, results) => {
  return horses.filter((horse) => {
    return (
      horse.id !== results.win &&
      horse.id !== results.place &&
      horse.id !== results.show
    );
  });
};

const results = new Vue({
  el: "#app",
  data() {
    return {
      lastRace: -1,
      lastRacePage: -1,
      eventHasRaces: true,
      eventId: params.get("e"),
      raceId: 1,
      page: params.get("pg"),
      enteredResults: deepClone(defaultResults),
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

      const win = findCorrectHorse(this.horses, this.enteredResults.win);
      const place = findCorrectHorse(this.horses, this.enteredResults.place);
      const show = findCorrectHorse(this.horses, this.enteredResults.show);
      const otherHorses = findOtherHorses(this.horses, this.enteredResults);

      const result = [...otherHorses];

      if (show.id > 0) result.unshift(show);
      if (place.id > 0) result.unshift(place);
      if (win.id > 0) result.unshift(win);

      return result;
    },
    editRaceURL() {
      return `/admin-v2/races/results.php?e=${this.eventId}&pg=${this.page}&r=${this.raceId}`;
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
      try {
        this.race = race.data.races[0];
        this.horses = this.race.horses;
        this.lastRacePage = race.data.numberOfPages;
      } catch (e) {
        console.log("This event contains no races.");
        this.eventHasRaces = false;
      }
    },
    async fetchResults() {
      if (!this.eventHasRaces) return;
      try {
        const requestURL = `/api/results/?e=${this.eventId}&r=${this.raceId}`;
        let results = await fetch(requestURL);
        results = await results.json();
        this.mapResults(results.data);
      } catch (e) {
        console.log("Error retrieving results (results not yet entered)");
        this.enteredResults = deepClone(defaultResults);
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
      this.lastRace = race?.race_number ? race.race_number : 1;
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
