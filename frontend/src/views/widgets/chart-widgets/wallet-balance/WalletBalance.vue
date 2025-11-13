<template>
  <div>
    <div class="d-flex align-items-center">
      <div class="mr-2">
        <span>POSISI KAS</span>
        <h4>Rp 3.402.190.588</h4>
      </div>
      <div class="ml-auto">
        <div>
          <vue-apex-charts
            type="area"
            height="70px"
            width="100px"
            :options="WalletBalanceChart.walletChart.chartOptions"
            :series="WalletBalanceChart.walletChart.series"
          ></vue-apex-charts>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import WalletBalanceChart from "./WalletBalanceData";

export default {
  name: "WalletBalance",
  data: () => ({
    title: "WalletBalance",
    WalletBalanceChart: WalletBalanceChart
  }),
  components: {
    VueApexCharts
  }
};
</script>