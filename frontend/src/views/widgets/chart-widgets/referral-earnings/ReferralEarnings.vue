<template>
  <div>
    <div class="d-flex align-items-center">
      <div class="mr-2">
        <span>PENERIMAAN KAS</span>
        <h4>Rp  624.925.519</h4>
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
  name: "ReferralEarnings",
  data: () => ({
    title: "ReferralEarnings",
    ReferralEarnings: ReferralEarnings
  }),
  components: {
    VueApexCharts
  }
};
</script>