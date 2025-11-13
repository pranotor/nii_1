<template>
  <div>
    <vs-row>
      <vs-col vs-lg="8" vs-xs="12" vs-sm="8">
        <h3 class="card-title">Order Status</h3>
        <h5 class="card-subtitle mb-0 font-weight-normal text-muted">Total Earnings of the Month</h5>
        <vs-row class="my-4">
          <vs-col vs-lg="4" vs-xs="4" vs-sm="4" class="border-right">
            <i class="mdi mdi-checkbox-blank-circle text-primary"></i>
            <h3 class="mb-0">5489</h3>
            <span>Success</span>
          </vs-col>
          <vs-col vs-lg="4" vs-xs="4" vs-sm="4" class="border-right">
            <i class="mdi mdi-checkbox-blank-circle text-warning"></i>
            <h3 class="mb-0">954</h3>
            <span>Pending</span>
          </vs-col>
          <vs-col vs-lg="4" vs-xs="4" vs-sm="4">
            <i class="mdi mdi-checkbox-blank-circle text-info"></i>
            <h3 class="mb-0">736</h3>
            <span>Failed</span>
          </vs-col>
        </vs-row>
      </vs-col>
      <vs-col vs-lg="4" vs-xs="12" vs-sm="4">
        <div class="mt-4 text-center">
          <vue-apex-charts
            type="donut"
            height="150"
            :options="apexChartData.donutChart.chartOptions"
            :series="apexChartData.donutChart.series"
          ></vue-apex-charts>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import apexChartData from "../../../charts/apexcharts/ApexChartsData";

export default {
  name: "TotalEarnings",
  data: () => ({
    title: "TotalEarnings",
    apexChartData: apexChartData
  }),
  components: {
    VueApexCharts
  }
};
</script>