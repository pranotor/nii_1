<template>
  <div>
    <!-- Header -->
    <div class="d-md-flex align-items-center">
      <div class="w-100">
        <div v-bar class="vs-scrollable" style="height:500px">
          <div>
            <ul ref="chatLog" class="p-3">
              <li
                class="d-flex align-items-start"
                :class="{'flex-row-reverse': chat.fromMe, 'mt-4': index}"
                v-for="(chat, index) in chatConversation"
                :key="index"
              >
                <img
                  class="m-0 flex-no-shrink rounded-circle"
                  :class="chat.fromMe ? 'ml-3' : 'mr-3'"
                  width="37"
                  :src="require(`@/assets/images/users/${chat.conversationImg}`)"
                />
                <div
                  class="chat p-3 mb-2 rounded-lg"
                  :class="{'chat-sent-chat bg-primary text-white': chat.fromMe, 'bg-light': !chat.fromMe}"
                >
                  <span>{{ chat.chat }}</span>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex border-top pt-3">
          <input
            class="w-100 border-0"
            placeholder="Type Your Message Here"
            v-model="newMessage"
            type="text"
            @keyup.enter="addMessage"
          />
          <vs-button
            radius
            color="success"
            type="filled"
            icon="send"
            class="float-right"
            @click="addMessage"
          ></vs-button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "RecentChats",
  data: () => ({
    title: "RecentChats",
    newMessage: "",
    chatConversation: [
      {
        conversationImg: "1-old.jpg",
        chat:
          "Lorem Ipsum is simply dummy text of the printing & type setting industry.",
        fromMe: true
      },
      {
        conversationImg: "2.jpg",
        chat: "It’s Great opportunity to work.",
        fromMe: false
      },
      { conversationImg: "1-old.jpg", chat: "Yes", fromMe: true },
      {
        conversationImg: "2.jpg",
        chat: "I would love to join the team.",
        fromMe: false
      },
      {
        conversationImg: "1-old.jpg",
        chat: "Whats budget of the new project.",
        fromMe: true
      },
      {
        conversationImg: "2.jpg",
        chat: "Well we have good budget for the project",
        fromMe: false
      }
    ]
  }),
  methods: {
    addMessage() {
      if (this.newMessage) {
        this.chatConversation.push({
          conversationImg: "1-old.jpg",
          chat: this.newMessage,
          fromMe: true
        });
        this.newMessage = "";
      }
    }
  }
};
</script>