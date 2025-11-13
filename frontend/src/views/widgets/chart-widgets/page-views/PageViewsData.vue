<script>
export default {
  viewChart: {
    series: [
      {
        name: "Views",
        data: [28, 40, 36, 52, 38, 60, 55]
      }
    ],
    chartOptions: {
      grid: {
        show: false,
        padding: {
          left: 0,
          right: 0
        }
      },
      colors: ["#fff"],
      chart: {
        toolbar: {
          show: false
        },
        sparkline: {
          enabled: true
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: "straight",
        width: 1
      },
      tooltip: {
        theme: "dark"
      }
    }
  }
};
</script>