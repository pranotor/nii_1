<template>
  <div>
    <hr class="custom-hr" />
    <MonthlyProfitList :totalincomes="totalincomes"></MonthlyProfitList>
  </div>
</template>
<script>
import MonthlyProfitList from "../monthly-profit-list/MonthlyProfitList";
export default {
  name: "Overview",
  data: () => ({
    title: "Overview",
    // Data for TotalIncome
    totalincomes: [
      {
        gridlg: "vs-lg-4",
        gridsm: "vs-sm-4",
        iconcolor: "text-warning",
        icon: "mdi mdi-wallet",
        textcolor: "text-muted",
        text: "Net Profit",
        money: "$3,567.53"
      },
      {
        gridlg: "vs-lg-4",
        gridsm: "vs-sm-4",
        iconcolor: "text-info",
        icon: "mdi mdi-basket",
        textcolor: "text-muted",
        text: "Product sold",
        money: "5489"
      },
      {
        gridlg: "vs-lg-4",
        gridsm: "vs-sm-4",
        iconcolor: "text-primary",
        icon: "mdi mdi-account-box",
        textcolor: "text-muted",
        text: "New Customers",
        money: "$23,568.90"
      }
    ]
  }),
  components: {
    MonthlyProfitList
  }
};
</script>