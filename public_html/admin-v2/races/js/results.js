const params = new URLSearchParams(window.location.search);

const results = new Vue({
  el: "#app",
  data() {
    return {
      event: {},
      race: {},
      horses: [],
      loading: true,
      results: {
        win: [0, 0, 0],
        place: [0, 0, 0],
        show: [0, 0, 0],
        top_horses: [
          { horse_number: 0, id: -1 },
          { horse_number: 0, id: -1 },
          { horse_number: 0, id: -1 },
        ],
      },
    };
  },
  computed: {
    availableWinHorses: function () {
      if (this.loading) return [];
      const result = [];
      this.horses.forEach((horse) => {
        if (
          horse.id !== this.results.top_horses[1].id &&
          horse.id !== this.results.top_horses[2].id
        ) {
          result.push(horse);
        }
      });
      return result;
    },
    availablePlaceHorses: function () {
      if (this.loading) return [];
      const result = [];
      this.horses.forEach((horse) => {
        if (
          horse.id !== this.results.top_horses[0].id &&
          horse.id !== this.results.top_horses[2].id
        ) {
          result.push(horse);
        }
      });
      return result;
    },
    availableShowHorses: function () {
      if (this.loading) return [];
      const result = [];
      this.horses.forEach((horse) => {
        if (
          horse.id !== this.results.top_horses[0].id &&
          horse.id !== this.results.top_horses[1].id
        ) {
          result.push(horse);
        }
      });
      return result;
    },
  },
  methods: {
    async fetchEvent() {
      const e = params.get("e");
      const pg = params.get("pg");
      const requestURL = `/api/events?e=${e}&pg=${pg}`;
      let event = await fetch(requestURL);
      event = await event.json();
      console.log(event);
      event = event.data.events.filter((event) => event.id == e)[0];
      this.event = event;
    },
    async fetchRace() {
      const e = params.get("e");
      const r = params.get("r");
      const requestURL = `/api/races?e=${e}&r=${r}`;
      let race = await fetch(requestURL);
      race = await race.json();
      this.race = race.data.races[0];
      this.horses = this.race.horses;
    },
    async fetchResults() {
      const e = params.get("e");
      const r = params.get("r");
      const requestURL = `/api/results?e=${e}&r=${r}`;
      let results = await fetch(requestURL);
      results = await results.json();
      console.log(results);
      this.results = results.data;
    },

    // Might need to be redone
    async updateResults() {
      // Add guard to make sure everything is filled out too
      this.toggleLoading();
      const data = { horses: [] };
      data.horses.forEach(
        (horse, i) =>
          (horse.finish = i == 0 ? "win" : i == 1 ? "place" : "show")
      );
      const e = params.get("e");
      const r = params.get("r");
      const requestURL = `/api/results/?e=${e}&r=${r}`;

      const win = this.horses.filter((horse) => {
        return horse.horse_number == this.results.top_horses[0].horse_number;
      })[0];
      const place = this.horses.filter((horse) => {
        return horse.horse_number == this.results.top_horses[1].horse_number;
      })[0];
      const show = this.horses.filter((horse) => {
        return horse.horse_number == this.results.top_horses[2].horse_number;
      })[0];

      data.horses = [win, place, show];

      data.horses.forEach((horse, i) => {
        if (i == 0) {
          horse.win_purse = this.results.win[0];
          horse.place_purse = this.results.win[1];
          horse.show_purse = this.results.win[2];
        } else if (i == 1) {
          horse.place_purse = this.results.place[0];
          horse.show_purse = this.results.place[1];
        } else {
          horse.show_purse = this.results.show[0];
        }
      });

      let response = await fetch(requestURL, {
        method: "PUT",
        headers: {
          "Content-type": "application/json",
        },
        body: JSON.stringify(data),
      });
      response = await response.json();

      console.log(response);

      await this.fetchResults();
      this.toggleLoading();
    },
    showHorse(type, horseName) {
      const horse = this.horses.filter(
        (horse) => horse.horse_number == horseName
      )[0];
      return horse.finish === null || horse.finish === type;
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
    console.log(this.horses);
    console.log(this.event);
    console.log(this.race);
    console.log(this.results);
  },
});
