<script>
export default {
  salesChart: {
    series: [
      {
        name: "Sales Ratio",
        data: [5, 6, 3, 7, 9, 10, 14, 12, 11, 9, 8, 7, 10, 6, 12, 10, 8]
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
        sparkline: { enabled: true }
      },
      stroke: {
        curve: "smooth",
        width: 2
      },
      colors: ["#2962FF"],
      xaxis: {
        show: false,
        labels: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      markers: {
        size: 0
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