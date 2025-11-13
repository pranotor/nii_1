<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card class="card-no-padding card-no-shadow">
        <h3 class="card-title mb-0" slot="header">Recent comments</h3>
        <RecentComments></RecentComments>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Recent Chats</h3>
        <RecentChats></RecentChats>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Browse Stats</h3>
        <BrowseStats></BrowseStats>
      </vs-card>
    </vs-col>
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <h3 class="card-title">Recent Messages</h3>
        <RecentMessages></RecentMessages>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Notifications</h3>
        <RecentNotification></RecentNotification>
      </vs-card>
      <vs-card class="card-no-padding card-no-shadow">
        <div slot="header">
          <!-- Header -->
          <div class="d-md-flex align-items-center">
            <div>
              <h3 class="card-title mb-0">Project Month</h3>
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
        </div>
        <ProjectTable></ProjectTable>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Feeds</h3>
        <Feeds></Feeds>
      </vs-card>
    </vs-col>
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="4" vs-xs="12" vs-sm="12">
      <vs-card>
        <UserProfile></UserProfile>
      </vs-card>
      <vs-card class="text-center">
        <FollowProfile></FollowProfile>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Result of the Poll</h3>
        <h5
          class="card-subtitle font-weight-normal text-muted"
        >Here is the result for the latest poll</h5>
        <p class="font-weight-bold text-muted">What is your mobile app usage daily?</p>
        <ResultPoll></ResultPoll>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Poll of the week</h3>
        <h5 class="card-subtitle font-weight-normal text-muted">Here is the latest polll</h5>
        <p class="font-weight-bold text-muted">What is your mobile app usage daily?</p>
        <PollWeek></PollWeek>
      </vs-card>
      <vs-card>
        <h3 class="card-title">Visits around the countries</h3>
        <VisitCountry></VisitCountry>
      </vs-card>
    </vs-col>
  </vs-row>
</template>
<script>
import RecentComments from "../../dashboards/dashboard-components/recent-comments/RecentComments";
import RecentChats from "../../dashboards/dashboard-components/recent-chats/RecentChats";
import RecentMessages from "../other-widgets/recent-messages/RecentMessages";
import RecentNotification from "../other-widgets/recent-notifications/RecentNotifications";
import UserProfile from "../other-widgets/user-profile/UserProfile";
import FollowProfile from "../other-widgets/follow-profile/FollowProfile";
import ProjectTable from "../other-widgets/project-table/ProjectTable";
import BrowseStats from "../other-widgets/browse-stats/BrowseStats";
import ResultPoll from "../other-widgets/result-poll/ResultPoll";
import PollWeek from "../other-widgets/poll-week/PollWeek";
import VisitCountry from "../other-widgets/visit-country/VisitCountry";
import Feeds from "../other-widgets/feeds/Feeds";

export default {
  name: "widgetapps",
  data: () => ({
    title: "widgetapps",
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
    RecentComments,
    RecentChats,
    RecentMessages,
    RecentNotification,
    UserProfile,
    FollowProfile,
    ProjectTable,
    BrowseStats,
    ResultPoll,
    PollWeek,
    VisitCountry,
    Feeds
  }
};
</script>