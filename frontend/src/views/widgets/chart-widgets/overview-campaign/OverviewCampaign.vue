<template>
  <div>
    <!-- Chart and Data Section -->
    <vue-apex-charts
      type="area"
      height="168"
      :options="OverviewCampaignData.overviewChart.chartOptions"
      :series="OverviewCampaignData.overviewChart.series"
    ></vue-apex-charts>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import OverviewCampaignData from "./OverviewCampaignData";
export default {
  name: "OverviewCampaignChart",
  data: () => ({
    title: "OverviewCampaignChart",
    OverviewCampaignData: OverviewCampaignData
  }),
  components: {
    VueApexCharts
  }
};
</script>