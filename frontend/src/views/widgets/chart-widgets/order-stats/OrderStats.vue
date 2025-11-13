<template>
  <div>
    <div class="mt-4 pt-2 mb-5 text-center">
      <vue-apex-charts
        type="donut"
        height="275"
        :options="apexChartData.donutChart.chartOptions"
        :series="apexChartData.donutChart.series"
      ></vue-apex-charts>
    </div>
    <hr class="custom-hr" />
    <vs-row>
      <vs-col vs-lg="4" vs-xs="4" vs-sm="4">
        <i class="mdi mdi-checkbox-blank-circle text-primary"></i>
        <h3 class="mb-0 font-medium">5489</h3>
        <span>Success</span>
      </vs-col>
      <vs-col vs-lg="4" vs-xs="4" vs-sm="4">
        <i class="mdi mdi-checkbox-blank-circle text-info"></i>
        <h3 class="mb-0 font-medium">954</h3>
        <span>Pending</span>
      </vs-col>
      <vs-col vs-lg="4" vs-xs="4" vs-sm="4">
        <i class="mdi mdi-checkbox-blank-circle text-warning"></i>
        <h3 class="mb-0 font-medium">736</h3>
        <span>Failed</span>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import apexChartData from "../../../charts/apexcharts/ApexChartsData";

export default {
  name: "OrderStats",
  data: () => ({
    title: "OrderStats",
    apexChartData: apexChartData
  }),
  components: {
    VueApexCharts
  }
};
</script>