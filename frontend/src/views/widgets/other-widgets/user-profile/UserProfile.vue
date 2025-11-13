<template>
  <div>
    <div class="d-flex flex-row">
      <div class>
        <img src="@/assets/images/users/1.jpg" alt="user" class="rounded-circle" width="100" />
      </div>
      <div class="pl-3">
        <h3 class="mb-2">Daniel Kristeen</h3>
        <h6 class="mb-3">UIUX Designer</h6>
        <vs-button color="success" type="filled">
          <i class="ti-plus"></i> Follow
        </vs-button>
      </div>
    </div>
    <vs-row class="my-5">
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <h2 class="font-weight-light">14</h2>
        <h6>Photos</h6>
      </vs-col>
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <h2 class="font-weight-light">54</h2>
        <h6>Videos</h6>
      </vs-col>
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <h2 class="font-weight-light">145</h2>
        <h6>Tasks</h6>
      </vs-col>
    </vs-row>
    <hr class="custom-hr" />
    <p
      class="text-center mt-4"
    >Lorem ipsum dolor sit ametetur adipisicing elit, sed do eiusmod tempor incididunt adipisicing elit, sed do eiusmod tempor incididunLorem ipsum dolor sit ametetur adipisicing elit, sed do eiusmod tempor incididuntt</p>
    <vs-row>
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <vs-tooltip text="Website">
          <a href="javascript:void(0)" class="text-dark display-8">
            <i class="ti-world"></i>
          </a>
        </vs-tooltip>
      </vs-col>
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <vs-tooltip text="Twitter">
          <a
            href="javascript:void(0)"
            class="text-dark display-8"
            data-toggle="tooltip"
            title="twitter"
          >
            <i class="ti-twitter"></i>
          </a>
        </vs-tooltip>
      </vs-col>
      <vs-col
        type="flex"
        vs-justify="center"
        vs-align="center"
        vs-lg="4"
        vs-xs="4"
        class="text-center"
      >
        <vs-tooltip text="Facebook">
          <a
            href="javascript:void(0)"
            class="text-dark display-8"
            data-toggle="tooltip"
            title="Facebook"
          >
            <i class="ti-facebook"></i>
          </a>
        </vs-tooltip>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
export default {
  name: "UserProfile",
  data: () => ({
    title: "UserProfile"
  })
};
</script>