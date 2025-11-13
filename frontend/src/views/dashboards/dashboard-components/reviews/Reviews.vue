<template>
  <div>
    <vs-row>
      <vs-col vs-lg="4" vs-xs="12" vs-sm="4" class="border-right">
        <h4 class="card-title">Reviews</h4>
        <h5 class="card-subtitle font-weight-normal text-muted">Overview of Review</h5>
        <h2 class="font-medium mt-5 mb-0">25426</h2>
        <span class="text-muted">This month we got 346 New Reviews</span>
        <div class="image-box mt-4 mb-4 d-flex">
          <vs-tooltip
            :text="review.tooltiptext"
            v-for="review in reviews"
            :review="review"
            :key="review.reviewtitle"
          >
            <a href="javascript:void(0)" class="mr-2">
              <img
                :src="require('@/assets/images/users'+ review.img)"
                alt="user"
                class="rounded-circle"
                width="45"
              />
            </a>
          </vs-tooltip>
        </div>
        <vs-button size="large" class="px-3">Checkout All Reviews</vs-button>
      </vs-col>
      <vs-col vs-lg="8" vs-xs="12" vs-sm="8">
        <ul class="list-inline ml-4">
          <li
            class="mt-4"
            v-for="reaction in reactions"
            :reaction="reaction"
            :key="reaction.reactiontitle"
          >
            <div class="d-flex align-items-center">
              <i class="display-4 text-muted" :class="[reaction.clientreview]"></i>
              <div class="ml-2">
                <h5 class="mb-0">{{ reaction.reviewtext }}</h5>
                <span class="text-muted">{{ reaction.reviewno }}</span>
              </div>
            </div>
            <vs-progress :percent="reaction.percent" :class="[reaction.reviewcolor]">primary</vs-progress>
          </li>
        </ul>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
export default {
  name: "Reviews",
  props: {
    review: Object
  },
  data: () => ({
    title: "Reviews",
    reviews: [
      {
        tooltiptext: "Simmons",
        img: "/1.jpg"
      },
      {
        tooltiptext: "Fitz",
        img: "/2.jpg"
      },
      {
        tooltiptext: "Phil",
        img: "/3.jpg"
      },
      {
        tooltiptext: "Melinda",
        img: "/4.jpg"
      }
    ],
    reactions: [
      {
        clientreview: "mdi mdi-emoticon-happy",
        reviewtext: "Positive Reviews",
        reviewno: "25547 Reviews",
        percent: 47,
        reviewcolor: "vs-progress-success"
      },
      {
        clientreview: "mdi mdi-emoticon-sad",
        reviewtext: "Negative Reviews",
        reviewno: "5547 Reviews",
        percent: 33,
        reviewcolor: "vs-progress-warning"
      },
      {
        clientreview: "mdi mdi-emoticon-neutral",
        reviewtext: "Neutral Reviews",
        reviewno: "547 Reviews",
        percent: 20,
        reviewcolor: "vs-progress-primary"
      }
    ]
  })
};
</script>