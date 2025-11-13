<template>
  <div>
    <vs-row class="mt-4">
      <vs-col vs-lg="5" vs-xs="12" vs-sm="6" class="d-flex align-items-center">
        <div>
          <h3 class="font-medium mb-0">56.33%</h3>
          <span class>Total Bounce</span>
        </div>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="12" vs-sm="6">
        <div>
          <vue-apex-charts
            type="area"
            height="120px"
            :options="BounceRateChart.rateChart.chartOptions"
            :series="BounceRateChart.rateChart.series"
          ></vue-apex-charts>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import BounceRateChart from "./BounceRateData";

export default {
  name: "BounceRate",
  data: () => ({
    title: "BounceRate",
    BounceRateChart: BounceRateChart
  }),
  components: {
    VueApexCharts
  }
};
</script>