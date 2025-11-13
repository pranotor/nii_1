<template>
  <div>
    <h3 class="card-title text-white">Revenue Statistics</h3>
    <vs-row>
      <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
        <div style="height:90px;width: 150px;" class="mt-4 mb-4">
          <vue-apex-charts
            type="bar"
            :options="RevenueStaticsChart.revenueChart.chartOptions"
            :series="RevenueStaticsChart.revenueChart.series"
          ></vue-apex-charts>
        </div>
      </vs-col>
      <vs-col vs-lg="6" vs-xs="12" vs-sm="6" class="d-flex align-items-center justify-content-end">
        <div>
          <h2 class="font-medium text-white mb-0">$351</h2>
          <span class="text-white op-5">Jan 10 - Jan 20</span>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import RevenueStaticsChart from "./RevenueStaticsData";

export default {
  name: "RevenueStatics",
  data: () => ({
    title: "RevenueStatics",
    RevenueStaticsChart: RevenueStaticsChart
  }),
  components: {
    VueApexCharts
  }
};
</script>