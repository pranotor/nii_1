<template>
  <div class="emailapp">
    <vs-row>
      <vs-col>
        <vs-card class="mb-0">
          <div class="left-part">
            <vs-sidebar
              :click-not-close="doNotClose"
              :hidden-background="doNotClose"
              v-model="emailSidebarActive"
            >
              <div class="pt-2 pl-3 pr-3 pb-3 border-bottom">
                <vs-button color="danger" class="w-100" @click="composePrompt = !composePrompt" type="filled">Compose</vs-button>
              </div>
              <EmailCompose :composePrompt.sync="composePrompt" class="email-compose"/>
              <!---
                User List
              --->
              <div v-bar class="vs-scrollable">
                <div>
                  <ul class="common-list email-nav mt-3">
                    <li @click="sortMail('Inbox')" :class="{ active : active_el == 'Inbox'}">
                      <vs-icon icon-pack="mdi mdi-inbox mr-2"></vs-icon>Inbox <span class="ml-auto p-1 badge badge-success">{{ inboxCount }}</span>
                    </li>
                    <li @click="sortMail('Sent')" :class="{ active : active_el == 'Sent'}">
                      <vs-icon icon-pack="mdi mdi-send mr-2"></vs-icon>Sent
                    </li>
                    <li @click="sortMail('Spam')" :class="{ active : active_el == 'Spam'}">
                      <vs-icon icon-pack="mdi mdi-star mr-2"></vs-icon>Spam 
                    </li>
                    <li @click="sortMail('Draft')" :class="{ active : active_el == 'Draft'}">
                      <vs-icon icon-pack="mdi mdi-email mr-2"></vs-icon>Draft
                    </li>
                    
                  </ul>
                </div>
              </div>
              <!---
                            User List
              --->
            </vs-sidebar>
          </div>
          <div class="right-part bg-light">
            <template>
              <!---
              User Navbar
              --->
              <div class="border-bottom d-flex p-3 align-items-center">
                <div
                  class="hiddenDesktop cursor-pointer mr-2"
                  @click.stop="toggleEmailSidebar(true)"
                >
                  <vs-icon icon="menu"></vs-icon>
                </div>
                <div>
                  <h3>MailBox</h3>
                  <small>Here is the list of mail</small>
                </div>
              </div>
              <!---
              User Navbar
              --->
              <!---
              Mail Room
              --->
              <div v-bar class="vs-scrollable" height="">
                <div>
                  <div class="mail-room">
                    <!---
                     Mail List
                    ----->
                    <vs-table v-if="emails.length > 1" class="mail-table text-nowrap" multiple v-model="selected" pagination search
                      :data="emails">
                      <template slot="thead">
                        <vs-th></vs-th>
                        <vs-th></vs-th>
                        <vs-th></vs-th>
                        
                        <vs-th></vs-th>
                      </template>
                      <template slot-scope="{data}">
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                          
                          <vs-td class="email-name">
                            <div class="d-flex align-items-center vs-td-space" @click="updateOpenMail(data[indextr].id)">
                              <img
                                :src="require('@/assets/images/users/'+ data[indextr].img)"
                                class="rounded-circle mr-2"
                                alt="user"
                                width="30"
                              />
                              <span class="font-weight-bold">{{data[indextr].from_name}}</span>
                            </div>
                          </vs-td>
                          <vs-td>
                            <div class="vs-td-space d-flex" @click="updateOpenMail(data[indextr].id)">
                            <span class="mr-2 label" :class="'badge-'+data[indextr].label_color">{{data[indextr].labels}}</span>
                            <span class="mail-description">{{data[indextr].message}}</span>
                            </div> 
                          </vs-td>
                          <vs-td class="text-center"><vs-icon v-if="data[indextr].attachments" icon-pack="mdi mdi-attachment"></vs-icon></vs-td>
                          <vs-td class="text-right">{{data[indextr].time}}</vs-td>
                        </vs-tr>
                      </template>
                    </vs-table>
                    <div v-else class="d-flex justify-content-center align-items-center h-100 bg-light">
                      <h4 @click.stop="toggleEmailSidebar(true)">No Email Found</h4>
                    </div>
                    <!---
                    Mail List
                    --->
                    
                  </div>
                </div>
              </div>

              <!---
              Mail Room
              --->
              <div v-if="activePrompt" :activePrompt="activePrompt" class="email-detail bg-light">
                <div @click="activePrompt=false" class="cursor-pointer d-flex align-items-center">
                  <vs-icon icon-pack="mdi mdi-arrow-left mr-3"></vs-icon> 
                  <h3>{{ currentEmail.subject}}</h3>
                  <span class="ml-2 label" :class="'badge-'+currentEmail.label_color">{{currentEmail.labels}}</span>
                  </div>
                <EmailDetail  :openCurrentId = "openCurrentId"/>
              </div>
            </template>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import EmailDetail from './EmailDetail.vue'
import EmailCompose from './EmailCompose.vue'
export default {
  name: "Email",
  components: {
    EmailDetail,
    EmailCompose
  },
  data: () => ({
    emailSidebarActive: true,
    doNotClose: true,
    windowWidth: window.innerWidth,
    selected: [],
    isClass: true,
    close:'',
    active_el: "Inbox",
    activePrompt: false,
    composePrompt:false,
    openCurrentId: null
  }),
  methods: {
    updateOpenMail(emailId){
      this.openCurrentId = emailId;
      this.activePrompt = true;   
    },
    openMessages() {
      if (this.windowWidth < 1170) {
        this.toggleEmailSidebar();
      }
    },
    handleWindowResize(event) {
      this.windowWidth = event.currentTarget.innerWidth;
      this.setSidebarWidth();
    },
    setSidebarWidth() {
      if (this.windowWidth < 1170) {
        this.emailSidebarActive = this.doNotClose = false;
      } else {
        this.emailSidebarActive = this.doNotClose = true;
      }
    },
    toggleEmailSidebar(value = false) {
      if (!value && this.clickNotClose) return;
      this.emailSidebarActive = value;
    },
    sortMail(filterCategory) {
      this.$store.dispatch("filteredMail", filterCategory);
      this.active_el = filterCategory;
      this.activePrompt = false;
    }
  },
  computed: {
    emails() {
      return this.$store.getters["getFilteredMails"];
    },
    inboxCount() {
      return this.$store.getters["getInboxCount"];
    },
    isMailOpen() {
          return (emailId) => emailId == this.openCurrentId;
    },
    currentEmail() {
            return this.$store.getters['getCurrentMail'](this.openCurrentId)
        },
  },
  created() {
    this.$nextTick(() => {
      window.addEventListener("resize", this.handleWindowResize);
    });
    this.setSidebarWidth();
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleWindowResize);
  }
};
</script>