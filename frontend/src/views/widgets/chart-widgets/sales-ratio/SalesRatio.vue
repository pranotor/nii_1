<template>
  <div>
    <vue-apex-charts
      type="area"
      height="69px"
      :options="SalesratioChart.salesChart.chartOptions"
      :series="SalesratioChart.salesChart.series"
    ></vue-apex-charts>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import SalesratioChart from "./SalesRatioData";

export default {
  name: "SalesRatio",
  data: () => ({
    title: "SalesRatio",
    SalesratioChart: SalesratioChart
  }),
  components: {
    VueApexCharts
  }
};
</script>