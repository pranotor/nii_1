<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="6" vs-xs="12" vs-sm="6">
      <vs-card>
        <h3 class="card-title mb-0" slot="header">Active User</h3>
        <ActiveUsers></ActiveUsers>
      </vs-card>
      <vs-card>
        <h3 class="card-title mb-0" slot="header">TotalVisits</h3>
        <TotalVisits></TotalVisits>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Order Stats</h3>
        <OrderStats></OrderStats>
      </vs-card>
      <vs-card>
        <WalletBalance></WalletBalance>
      </vs-card>
      <vs-card class="bg-info card-no-padding">
        <h3 class="card-title text-white p-3">
          <span class="mb-2 d-block">Page Views</span>
          <i class="ti-arrow-up"></i> 6548
        </h3>
        <PageViews></PageViews>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Estimated Sales</h3>
        <EstimatedSales></EstimatedSales>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Referral Earnings</h3>
        <ReferralEarnings></ReferralEarnings>
      </vs-card>
    </vs-col>
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="6" vs-xs="12" vs-sm="6">
      <vs-card>
        <h3 class="card-title mb-0" slot="header">Device Visit</h3>
        <DeviceVisits></DeviceVisits>
      </vs-card>
      <vs-card class="bg-primary">
        <RevenueStatics></RevenueStatics>
      </vs-card>

      <vs-card>
        <h3 class="card-title">Sales Ratio</h3>
        <SalesRatio></SalesRatio>
      </vs-card>
      <vs-card>
        <h2 class="card-title mb-0">56.33%</h2>
        <span class>Bounce Rate</span>
        <MonthBounceRate></MonthBounceRate>
      </vs-card>
      <vs-card>
        <h3 class="card-title mb-0" slot="header">Bounce Rate</h3>
        <BounceRate></BounceRate>
      </vs-card>
      <vs-card>
        <TotalEarnings></TotalEarnings>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Overview Campaign</h3>
        <OverviewCampaignChart></OverviewCampaignChart>
      </vs-card>
    </vs-col>
  </vs-row>
</template>
<script>
import ActiveUsers from "../chart-widgets/active-users/ActiveUsers";
import DeviceVisits from "../chart-widgets/device-visit/DeviceVisit";
import BounceRate from "../chart-widgets/bounce-rate/BounceRate";
import OrderStats from "../chart-widgets/order-stats/OrderStats";
import TotalVisits from "../chart-widgets/total-visits/TotalVisits";
import RevenueStatics from "../chart-widgets/revenue-statics/RevenueStatics";
import PageViews from "../chart-widgets/page-views/PageViews";
import MonthBounceRate from "../chart-widgets/month-bounce-rate/MonthBounceRate";
import EstimatedSales from "../chart-widgets/estimated-sales/EstimatedSales";
import OverviewCampaignChart from "../chart-widgets/overview-campaign/OverviewCampaign";
import ReferralEarnings from "../chart-widgets/referral-earnings/ReferralEarnings";
import SalesRatio from "../chart-widgets/sales-ratio/SalesRatio";
import TotalEarnings from "../chart-widgets/total-earnings/TotalEarnings";
import WalletBalance from "../chart-widgets/wallet-balance/WalletBalance";

export default {
  name: "widgetdata",
  data: () => ({
    title: "widgetdata"
  }),
  components: {
    ActiveUsers,
    DeviceVisits,
    BounceRate,
    OrderStats,
    TotalVisits,
    RevenueStatics,
    PageViews,
    MonthBounceRate,
    EstimatedSales,
    OverviewCampaignChart,
    ReferralEarnings,
    SalesRatio,
    TotalEarnings,
    WalletBalance
  }
};
</script>