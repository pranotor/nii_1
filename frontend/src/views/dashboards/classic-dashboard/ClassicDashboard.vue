<template>
  <vs-row vs-justify="center">
    

    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <DrdBalance></DrdBalance>
      </vs-card>
    </vs-col>
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <LppBalance></LppBalance>
      </vs-card>
    </vs-col>
    <vs-col vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <MeterBaca></MeterBaca>
      </vs-card>
    </vs-col>
    <!-- /////////////////////////////////////////////
    Sales Summary
    /////////////////////////////////////////////-->
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <SalesSummery></SalesSummery>
      </vs-card>
    </vs-col>
    
  </vs-row>
</template>
<script>
// //////////////////////////////////////////
// Import Components Here
// //////////////////////////////////////////
import SalesSummery from "../dashboard-components/sales-summery/SalesSummery";
import WalletBalance from "../../widgets/chart-widgets/wallet-balance/WalletBalance";
import DrdBalance from "../../widgets/chart-widgets/drd-balance/DrdBalance";
import LppBalance from "../../widgets/chart-widgets/lpp-balance/LppBalance";
import MeterBaca from "../../widgets/chart-widgets/meter-baca/MeterBaca";
import ReferralEarnings from "../../widgets/chart-widgets/referral-earnings/ReferralEarnings";
import EstimatedSales from "../../widgets/chart-widgets/estimated-sales/EstimatedSales";
import EmailCampaign from "../dashboard-components/email-campaign/EmailCampaign";
import ActiveVisitors from "../dashboard-components/active-visitors/ActiveVisitors";
import MonthTable from "../dashboard-components/month-table/MonthTable";
import RevenueStatics from "../../widgets/chart-widgets/revenue-statics/RevenueStatics";
import PageViews from "../../widgets/chart-widgets/page-views/PageViews";
import RecentComments from "../dashboard-components/recent-comments/RecentComments";
import RecentChats from "../dashboard-components/recent-chats/RecentChats";
import BounceRate from "../../widgets/chart-widgets/bounce-rate/BounceRate";

// //////////////////////////////////////////
// Export Here
// //////////////////////////////////////////
export default {
  name: "ClassicDashboard",
  data: () => ({
    title: "ClassicDashboard",
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
    WalletBalance,
    ReferralEarnings,
    EstimatedSales,
    DrdBalance,  
    LppBalance,  
    MeterBaca,  
    SalesSummery,
    EmailCampaign,
    ActiveVisitors,
    MonthTable,
    RevenueStatics,
    PageViews,
    RecentComments,
    RecentChats,
    BounceRate
  }
};
</script>