<template>
  <div>
    <div class="mailbox">
      <div
        class="message-center overflow-auto position-relative"
        v-for="notification in notifications"
        :notification="notification"
        :key="notification.notificationtitle"
      >
        <!-- Message -->
        <a
          href="javascript:void(0)"
          class="d-flex align-items-center border-bottom d-block px-3 py-2"
        >
          <vs-button
            radius
            color
            :class="[notification.btnbg]"
            type="filled"
            :icon="notification.btnicon"
          ></vs-button>
          <div class="mail-contnet d-inline-block w-75 pl-2 align-middle">
            <h6 class="message-title mt-1">{{ notification.notificationtitle }}</h6>
            <span
              class="mail-desc d-block text-truncate overflow-hidden text-nowrap text-muted"
            >{{ notification.notificationdesc }}</span>
            <span
              class="time d-block text-truncate overflow-hidden text-nowrap text-muted"
            >{{ notification.notificationtime }}</span>
          </div>
        </a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "RecentNotification",
  props: {
    comment: Object
  },
  data: () => ({
    title: "RecentNotification",
    notifications: [
      {
        btnbg: "vs-button-primary",
        btnicon: "question_answer",
        notificationtitle: "Luanch Admin",
        notificationdesc: "Just see the my new admin!",
        notificationtime: "9:30 AM"
      },
      {
        btnbg: "vs-button-success",
        btnicon: "calendar_view_day",
        notificationtitle: "Event today",
        notificationdesc: "Just a reminder that you have event",
        notificationtime: "9:10 AM"
      },
      {
        btnbg: "vs-button-warning",
        btnicon: "perm_data_setting",
        notificationtitle: "Settings",
        notificationdesc: "You can customize this template as you want",
        notificationtime: "9:08 AM"
      },
      {
        btnbg: "vs-button-danger",
        btnicon: "face",
        notificationtitle: "Pavan kumar",
        notificationdesc: "Just see the my admin!",
        notificationtime: "9:02 AM"
      }
    ]
  })
};
</script>