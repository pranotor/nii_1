<script>
export default {
  walletChart: {
    series: [
      {
        name: "Balance",
        data: [59, 65, 45, 50, 42, 40]
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
      colors: ["#4fc3f7"],
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
        curve: "smooth",
        width: 2
      },
      markers: {
        size: 3
      },
      fill: {
        type: "solid",
        opacity: 0
      },
      xaxis: {
        type: "numeric",
        lines: {
          show: false
        },
        axisBorder: {
          show: false
        },
        labels: { show: false }
      },
      yaxis: [
        {
          y: 0,
          offsetX: 0,
          offsetY: 0,
          padding: { left: 0, right: 0 }
        }
      ],
      tooltip: {
        x: { show: false },
        theme: "dark"
      }
    }
  }
};
</script>