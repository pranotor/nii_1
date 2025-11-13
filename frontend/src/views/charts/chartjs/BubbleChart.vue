<script>
import { Bubble } from "vue-chartjs";
export default {
  extends: Bubble,
  mounted() {
    this.renderChart(
      {
        labels: [
          "Eating",
          "Drinking",
          "Sleeping",
          "Designing",
          "Coding",
          "Cycling",
          "Running"
        ],
        datasets: [
          {
            label: "My First dataset",
            backgroundColor: "rgba(41, 98, 255,0.2)",
            borderColor: "rgba(41, 98, 255,1)",
            data: [
              {
                x: 21269017,
                y: 5.245,
                r: 15
              },
              {
                x: 21261000,
                y: 7.205,
                r: 15
              },
              {
                x: 30261000,
                y: 7.205,
                r: 15
              },
              {
                x: 25261000,
                y: 8.205,
                r: 15
              },
              {
                x: 19261000,
                y: 3.205,
                r: 15
              }
            ]
          },
          {
            label: "My Second dataset",
            backgroundColor: "rgba(0, 231, 255,0.2)",
            borderColor: "rgba(0, 231, 255,1)",
            data: [
              {
                x: 258702,
                y: 7.526,
                r: 10
              },
              {
                x: 15002000,
                y: 7.205,
                r: 15
              },
              {
                x: 10002000,
                y: 7.205,
                r: 15
              },
              {
                x: 21004000,
                y: 8.205,
                r: 15
              },
              {
                x: 18001000,
                y: 3.205,
                r: 15
              }
            ]
          }
        ]
      },
      { responsive: true, maintainAspectRatio: false }
    );
  }
};
</script>