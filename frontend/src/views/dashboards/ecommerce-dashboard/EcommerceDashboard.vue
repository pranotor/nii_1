<template>
  <vs-row vs-justify="center">
    <!-- /////////////////////////////////////////////
    Earnings
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card class="card-no-padding card-no-shadow">
        <div slot="header">
          <h3 class="card-title">Earnings</h3>
          <h5 class="card-subtitle font-weight-normal text-muted">Total Earnings of the Month</h5>
          <h2 class="font-medium mb-0">$43,567.53</h2>
        </div>
        <Earnings></Earnings>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Overview
    /////////////////////////////////////////////-->
    <vs-col vs-lg="8" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Overview</h3>
        <h5 class="card-subtitle font-weight-normal text-muted mb-4">Total Earnings of the Month</h5>
        <Overview></Overview>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Product Sales
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card class="card-no-shadow">
        <div>
          <h3 class="card-title">Product Sales</h3>
          <h5 class="card-subtitle font-weight-normal text-muted">Total Earnings of the Month</h5>
        </div>
        <ProductSales></ProductSales>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Products of the Month
    /////////////////////////////////////////////-->
    <vs-col vs-lg="8" vs-xs="12" vs-sm="12">
      <vs-card>
        <!-- Header -->
        <div class="d-md-flex align-items-center">
          <div>
            <h3 class="card-title">Products of the Month</h3>
            <h5 class="card-subtitle text-muted font-weight-normal">Overview of Latest Month</h5>
          </div>
          <div class="ml-auto">
            <vs-select class="w-100px" v-model="month1">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in monthoptions1"
              />
            </vs-select>
          </div>
        </div>
        <!-- Content -->
        <ProductTable></ProductTable>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Order Stats
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Order Stats</h3>
        <h5 class="card-subtitle font-weight-normal text-muted">Overview of orders</h5>
        <OrderStats></OrderStats>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Reviews
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <Reviews></Reviews>
      </vs-card>
    </vs-col>
  </vs-row>
</template>
<script>
// //////////////////////////////////////////
// Import Components Here
// //////////////////////////////////////////
import Earnings from "../../widgets/chart-widgets/earnings/Earnings";
import Overview from "../dashboard-components/overview/Overview";
import ProductSales from "../../widgets/chart-widgets/product-sales/ProductSales";
import ProductTable from "../dashboard-components/product-table/ProductTable";
import OrderStats from "../../widgets/chart-widgets/order-stats/OrderStats";
import Reviews from "../dashboard-components/reviews/Reviews";

// //////////////////////////////////////////
// Export Here
// //////////////////////////////////////////
export default {
  name: "EcommerceDashboard",
  data: () => ({
    title: "EcommerceDashboard",
    // Month Table
    month1: 0,
    monthoptions1: [
      { text: "March", value: 0 },
      { text: "April", value: 1 },
      { text: "May", value: 2 },
      { text: "June", value: 3 }
    ]
  }),
  components: {
    Earnings,
    Overview,
    ProductSales,
    ProductTable,
    OrderStats,
    Reviews
  }
};
</script>