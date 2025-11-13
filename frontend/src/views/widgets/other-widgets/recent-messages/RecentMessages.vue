<template>
  <div>
    <div class="mailbox">
      <div
        class="message-center overflow-auto position-relative"
        v-for="chat in chats"
        :chat="chat"
        :key="chat.chattitle"
      >
        <!-- Message -->
        <a
          href="javascript:void(0)"
          class="d-flex align-items-center border-bottom d-block px-3 py-2"
        >
          <span class="user-img position-relative d-inline-block">
            <img
              :src="require('@/assets/images/users'+ chat.img)"
              alt="user"
              class="rounded-circle"
            />
            <span
              class="profile-status pull-right d-inline-block position-absolute"
              :class="[chat.userstatus]"
            ></span>
          </span>
          <div class="mail-contnet d-inline-block w-75 pl-2 align-middle">
            <h6 class="message-title mt-1">{{ chat.msgtitle }}</h6>
            <span
              class="mail-desc d-block text-truncate overflow-hidden text-nowrap text-muted"
            >{{ chat.msgdesc }}</span>
            <span
              class="time d-block text-truncate overflow-hidden text-nowrap text-muted"
            >{{ chat.msgtime }}</span>
          </div>
        </a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "RecentMessages",
  props: {
    comment: Object
  },
  data: () => ({
    title: "RecentMessages",
    // Data For Chats
    chats: [
      {
        img: "/3.jpg",
        userstatus: "online",
        msgtitle: "Pavan kumar",
        msgdesc: "Just see the my admin!",
        msgtime: "9:30 AM"
      },
      {
        img: "/1.jpg",
        userstatus: "busy",
        msgtitle: "Sonu Nigam",
        msgdesc: "I have sung a song! See you at",
        msgtime: "9:10 AM"
      },
      {
        img: "/2.jpg",
        userstatus: "away",
        msgtitle: "Arijit Sinh",
        msgdesc: "I am a singer!",
        msgtime: "9:08 AM"
      },
      {
        img: "/3.jpg",
        userstatus: "away",
        msgtitle: "Arijit Sinh",
        msgdesc: "I am a singer!",
        msgtime: "9:08 AM"
      },
      {
        img: "/4.jpg",
        userstatus: "offline",
        msgtitle: "Pavan kumar",
        msgdesc: "Just see the my admin!",
        msgtime: "9:30 AM"
      }
    ]
  })
};
</script>