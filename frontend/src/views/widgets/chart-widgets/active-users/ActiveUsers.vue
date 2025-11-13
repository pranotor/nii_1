<template>
  <div>
    <div class="d-flex">
      <h2>
        356
        <small>
          <i class="ti-arrow-up text-success"></i>
        </small>
      </h2>
      <span class="ml-auto">Users per minute</span>
    </div>
    <div class="mt-3 text-center">
      <div style="height: 70px;width: 110px;margin: 0 auto;">
        <vue-apex-charts
          type="bar"
          :options="ActiveUsersChart.usersChart.chartOptions"
          :series="ActiveUsersChart.usersChart.series"
        ></vue-apex-charts>
      </div>
    </div>
    <h5 class="mb-2 mt-3">Top Active Pages</h5>
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        /templates/matrix-admin/
        <span class="badge badge-light badge-pill">1</span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        /panel/home.asp
        <span class="badge badge-primary badge-pill">4</span>
      </li>
      <li
        class="list-group-item d-flex justify-content-between align-items-center"
      >/demos/admin-t...e/index2.html</li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        /templates/adminwrap-lite/
        <span class="badge badge-light badge-pill">14</span>
      </li>
    </ul>
  </div>
</template>
<script>
import VueApexCharts from "vue-apexcharts";
import ActiveUsersChart from "./ActiveUsersData";

export default {
  name: "ActiveUsers",
  data: () => ({
    title: "ActiveUsers",
    ActiveUsersChart: ActiveUsersChart
  }),
  components: {
    VueApexCharts
  }
};
</script>