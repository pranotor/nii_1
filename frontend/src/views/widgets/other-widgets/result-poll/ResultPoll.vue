<template>
  <div>
    <ul class="list-inline mt-3 mb-2">
      <li>
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0 font-weight-bold">
              A
              <span class="font-weight-light">30 Minutes</span>
            </h6>
          </div>
          <div class="ml-auto">
            <h6 class="mb-0 font-weight-bold">55%</h6>
          </div>
        </div>
        <vs-progress :percent="55" class="mt-2" color="primary">primary</vs-progress>
      </li>
      <li class="mt-4">
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0 font-weight-bold">
              B
              <span class="font-weight-light">More than 30 minutes</span>
            </h6>
          </div>
          <div class="ml-auto">
            <h6 class="mb-0 font-weight-bold">20%</h6>
          </div>
        </div>
        <vs-progress :percent="20" class="mt-2" color="success">success</vs-progress>
      </li>
      <li class="mt-4">
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0 font-weight-bold">
              C
              <span class="font-weight-light">1 Hour</span>
            </h6>
          </div>
          <div class="ml-auto">
            <h6 class="mb-0 font-weight-bold">15%</h6>
          </div>
        </div>
        <vs-progress :percent="15" class="mt-2" color="danger">danger</vs-progress>
      </li>
      <li class="mt-4">
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0 font-weight-bold">
              D
              <span class="font-weight-light">More than 1 hour</span>
            </h6>
          </div>
          <div class="ml-auto">
            <h6 class="mb-0 font-weight-bold">10%</h6>
          </div>
        </div>
        <vs-progress :percent="10" class="mt-2" color="warning">warning</vs-progress>
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  name: "ResultPoll",
  data: () => ({
    title: "ResultPoll"
  })
};
</script>