<script>
export default {
  dayChart: {
    series: [
      {
        name: "Site A",
        type: "column",
        data: [5, 6, 3, 7, 9, 10, 14, 12, 11, 9, 8, 7, 10, 6, 12, 10, 8]
      },
      {
        name: "Site B",
        type: "column",
        data: [1, 2, 8, 3, 4, 5, 7, 6, 5, 6, 4, 3, 3, 12, 5, 6, 3]
      }
    ],
    chartOptions: {
      labels: ["Day", "Month"],
      dataLabels: {
        enabled: false
      },
      grid: {
        padding: {
          left: 0,
          right: 0
        },
        borderColor: "rgba(0,0,0,0.1)"
      },
      yaxis: [
        {
          seriesName: "Site A"
        },
        {
          seriesName: "Site B",
          show: false
        }
      ],
      tooltip: {
        shared: false,
        intersect: true,
        x: {
          show: false
        },
        theme: "dark"
      },
      legend: {
        show: false
      },
      colors: ["#2962FF", "#4fc3f7"],
      chart: {
        stacked: false
      },
      plotOptions: {
        bar: {
          columnWidth: "25%"
        }
      },
      fill: {
        opacity: 1
      },
      stroke: {
        width: 0
      },
      xaxis: {
        categories: [0, 2, 4, 6, 8, 10, 12, 14],
        labels: {
          style: {
            cssClass: "text-muted fill-color"
          }
        }
      }
    }
  },
  weekChart: {
    series: [60, 15],
    chartOptions: {
      labels: ["Day", "Month"],
      dataLabels: {
        enabled: false
      },
      grid: {
        padding: {
          left: 0,
          right: 0
        }
      },
      chart: {},
      plotOptions: {
        pie: {
          donut: {
            size: "95px"
          }
        }
      },
      stroke: {
        width: 0
      },
      legend: {
        show: false
      },
      colors: ["rgb(41, 97, 255)", "rgb(218, 218, 218)"],
      tooltip: {
        fillSeriesColor: false,
        theme: "dark"
      },
      responsive: [
        {
          breakpoint: 991.98,
          options: {
            chart: {
              height: 250,
              width: 250
            }
          }
        }
      ]
    }
  },
  monthChart: {
    series: [45, 15, 27, 18],
    chartOptions: {
      labels: ["Email", "Website", "Mobile", "Other"],
      dataLabels: {
        enabled: false
      },
      grid: {
        padding: {
          left: 0,
          right: 0
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: "80px"
          }
        }
      },
      stroke: {
        width: 0
      },
      legend: {
        show: true,
        position: "bottom",
        onItemHover: {
          highlightDataSeries: true
        }
      },
      colors: ["#40c4ff", "#2961ff", "#ff821c", "#7e74fb"],
      tooltip: {
        fillSeriesColor: false,
        theme: "dark"
      },
      responsive: [
        {
          breakpoint: 991.98,
          options: {
            chart: {
              height: 250
            }
          }
        }
      ]
    }
  }
};
</script>