<template>
  <div>
      <vue-apex-charts
        type="bar"
        height=70
        :options="TotalVisitsChart.visitChart.chartOptions"
        :series="TotalVisitsChart.visitChart.series"
      ></vue-apex-charts>
    
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import TotalVisitsChart from "./TotalVisitsData";

export default {
  name: "TotalVisits",
  data: () => ({
    title: "TotalVisits",
    TotalVisitsChart: TotalVisitsChart
  }),
  components: {
    VueApexCharts
  }
};
</script>