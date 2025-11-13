<template>
  <vs-row vs-justify="center">
    <!-- /////////////////////////////////////////////
    Overview of email campaigns
    /////////////////////////////////////////////-->
    <vs-col vs-lg="6" vs-xs="12" vs-sm="12">
      <vs-card class="card-no-padding">
        <div class="mb-5 p-4">
          <!-- Header -->
          <div class="d-flex align-items-center mb-3">
            <div>
              <h1 class="font-bold mb-0">$6,890.68</h1>
              <span>Overview of email campaigns</span>
            </div>
            <div class="ml-auto">
              <i class="mdi mdi-wallet text-primary display-4"></i>
            </div>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non pharetra ligula, sit amet laoreet arcu.Integer.</p>
          <a href="javascript: void(0)" class="font-weight-bold">Last Month Summary</a>
        </div>
        <OverviewCampaign></OverviewCampaign>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Overview of email campaigns
    /////////////////////////////////////////////-->
    <vs-col vs-lg="6" vs-xs="12" vs-sm="12">
      <vs-row>
        <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
          <vs-card class="card-no-padding">
            <div class="p-3">
              <h3 class="card-title">Total Visits</h3>
              <h4 class="font-weight-bold mt-3 mb-2">3,25,346</h4>
              <h5
                class="card-subtitle mb-0 font-weight-normal text-muted"
              >Total Earnings of the Month</h5>
            </div>
            <TotalVisits></TotalVisits>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
          <vs-card class="card-no-padding">
            <div class="p-3">
              <h3 class="card-title">Sales Ratio</h3>
              <h4 class="font-weight-bold mt-3 mb-2">35,658</h4>
              <h5
                class="card-subtitle mb-0 font-weight-normal text-muted"
              >Total Earnings of the Month</h5>
            </div>
            <SalesRatio></SalesRatio>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
          <vs-card>
            <TotalEarnings></TotalEarnings>
          </vs-card>
        </vs-col>
      </vs-row>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Revenue Statistics
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card class="bg-primary">
        <RevenueStatics></RevenueStatics>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Page Views
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card class="bg-info card-no-padding">
        <h3 class="card-title text-white p-3">
          <span class="mb-2 d-block">Page Views</span>
          <i class="ti-arrow-up"></i> 6548
        </h3>
        <PageViews></PageViews>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Bounce Rate
    /////////////////////////////////////////////-->
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h2 class="card-title mb-0">56.33%</h2>
        <span class>Bounce Rate</span>
        <MonthBounceRate></MonthBounceRate>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Projects of the Month
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <!-- Header -->
        <div class="d-md-flex align-items-center">
          <div>
            <h3 class="card-title">Projects of the Month</h3>
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
        <MonthTable></MonthTable>
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
import OverviewCampaign from "../../widgets/chart-widgets/overview-campaign/OverviewCampaign";
import MonthTable from "../dashboard-components/month-table/MonthTable";
import RevenueStatics from "../../widgets/chart-widgets/revenue-statics/RevenueStatics";
import PageViews from "../../widgets/chart-widgets/page-views/PageViews";
import RecentComments from "../dashboard-components/recent-comments/RecentComments";
import RecentChats from "../dashboard-components/recent-chats/RecentChats";
import MonthBounceRate from "../../widgets/chart-widgets/month-bounce-rate/MonthBounceRate";
import SalesRatio from "../../widgets/chart-widgets/sales-ratio/SalesRatio";
import TotalVisits from "../../widgets/chart-widgets/total-visits/TotalVisits";
import TotalEarnings from "../../widgets/chart-widgets/total-earnings/TotalEarnings";

// //////////////////////////////////////////
// Export Here
// //////////////////////////////////////////
export default {
  name: "GeneralDashboard",
  data: () => ({
    title: "GeneralDashboard",
    // Month Table
    month1: 0,
    monthoptions1: [
      { text: "Monthly", value: 0 },
      { text: "Daily", value: 1 },
      { text: "Weekly", value: 2 },
      { text: "Yearly", value: 3 }
    ]
  }),
  components: {
    MonthTable,
    RevenueStatics,
    PageViews,
    RecentComments,
    RecentChats,
    MonthBounceRate,
    OverviewCampaign,
    SalesRatio,
    TotalVisits,
    TotalEarnings
  }
};
</script>