<script>
export default {
  visitChart: {
    series: [
      {
        name: "Total Visits",
        data: [6, 15, 9, 11, 9, 15, 20, 15, 9, 11, 9, 15]
      }
    ],

    chartOptions: {
      colors: ["#4dd0e1"],
      plotOptions: {
        bar: {
          horizontal: false,
          endingShape: "flat",
          columnWidth: "30%"
        }
      },
      chart: {
        toolbar: {
          show: false
        },
        sparkline: { enabled: true }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 4,
        colors: ["transparent"]
      },
      xaxis: {
        labels: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      grid: {
        show: false
      },
      fill: {
        opacity: 1
      },
      tooltip: {
        enabled: true,
        theme: "dark"
      }
    }
  }
};
</script>