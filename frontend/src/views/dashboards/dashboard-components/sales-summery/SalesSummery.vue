<template>
  <div>
    <!-- Header -->
    <div class="d-md-flex align-items-center">
      <div>
        <h3 class="card-title">Summary Income / Expenses</h3>
        <h5 class="card-subtitle text-muted font-weight-normal"></h5>
      </div>
      <div class="ml-auto d-flex no-block align-items-center">
        <ul class="list-inline font-12 dl mr-3 mb-0">
          <li class="list-inline-item text-info">
            <i class="mdi mdi-checkbox-blank-circle"></i> Income
          </li>
          <li class="list-inline-item text-primary">
            <i class="mdi mdi-checkbox-blank-circle"></i> Expenses
          </li>
        </ul>
        <vs-select class="w-100px" v-model="sales1">
          <vs-select-item
            :key="index"
            :value="item.value"
            :text="item.text"
            v-for="(item,index) in salesoptions1"
          />
        </vs-select>
      </div>
    </div>
    <!-- Chart and Data Section -->
    <vs-row vs-justify="center">
     
      <vs-col vs-lg="8" vs-xs="12" vs-sm="8">
        <vue-apex-charts
          type="area"
          height="250"
          :options="apexChartData.lineAreaChartSpline.chartOptions"
          :series="apexChartData.lineAreaChartSpline.series"
        ></vue-apex-charts>
      </vs-col>
    </vs-row>
    <!-- Footer -->
    <hr class="custom-hr" />
    
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import apexChartData from "../../../charts/apexcharts/ApexChartsData";
import MonthlyProfitList from "../monthly-profit-list/MonthlyProfitList";
export default {
  name: "SalesSummery",
  data: () => ({
    title: "SalesSummery",
    sales1: 0,
    salesoptions1: [
      { text: "Monthly", value: 0 },
      { text: "Daily", value: 1 },
      { text: "Weekly", value: 2 },
      { text: "Yearly", value: 3 }
    ],
    apexChartData: apexChartData,
    // Data for TotalIncome
    totalincomes: [
      {
        gridlg: "vs-lg-3",
        gridsm: "vs-sm-6",
        iconcolor: "text-warning",
        icon: "mdi mdi-wallet",
        textcolor: "text-dark",
        text: "Wallet Balance",
        money: "$3,567.53"
      },
      {
        gridlg: "vs-lg-3",
        gridsm: "vs-sm-6",
        iconcolor: "text-info",
        icon: "mdi mdi-star-circle",
        textcolor: "text-dark",
        text: "Referral Earnings",
        money: "$769.08"
      },
      {
        gridlg: "vs-lg-3",
        gridsm: "vs-sm-6",
        iconcolor: "text-primary",
        icon: "mdi mdi-shopping",
        textcolor: "text-dark",
        text: "Estimate Sales",
        money: "5489"
      },
      {
        gridlg: "vs-lg-3",
        gridsm: "vs-sm-6",
        iconcolor: "text-success",
        icon: "mdi mdi-currency-usd",
        textcolor: "text-dark",
        text: "Earnings",
        money: "$23,568.90"
      }
    ]
  }),
  components: {
    VueApexCharts,
    MonthlyProfitList
  }
};
</script>