<template>
  <div>
    <div class="pt-3 text-center">
      <i class="mdi mdi-file-chart display-4 text-warning d-block"></i>
      <span class="display-4 d-block font-medium">368</span>
      <span>Active Visitors on Site</span>
      <!-- Progress -->
      <vs-progress :percent="50" color="warning" class="mt-5">primary</vs-progress>
      <vs-row class="mt-4 mb-3">
        <vs-col vs-lg="4" vs-xs="12" vs-sm="4" class="border-right text-left">
          <h3 class="mb-0 font-medium">60%</h3>Desktop
        </vs-col>
        <vs-col vs-lg="4" vs-xs="12" vs-sm="4" class="border-right">
          <h3 class="mb-0 font-medium">28%</h3>Mobile
        </vs-col>
        <vs-col vs-lg="4" vs-xs="12" vs-sm="4" class="text-right">
          <h3 class="mb-0 font-medium">12%</h3>Tablet
        </vs-col>
      </vs-row>
      <vs-button color="primary" type="filled" size="large" class="mt-3">View More Details</vs-button>
    </div>
  </div>
</template>
<script>


export default {
  name: "ActiveVisitors",
  data: () => ({
    title: "ActiveVisitors"
  }),
  components: {

  }
};
</script>