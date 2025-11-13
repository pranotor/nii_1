<template>
  <div class="productsales-margin">
    <vs-tabs alignment="right">
      <vs-tab label="Day">
        <div class="con-tab-ejemplo pt-5">
          <div>
            <vue-apex-charts
              type="line"
              height="400"
              :options="ProductSalesData.dayChart.chartOptions"
              :series="ProductSalesData.dayChart.series"
            ></vue-apex-charts>
          </div>
        </div>
      </vs-tab>
      <vs-tab label="Week">
        <div class="con-tab-ejemplo pt-5">
          <vs-row vs-justify="center">
            <vs-col vs-lg="5" vs-xs="12" vs-sm="5">
              <div>
                <vue-apex-charts
                  type="donut"
                  height="400"
                  width="400"
                  :options="ProductSalesData.weekChart.chartOptions"
                  :series="ProductSalesData.weekChart.series"
                ></vue-apex-charts>
              </div>
            </vs-col>
          </vs-row>
        </div>
      </vs-tab>
      <vs-tab label="Month">
        <div class="con-tab-ejemplo pt-5">
          <div>
            <vue-apex-charts
              type="donut"
              height="400"
              :options="ProductSalesData.monthChart.chartOptions"
              :series="ProductSalesData.monthChart.series"
            ></vue-apex-charts>
          </div>
        </div>
      </vs-tab>
    </vs-tabs>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import ProductSalesData from "./ProductSalesData";

export default {
  name: "ProductSales",
  data: () => ({
    title: "ProductSales",
    ProductSalesData: ProductSalesData
  }),
  components: {
    VueApexCharts
  }
};
</script>