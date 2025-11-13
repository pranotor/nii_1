<script>
import { Pie } from "vue-chartjs";

export default {
  extends: Pie,
  mounted() {
    this.renderChart(
      {
        labels: ["Ample Admin", "Xtreme Admin", "MaterialPro Admin"],

        datasets: [
          {
            backgroundColor: ["#2962FF", "#4fc3f7", "#36bea6"],
            data: [40, 20, 10]
          }
        ]
      },
      { responsive: true, maintainAspectRatio: false, height: 250 }
    );
  }
};
</script>