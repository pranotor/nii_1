<template>
  <div>
    <ul class="list-inline mt-3 mb-2">
      <li class="mb-4">
        <h2 class="mb-0">6350</h2>
        <small>From India</small>
        <div class="float-right">
          48%
          <i class="mdi mdi-arrow-up text-success"></i>
        </div>
        <vs-progress :percent="48" color="primary">primary</vs-progress>
      </li>
      <li class="mb-4">
        <h2 class="mb-0">3250</h2>
        <small>From UAE</small>
        <div class="float-right">
          98%
          <i class="mdi mdi-arrow-up text-success"></i>
        </div>
        <vs-progress :percent="98" color="success">success</vs-progress>
      </li>
      <li class="mb-4">
        <h2 class="mb-0">1250</h2>
        <small>From Australia</small>
        <div class="float-right">
          75%
          <i class="mdi mdi-arrow-down text-danger"></i>
        </div>
        <vs-progress :percent="75" color="danger">danger</vs-progress>
      </li>
      <li class="mb-4">
        <h2 class="mb-0">1350</h2>
        <small>From USA</small>
        <div class="float-right">
          48%
          <i class="mdi mdi-arrow-up text-success"></i>
        </div>
        <vs-progress :percent="48" color="warning">warning</vs-progress>
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  name: "VisitCountry",
  data: () => ({
    title: "VisitCountry"
  })
};
</script>