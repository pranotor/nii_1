<template>
  <div>
    <div class="d-flex align-items-center">
      <div class="mr-2">
        <span>PIUTANG USAHA</span>
        <h4>Rp 8.307.807.631</h4>
      </div>
      <div class="ml-auto">
        <div style="height: 70px;width: 110px;">
          <vue-apex-charts
            type="bar"
            :options="ReferralEarnings.referralChart.chartOptions"
            :series="ReferralEarnings.referralChart.series"
          ></vue-apex-charts>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import ReferralEarnings from "./ReferralEarningsData";

export default {
  name: "EstimatedSales",
  data: () => ({
    title: "EstimatedSales",
    ReferralEarnings: ReferralEarnings
  }),
  components: {
    VueApexCharts
  }
};
</script>