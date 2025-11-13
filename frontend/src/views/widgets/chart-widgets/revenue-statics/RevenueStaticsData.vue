<script>
export default {
  revenueChart: {
    series: [
      {
        name: "Net Profit",
        data: [6, 10, 9, 11, 9, 10, 12]
      }
    ],

    chartOptions: {
      colors: ["#fff"],
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
        width: 2,
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