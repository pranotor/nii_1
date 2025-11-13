<template>
  <vs-row vs-justify="center">
    
    <!-- /////////////////////////////////////////////
    Projects of the Month
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <!-- Header -->
        <div class="d-md-flex align-items-center">
          <div>
            <h3 class="card-title">Projects</h3>
            
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
        <Kanban></Kanban>
        
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
import Kanban from "../dashboard-components/kanban/MonthTable";
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
    Kanban,
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