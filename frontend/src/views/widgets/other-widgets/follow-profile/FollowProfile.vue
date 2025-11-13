<template>
  <div>
    <div class="profile-pic mb-3 mt-3">
      <img src="@/assets/images/users/5.jpg" width="150" class="rounded-circle" alt="user" />
      <h4 class="mt-3 mb-0">Daniel Kristeen</h4>
      <a href="mailto:danielkristeen@gmail.com">danielkristeen@gmail.com</a>
    </div>
    <div class="d-flex justify-content-center">
      <div class="badge badge-pill badge-light px-3 py-2">Dashboard</div>
      <div class="badge badge-pill badge-light px-3 py-2">UI</div>
      <div class="badge badge-pill badge-light px-3 py-2">UX</div>
      <vs-tooltip text="3 More">
        <div class="badge badge-pill badge-primary px-3 py-2">+3</div>
      </vs-tooltip>
    </div>
    <hr class="custom-hr" />
    <div class="px-3 py-2">
      <vs-row>
        <vs-col
          type="flex"
          vs-justify="center"
          vs-align="center"
          vs-lg="6"
          vs-xs="6"
          class="text-center border-right"
        >
          <a href="#" class="text-dark d-flex align-items-center justify-content-center">
            <i class="mdi mdi-message mr-1"></i>Message
          </a>
        </vs-col>
        <vs-col
          type="flex"
          vs-justify="center"
          vs-align="center"
          vs-lg="6"
          vs-xs="6"
          class="text-center"
        >
          <a href="#" class="text-dark d-flex align-items-center justify-content-center">
            <i class="mdi mdi-developer-board mr-1"></i>Portfolio
          </a>
        </vs-col>
      </vs-row>
    </div>
  </div>
</template>
<script>
export default {
  name: "FollowProfile",
  data: () => ({
    title: "FollowProfile"
  })
};
</script>