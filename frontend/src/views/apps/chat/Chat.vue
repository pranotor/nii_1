<template>
  <div class="chatapp">
    <vs-row>
        <vs-col>
            <vs-card class="mb-0">
                <div class="left-part">
                    <vs-sidebar  :click-not-close="doNotClose" :hidden-background="doNotClose" v-model="chatSidebarActive">    
                        <div class="pt-2 pl-3 pr-3 pb-3  border-bottom">
                            <vs-input icon-no-border icon="search" placeholder="Search contacts" v-model="handleSearchInput" class="w-100"/>
                        </div>    
                            <!---
                            User List
                            --->
                            <div v-bar class="vs-scrollable">
                            <div>
                                <ul class="chat-userlist common-list" >
                                    <li v-for="(conversation, index) in filteredList" 
                                    :key="index" 
                                    @click="(e) => openMessages(conversation, e)" >
                                        <vs-avatar class="" :src="require(`@/assets/images/users/${conversation.image}`)" size="42px"></vs-avatar>
                                        <div class="user-about"><h6>{{conversation.name}}</h6>
                                        <span >{{conversation.lastMessage}}</span></div>
                                    </li>
                                </ul>
                            </div>
                            </div>
                            <!---
                            User List
                            --->
                        
                    </vs-sidebar>
                    </div>
                    <div class="right-part chat-container">
                        <template  v-if="conversation">
                            <!---
                            User Navbar
                            --->
                            <div class="chat-topbar border-bottom d-flex p-3 align-items-center">
                                <div class="hiddenDesktop cursor-pointer mr-2" @click.stop="toggleChatSidebar(true)">
                                  <vs-icon icon="menu"></vs-icon>
                                </div>
                                <img class="rounded-circle mr-2" :src="require(`@/assets/images/users/${conversation.image}`)" width="37" />
                                <div class="user-about"><h6>{{conversation.name}}</h6>
                                </div>
                            </div>        
                            <!---
                            User Navbar
                            --->
                            <!---
                            Chat Room
                            --->
                            <div v-bar class="vs-scrollable">
                            <div>    
                            <div class="chat-room bg-light">
                                
                                <div class="d-flex align-items-center mb-3" v-for="message in conversation.messages" :key="message.id" :class="{fromMe: message.fromMe, 'messageItem': true}">
                                    <span class="thumb">
                                        <img v-if="message.fromMe"  class="rounded-circle" src="@/assets/images/users/1-old.jpg" alt="..." width="37"/>
                                        <img v-else
                                            class="rounded-circle" :src="require(`@/assets/images/users/${conversation.image}`)" alt="..." width="37"/>
                                        </span>
                                    <div class="messageBody">{{message.text}}</div>
                                </div>    
                            </div>
                                
                            </div>
                            </div>
                            <div class="send-message">
                                <input placeholder="Type and hit enter" class="form-control w-100" type="text" @keydown="addMessage" />
                            </div>    
                            <!---
                            Chat Room
                            --->
                        </template>
                        <template v-else>
                            <div class="d-flex justify-content-center align-items-center h-100 bg-light"><h4 @click.stop="toggleChatSidebar(true)">Start conversation</h4></div>
                        </template>
                    </div>    
                
            </vs-card>
        </vs-col>
    </vs-row>
  </div>
</template>

<script>
import Vue from 'vue';
import UsersList from './userslist.js'
export default {
  name: 'Chat',
  components: {
  },
  data:()=>({
      chatSidebarActive:true, 
      doNotClose:true,
      handleSearchInput:"",
      UsersList: UsersList,
      chatUserActive: true,
      conversation: "",
      sendMessage:" ",
      windowWidth: window.innerWidth,
  }),
  methods: {
    openMessages(conversation) {
      Vue.set(this, 'conversation', conversation);
      if(this.windowWidth < 1170){
          this.toggleChatSidebar();
      }
    },
    addMessage(e) {
      if (e.key === 'Enter' && e.target.value) {
        const value = {
          text: e.target.value,
          fromMe: true,
        };

        Vue.set(this, 'conversation', Object.assign({}, this.conversation, {
          messages: [
            ...this.conversation.messages || [],
            value,
          ],
        }));

        e.target.value = ''; 
      }
    },
    handleWindowResize(event) {
            this.windowWidth = event.currentTarget.innerWidth;
            this.setSidebarWidth();
    },
    setSidebarWidth() {
        if(this.windowWidth < 1170) {
            this.chatSidebarActive = this.doNotClose = false;
        }else {
            this.chatSidebarActive = this.doNotClose =  true;
        }
    },
    toggleChatSidebar(value = false) {
        if(!value && this.clickNotClose) return
        this.chatSidebarActive = value;
    }
  },
  computed:{
      filteredList() {
        return this.UsersList.filter(user => {
            return user.name.toLowerCase().includes(this.handleSearchInput.toLowerCase())
        }) 
    },
  },
  created() {
        this.$nextTick(() => {
            window.addEventListener('resize', this.handleWindowResize);
        })
        this.setSidebarWidth();
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleWindowResize)
    },
  
}
</script>