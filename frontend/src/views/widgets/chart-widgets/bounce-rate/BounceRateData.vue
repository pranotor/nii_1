<script>
export default {
  rateChart: {
    series: [
      {
        name: "Rate",
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
      colors: ["#2962FF"],
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
      fill: {
        type: "solid",
        opacity: 0
      },
      stroke: {
        curve: "smooth",
        width: 2
      },
      markers: {
        size: 3
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