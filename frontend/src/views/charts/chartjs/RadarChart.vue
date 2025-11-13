<script>
import { Radar } from "vue-chartjs";

export default {
  extends: Radar,
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
            pointBackgroundColor: "rgba(41, 98, 255,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(41, 98, 255,1)",
            data: [65, 59, 90, 81, 56, 55, 40]
          },
          {
            label: "My Second dataset",
            backgroundColor: "rgba(0, 231, 255,0.2)",
            borderColor: "rgba(0, 231, 255,1)",
            pointBackgroundColor: "rgba(0, 231, 255,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(0, 231, 255,1)",
            data: [28, 48, 40, 19, 96, 27, 100]
          }
        ]
      },
      { responsive: true, maintainAspectRatio: false }
    );
  }
};
</script>