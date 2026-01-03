<template>
  <Doughnut :options="chartOptions" :data="datacollection" />
</template>

<script>
import { Doughnut } from "vue-chartjs";
import {
  Chart,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

Chart.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale);

export default {
  props: ["chartData"],

  components: { Doughnut },
  data() {
    return {
      datacollection: {
        //  labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        labels: this.chartData.labels,

        datasets: [
          {
            label: "Total Complain",
            //backgroundColor: ['#f87979', 'green'],
            backgroundColor: this.randColors(this.chartData.labels.length),
            pointBackgroundColor: "white",
            borderWidth: 1,
            pointBorderColor: "#29465B",
            // data: [60, 40, 20, 50, 90, 10, 20, 40, 50, 70, 90, 100],
            data: this.chartData.data,
          },
        ],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
          },
        },
      },
    };
  },

  methods: {
    // random color generator
    randColors(val = 2) {
      // empty Array
      const colors = [];
      for (let i = 1; i <= val; i++) {
        let color = "#" + (Math.random().toString(16) + "0000000").slice(2, 8);
        // colors push in array
        colors.push(color);
      }
      console.log(colors);
      return colors;
    },
  },

  // mounted () {
  //   this.renderChart(this.datacollection, this.options)
  // },

  // created(){
  //   //console.log('chartdata--', this.randColors(this.chartData.labels.length), this.chartData.labels.length )
  // }
};
</script>
