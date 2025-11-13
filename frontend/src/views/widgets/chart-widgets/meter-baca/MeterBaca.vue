<template>
  <div>
    <div class="d-flex align-items-center">
      <div class="mr-2">
        <span>Actual vs Target</span>
        
      </div>
      <div class="ml-auto">
        <div style="height: 70px; width: 180px" class="overflow-hidden">
          <vue-apex-charts
            height="200px"
            width="220px"
            :options="EstimatedSales.salesChart.chartOptions"
            :series="EstimatedSales.salesChart.series"
          ></vue-apex-charts>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import EstimatedSales from "./EstimatedSalesData";

export default {
  name: "EstimatedSales",
  data: () => ({
    title: "EstimatedSales",
    EstimatedSales: EstimatedSales
  }),
  components: {
    VueApexCharts
  }
};
</script>