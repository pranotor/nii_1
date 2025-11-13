<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>
        <div class="d-flex align-items-center">
          
            <vs-input placeholder="Search Contact Here" v-model="search" class="inputx" />
            <vs-button
              icon="add"
              color="primary"
              @click="activePrompt = true"
              class="flex-shrink-0   ml-auto"
            >Add Contact</vs-button>
            <!---Add Contact --->
            <vs-prompt
              color="primary"
              @cancel="title=''"
              @accept="acceptAlert"
              @close="close"
              :is-valid="validName"
              :active.sync="activePrompt"
              title="Add Contact"
            >
              <div v-bar class="vs-scrollable" style="height: 350px;">
                <div>
                  <div class="con-exemple-prompt px-4 py-3">
                    <vs-input label="Name" v-model="contactname" class="w-100 mb-4" />
                    <vs-input label="Role" v-model="role" class="w-100 mb-4" />
                    <vs-input label="Address" v-model="address" class="w-100 mb-4" />
                    <vs-input type="tel" label="Phone No" v-model="phone" class="w-100 mb-4" />
                    <vs-input
                      type="number"
                      label="Instagram Followers"
                      v-model="ifollow"
                      class="w-100 mb-4"
                    />
                    <vs-input
                      type="number"
                      label="Linkedin Followers"
                      v-model="lfollow"
                      class="w-100 mb-4"
                    />
                    <vs-input
                      type="number"
                      label="Facebook Followers"
                      v-model="ffollow"
                      class="w-100 mb-4"
                    />

                    <vs-upload action="https://jsonplaceholder.typicode.com/posts/" v-model="img" />
                    <vs-alert
                      :active="!validName"
                      color="danger"
                      icon="new_releases"
                      class="mb-3"
                    >Fields can not be empty please enter the data</vs-alert>
                  </div>
                </div>
              </div>
            </vs-prompt>
          
        </div>
      </vs-card>
      <vs-row>
        <vs-col
          type="flex"
          vs-justify="center"
          vs-align="center"
          vs-lg="4"
          vs-xs="12"
          vs-sm="6"
          v-for="(contactgrid, index) in contactgrids"
          :key="index"
        >
          <vs-card class="text-center">
            <img
              :src="require('@/assets/images/users'+ contactgrid.img)"
              alt="user"
              class="rounded-circle"
              width="100"
            />
            <h4 class="card-title mt-3 mb-0">{{ contactgrid.contactname }}</h4>
            <small class="text-muted">{{ contactgrid.role }}</small>
            <div class="mt-3">
              <vs-button color="primary" type="filled">Chat</vs-button>
              <vs-button color="success" type="filled" class="ml-3">Follow</vs-button>
            </div>
            <div class="mt-3 pt-3">
              <address class="mb-0">
                {{ contactgrid.address }}
                <br />
                <br />
                <i class="mdi mdi-cellphone-iphone mr-2"></i>
                {{ contactgrid.phone }}
              </address>
            </div>
            <vs-row class="bg-light border border-bottom-0" slot="footer">
              <vs-col vs-lg="4" vs-xs="4" vs-sm="4" class="border-bottom border-right py-2">
                <a class="text-muted" href="javascript:void(0)">
                  <i class="mdi mdi-instagram"></i>
                  {{ contactgrid.instafollowers }}
                </a>
              </vs-col>
              <vs-col vs-lg="4" vs-xs="4" vs-sm="4" class="border-bottom border-right py-2">
                <a class="text-muted" href="javascript:void(0)">
                  <i class="mdi mdi-linkedin-box"></i>
                  {{ contactgrid.linkedinfollowers }}
                </a>
              </vs-col>
              <vs-col vs-lg="4" vs-xs="4" vs-sm="4" class="border-bottom py-2">
                <a class="text-muted" href="javascript:void(0)">
                  <i class="mdi mdi-facebook-box"></i>
                  {{ contactgrid.fbfollowers }}
                </a>
              </vs-col>
            </vs-row>
          </vs-card>
        </vs-col>
      </vs-row>
    </vs-col>
  </vs-row>
</template>

<script>
export default {
  name: "ContactGrid",
  data: () => ({
    title: "ContactGrid",
    activePrompt: false,
    contactfilter: [
      {
        phone: "(123) 456 789",
        img: "/1.jpg",
        contactname: "Hanna Gover",
        role: "Designer",
        address: " 2289 5th Ave, Suite 600 San Francisco New York, NY, 10037 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(234) 456 789",
        img: "/2.jpg",
        contactname: "Daniel Kristeen",
        role: "Developer",
        address: " 55 E 11th St #1OTH, Suite 600 New York, NY, 10003 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(345) 456 789",
        img: "/3.jpg",
        contactname: "Julian Josephs",
        role: "Accountant",
        address: " 36 W 138th St, San Francisco New York, NY, 10037 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(456) 456 789",
        img: "/4.jpg",
        contactname: "Jan Petrovic",
        role: "Designer",
        address: " 2289 5th Ave, Suite 600 San Francisco New York, NY, 10037 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(567) 456 789",
        img: "/5.jpg",
        contactname: "Leanne Graham",
        role: "HR",
        address: " 425 5th Ave, San Francisco New York, NY, 10016 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(678) 456 789",
        img: "/6.jpg",
        contactname: "Mrs. Dennis Schulist",
        role: "Designer",
        address: " 17 Stuyvesant Walk, Suite 600 New York, NY, 10009 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(123) 456 789",
        img: "/1.jpg",
        contactname: "Kurtis Weissnat",
        role: "Manager",
        address: " 2289 5th Ave, Suite 600 San Francisco New York, NY, 10037 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(234) 456 789",
        img: "/2.jpg",
        contactname: "Nicholas Runolfsdottir V",
        role: "Chairman",
        address: " 425 5th Ave, San Francisco New York, NY, 10016 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(345) 456 789",
        img: "/3.jpg",
        contactname: "Glenna Reichert",
        role: "Designer",
        address: " 2289 5th Ave, Suite 600 San Francisco New York, NY, 10037 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      },
      {
        phone: "(456) 456 789",
        img: "/4.jpg",
        contactname: "Clementina DuBuque",
        role: "Developer",
        address: " 55 E 11th St #1OTH, Suite 600 New York, NY, 10003 ",
        instafollowers: "12",
        linkedinfollowers: "20",
        fbfollowers: "21"
      }
    ],
    id: "",
    contactname: "",
    phone: "",
    role: "",
    img: "",
    address: "",
    ifollow: "",
    lfollow: "",
    ffollow: "",
    search: ""
  }),
  methods: {
    acceptAlert() {
      this.$vs.notify({
        color: "primary",
        title: "Contact Added",
        text: "Contact Added Successfully"
      });
    },
    close() {
      this.$vs.notify({
        color: "danger",
        title: "Closed",
        text: "You close a dialog!"
      });
    }
  },
  computed: {
    events() {
      return this.$store.state.contactEvents;
    },
    validName() {
      return this.contactname.length > 0;
    },
    contactgrids() {
      return this.contactfilter.filter(
        contactgrid =>
          contactgrid.contactname
            .toLowerCase()
            .includes(this.search.toLowerCase()) ||
          contactgrid.role.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  components: {}
};
</script>
