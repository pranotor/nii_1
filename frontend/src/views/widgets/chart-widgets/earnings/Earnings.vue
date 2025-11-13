<template>
  <div>
    <vue-apex-charts
      type="area"
      height="62px"
      :options="EarningsChart.earningChart.chartOptions"
      :series="EarningsChart.earningChart.series"
    ></vue-apex-charts>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import EarningsChart from "./EarningsData";

export default {
  name: "Earnings",
  data: () => ({
    title: "Earnings",
    EarningsChart: EarningsChart
  }),
  components: {
    VueApexCharts
  }
};
</script>