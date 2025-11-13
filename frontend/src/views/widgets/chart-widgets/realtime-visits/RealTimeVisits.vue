<template>
  <div>
    <div class="mt-4">
      <vue-apex-charts
        type="area"
        height="350"
        :options="RealTimeVisitsData.realtimeChart.chartOptions"
        :series="RealTimeVisitsData.realtimeChart.series"
      ></vue-apex-charts>
    </div>
    <hr class="custom-hr" />
    <vs-row class="mt-4">
      <vs-col vs-lg="3" vs-xs="12" vs-sm="6">
        <div class="d-flex align-items-center">
          <div class="mr-3 position-relative" style="height: 80px;">
            <i class="mdi mdi-magnify text-primary display-7 chart-inner-icon"></i>
            <vue-apex-charts
              type="donut"
              height="120"
              width="120"
              :options="RealTimeVisitsData.searchChart.chartOptions"
              :series="RealTimeVisitsData.searchChart.series"
            ></vue-apex-charts>
          </div>
          <div>
            <h3 class="mb-0">50%</h3>
            <span>Search Traffic</span>
          </div>
        </div>
      </vs-col>
      <vs-col vs-lg="3" vs-xs="12" vs-sm="6">
        <div class="d-flex align-items-center">
          <div class="mr-3 position-relative" style="height: 80px;">
            <i class="mdi mdi-link text-danger display-7 chart-inner-icon"></i>
            <vue-apex-charts
              type="donut"
              height="120"
              width="120"
              :options="RealTimeVisitsData.referralChart.chartOptions"
              :series="RealTimeVisitsData.referralChart.series"
            ></vue-apex-charts>
          </div>
          <div>
            <h3 class="mb-0">30%</h3>
            <span>Referral Traffic</span>
          </div>
        </div>
      </vs-col>
      <vs-col vs-lg="3" vs-xs="12" vs-sm="6">
        <div class="d-flex align-items-center">
          <div class="mr-3 position-relative" style="height: 80px;">
            <i class="mdi mdi-lightbulb-outline text-info display-7 chart-inner-icon"></i>
            <vue-apex-charts
              type="donut"
              height="120"
              width="120"
              :options="RealTimeVisitsData.socialChart.chartOptions"
              :series="RealTimeVisitsData.socialChart.series"
            ></vue-apex-charts>
          </div>
          <div>
            <h3 class="mb-0">10%</h3>
            <span>Social Media</span>
          </div>
        </div>
      </vs-col>
      <vs-col vs-lg="3" vs-xs="12" vs-sm="6">
        <div class="d-flex align-items-center">
          <div class="mr-3 position-relative" style="height: 80px;">
            <i class="mdi mdi-laptop-mac text-success display-7 chart-inner-icon"></i>
            <vue-apex-charts
              type="donut"
              height="120"
              width="120"
              :options="RealTimeVisitsData.directChart.chartOptions"
              :series="RealTimeVisitsData.directChart.series"
            ></vue-apex-charts>
          </div>
          <div>
            <h3 class="mb-0">10%</h3>
            <span>Direct Media</span>
          </div>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import RealTimeVisitsData from "./RealTimeVisitsData";

export default {
  name: "RealTimeVisits",
  data: () => ({
    title: "RealTimeVisits",
    RealTimeVisitsData: RealTimeVisitsData
  }),
  components: {
    VueApexCharts
  }
};
</script>