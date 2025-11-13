<template>
  <vs-row vs-justify="center">
    <!-- /////////////////////////////////////////////
    Wallet Balance
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <WalletBalance></WalletBalance>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Referral Earnings
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <ReferralEarnings></ReferralEarnings>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Estimated Sales
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <EstimatedSales></EstimatedSales>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Realtime Visits
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <!-- Header -->
        <div class="d-md-flex align-items-center">
          <div>
            <h3 class="card-title">Realtime Visits</h3>
            <h5 class="card-subtitle text-muted font-weight-normal">Overview of Latest Month</h5>
          </div>
          <div class="ml-auto mb-4 mb-md-0">
            <vs-select class="w-100px" v-model="visits1">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in visitsoptions1"
              />
            </vs-select>
          </div>
        </div>
        <RealTimeVisits></RealTimeVisits>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Active Users
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Active Users</h3>
        <ActiveUsers></ActiveUsers>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Device Visit
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Device Visit</h3>
        <DeviceVisits></DeviceVisits>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Visitors By Countries
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Visitors By Countries</h3>
        <div style="height:200px;">
          <App
            :countryData="countryData"
            defaultCountryFillColor="#93d5ed"
            highColor="#2961ff"
            lowColor="#2961ff"
            countryStrokeColor="transparent"
          />
        </div>
        <CountryVisits></CountryVisits>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Top Selling Products
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <!-- Header -->
        <div class="d-md-flex align-items-center">
          <div>
            <h3 class="card-title">Top Selling Products</h3>
            <h5 class="card-subtitle text-muted font-weight-normal">Overview of Latest Month</h5>
          </div>
          <div class="ml-auto mb-4 mb-md-0">
            <vs-select class="w-100px" v-model="selling1">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in sellingoptions1"
              />
            </vs-select>
          </div>
        </div>
        <SellingProduct></SellingProduct>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Recent Comments
    /////////////////////////////////////////////-->
    <vs-col vs-lg="6" vs-xs="12" vs-sm="12">
      <vs-card class="card-no-padding">
        <h3 class="card-title mb-0" slot="header">Recent Comments</h3>
        <RecentComments></RecentComments>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Recent Chats
    /////////////////////////////////////////////-->
    <vs-col vs-lg="6" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title mb-0" slot="header">Recent Chats</h3>
        <RecentChats></RecentChats>
      </vs-card>
    </vs-col>
  </vs-row>
</template>
<script>
// //////////////////////////////////////////
// Import Components Here
// //////////////////////////////////////////
import WalletBalance from "../../widgets/chart-widgets/wallet-balance/WalletBalance";
import ReferralEarnings from "../../widgets/chart-widgets/referral-earnings/ReferralEarnings";
import EstimatedSales from "../../widgets/chart-widgets/estimated-sales/EstimatedSales";
import RealTimeVisits from "../../widgets/chart-widgets/realtime-visits/RealTimeVisits";
import ActiveUsers from "../../widgets/chart-widgets/active-users/ActiveUsers";
import DeviceVisits from "../../widgets/chart-widgets/device-visit/DeviceVisit";
import RecentComments from "../dashboard-components/recent-comments/RecentComments";
import RecentChats from "../dashboard-components/recent-chats/RecentChats";
import CountryVisits from "../dashboard-components/country-visit/CountryVisit";
import SellingProduct from "../dashboard-components/selling-product/SellingProduct";
import App from "vue-world-map";

// //////////////////////////////////////////
// Export Here
// //////////////////////////////////////////
export default {
  name: "AnalyticalDashboard",
  data: () => ({
    title: "AnalyticalDashboard",
    // real time visits
    visits1: 0,
    visitsoptions1: [
      { text: "Monthly", value: 0 },
      { text: "Daily", value: 1 },
      { text: "Weekly", value: 2 },
      { text: "Yearly", value: 3 }
    ],
    // top selling
    selling1: 0,
    sellingoptions1: [
      { text: "Monthly", value: 0 },
      { text: "Daily", value: 1 },
      { text: "Weekly", value: 2 },
      { text: "Yearly", value: 3 }
    ],
    countryData: {
      US: 100,
      IN: 200
    }
  }),
  components: {
    WalletBalance,
    ReferralEarnings,
    EstimatedSales,
    RealTimeVisits,
    ActiveUsers,
    DeviceVisits,
    RecentComments,
    RecentChats,
    CountryVisits,
    SellingProduct,
    App
  }
};
</script>