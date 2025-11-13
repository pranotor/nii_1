<script>
const themeColors = ["#2962FF", "#4fc3f7"];

export default {
  overviewChart: {
    series: [
      {
        name: "Last Month",
        data: [3, 8, 2, 3, 2, 5, 6, 8]
      },
      {
        name: "Current Month",
        data: [7, 6, 5, 8, 6, 7, 2, 1]
      }
    ],

    chartOptions: {
      dataLabels: {
        enabled: false
      },
      chart: {
        toolbar: {
          show: false
        },
        sparkline: {
          enabled: true
        }
      },
      stroke: {
        curve: "smooth",
        width: 2
      },
      colors: themeColors,
      xaxis: {
        labels: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      },
      markers: {
        size: 3
      },
      tooltip: {
        x: {
          format: "dd/MM/yy HH:mm"
        },
        theme: "dark"
      },
      legend: {
        show: false
      },
      grid: {
        show: false
      }
    }
  }
};
</script>