<template>
  <Bar :options="chartOptions" :data="datacollection" :height="400" />
</template>

<script>
import { Bar } from "vue-chartjs";
import {
  Chart,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from "chart.js";

import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ChartDataLabels
);

export default {
  props: ["chartData", "chartLabel"],

  components: { Bar },
  data() {
    return {
      datacollection: {
        labels: this.chartLabel,
        datasets: [
          {
            label: "Total Expense",
            backgroundColor: this.randColors(this.chartLabel.length),
            pointBackgroundColor: "white",
            borderWidth: 1,
            fill: false,
            data: this.chartData
          }
        ]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          datalabels: {
            // Display data values
            anchor: "end",
            align: "top",
            formatter: Math.round,
            font: {
              weight: "normal",
              size: 12
            }
          }
        }
      }
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
      return colors;
    }
  }

  // mounted() {
  //   this.renderChart(this.datacollection, this.options);
  // },
};
</script>
