<script>
export default {
  referralChart: {
    series: [
      {
        name: "Net Profit",
        data: [6, 10, 9, 11, 9, 10, 12]
      }
    ],

    chartOptions: {
      colors: ["#2962FF"],
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
      xaxis: {
        show: false,
        labels: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      grid: {
        show: false,
        stroke: {
          show: false
        },
        padding: {
          top: 0,
          bottom: 0
        }
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