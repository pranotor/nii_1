<template>
  <div>
    <ul
      class="message-center overflow-auto position-relative mb-0"
      v-for="feeds in feedss"
      :feeds="feeds"
      :key="feeds.feedstitle"
    >
      <!-- Message -->
      <li class="d-flex align-items-center py-2 mb-2">
        <vs-button radius color :class="[feeds.btnbg]" type="filled" :icon="feeds.btnicon"></vs-button>
        <div class="ml-3">
          <span>{{ feeds.feedsdesc }}</span>
        </div>
        <div class="ml-auto">
          <span class="text-muted font-12">{{ feeds.feeddate }}</span>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  name: "Feeds",
  props: {
    comment: Object
  },
  data: () => ({
    title: "Feeds",
    feedss: [
      {
        btnbg: "vs-button-primary",
        btnicon: "all_inbox",
        feedsdesc: "You have 4 pending tasks.",
        feeddate: "Just Now"
      },
      {
        btnbg: "vs-button-success",
        btnicon: "view_agenda",
        feedstitle: "Event today",
        feedsdesc: "Server #1 overloaded.",
        feeddate: "2 Hours ago"
      },
      {
        btnbg: "vs-button-warning",
        btnicon: "add_shopping_cart",
        feedstitle: "Settings",
        feedsdesc: "New order received.",
        feeddate: "31 May"
      },
      {
        btnbg: "vs-button-danger",
        btnicon: "face",
        feedstitle: "Pavan kumar",
        feedsdesc: "New user registered.",
        feeddate: "30 May"
      },
      {
        btnbg: "vs-button-dark",
        btnicon: "all_inbox",
        feedsdesc: "New Version just arrived.",
        feeddate: "27 May"
      },
      {
        btnbg: "vs-button-primary",
        btnicon: "all_inbox",
        feedsdesc: "You have 4 pending tasks.",
        feeddate: "Just Now"
      },
      {
        btnbg: "vs-button-danger",
        btnicon: "face",
        feedsdesc: "New user registered",
        feeddate: "30 May"
      }
    ]
  })
};
</script>