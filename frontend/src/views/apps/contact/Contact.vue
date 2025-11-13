<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>
        <vs-table
          id="contact-table"
          ref="table"
          stripe
          multiple
          v-model="selected"
          pagination
          max-items="5"
          search
          :data="contacts"
          class="text-nowrap"
        >
          <div slot="header">
            <vs-button icon="add" color="primary" @click="activePrompt = true">Add Contact</vs-button>
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
                    <vs-input label="Id" v-model="id" class="w-100 mb-4" />

                    <vs-input label="Name" v-model="contactname" class="w-100 mb-4" />
                    <vs-input type="email" label="Email" v-model="contactmail" class="w-100 mb-4" />
                    <vs-input type="tel" label="Phone No" v-model="phone" class="w-100 mb-4" />
                    <vs-input
                      type="date"
                      label="Joining Date"
                      v-model="joindate"
                      class="w-100 mb-4"
                    />
                    <vs-input label="Role" v-model="role" class="w-100 mb-4" />
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
          <template slot="thead">
            <vs-th sort-key="id">Id</vs-th>
            <vs-th sort-key="contactname">User Info</vs-th>
            <vs-th sort-key="phone">Phone</vs-th>
            <vs-th sort-key="joindate">Joining Date</vs-th>
            <vs-th sort-key="role">Role</vs-th>
          </template>

          <template slot-scope="{data}">
            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
              <vs-td :data="data[indextr].id">
                <div>
                  <span
                    class="badge badge-light badge-pill border border-muted"
                  >{{data[indextr].id}}</span>
                </div>
              </vs-td>
              <vs-td :data="data[indextr].contactname">
                <div class="d-flex no-block align-items-center">
                  <div class="mr-2">
                    <img
                      :src="require('@/assets/images/users/'+ data[indextr].img)"
                      alt="user"
                      class="rounded-circle"
                      width="45"
                    />
                  </div>
                  <div class>
                    <h5 class="mb-0 font-16">{{ data[indextr].contactname }}</h5>
                    <span class="text-muted">{{ data[indextr].contactmail }}</span>
                  </div>
                </div>
              </vs-td>

              <vs-td :data="data[indextr].phone">
                <div class="d-flex align-items-center">
                  <i class="mdi mdi-phone-classic mr-2 display-8"></i>
                  <span class="text-muted">{{data[indextr].phone}}</span>
                </div>
              </vs-td>

              <vs-td :data="data[indextr].joindate">
                <div class="d-flex align-items-center">
                  <i class="mdi mdi-calendar-text mr-2 display-8"></i>
                  <span class="text-muted">{{data[indextr].joindate}}</span>
                </div>
              </vs-td>

              <vs-td :data="data[indextr].role">
                <span :class="data[indextr].rolebg">{{data[indextr].role}}</span>
              </vs-td>
            </vs-tr>
          </template>
        </vs-table>
      </vs-card>
    </vs-col>
  </vs-row>
</template>

<script>
export default {
  name: "Contact",
  data: () => ({
    title: "Contact",
    activePrompt: false,
    contacts: [
      {
        id: 1,
        phone: "+123 456 789",
        img: "1.jpg",
        contactname: "Hanna Gover",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "12-10-2014"
      },
      {
        id: 2,
        phone: "+234 456 789",
        img: "2.jpg",
        contactname: "Daniel Kristeen",
        contactmail: "Kristeen@gmail.com",
        rolebg: "badge badge-pill badge-info text-white",
        role: "Developer",
        joindate: "10-09-2014"
      },
      {
        id: 3,
        phone: "+345 456 789",
        img: "3.jpg",
        contactname: "Julian Josephs",
        contactmail: "Josephs@gmail.com",
        rolebg: "badge badge-pill badge-success",
        role: "Accountant",
        joindate: "01-10-2013"
      },
      {
        id: 4,
        phone: "+456 456 789",
        img: "4.jpg",
        contactname: "Jan Petrovic",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "02-10-2017"
      },
      {
        id: 5,
        phone: "+567 456 789",
        img: "5.jpg",
        contactname: "Leanne Graham",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-dark",
        role: "HR",
        joindate: "10-9-2015"
      },
      {
        id: 6,
        phone: "+678 456 789",
        img: "6.jpg",
        contactname: "Mrs. Dennis Schulist",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "10-5-2013"
      },
      {
        id: 7,
        phone: "+123 456 789",
        img: "1.jpg",
        contactname: "Kurtis Weissnat",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-primary",
        role: "Manager",
        joindate: "05-10-2012"
      },
      {
        id: 8,
        phone: "+234 456 789",
        img: "2.jpg",
        contactname: "Nicholas Runolfsdottir V",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-warning text-white",
        role: "Chairman",
        joindate: "11-10-2014"
      },
      {
        id: 9,
        phone: "+345 456 789",
        img: "3.jpg",
        contactname: "Glenna Reichert",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "12-5-2017"
      },
      {
        id: 10,
        phone: "+456 456 789",
        img: "4.jpg",
        contactname: "Clementina DuBuque",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-info text-white",
        role: "Developer",
        joindate: "18-5-2009"
      },
      {
        id: 11,
        phone: "+123 456 789",
        img: "1.jpg",
        contactname: "Hudson Smith",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "12-10-2014"
      },
      {
        id: 12,
        phone: "+234 456 789",
        img: "2.jpg",
        contactname: "Hunter Kristeen",
        contactmail: "Kristeen@gmail.com",
        rolebg: "badge badge-pill badge-info text-white",
        role: "Developer",
        joindate: "10-09-2014"
      },
      {
        id: 13,
        phone: "+345 456 789",
        img: "3.jpg",
        contactname: "Jack Josephs",
        contactmail: "Josephs@gmail.com",
        rolebg: "badge badge-pill badge-success",
        role: "Accountant",
        joindate: "01-10-2013"
      },
      {
        id: 14,
        phone: "+456 456 789",
        img: "4.jpg",
        contactname: "Jaxon Petrovic",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "02-10-2017"
      },
      {
        id: 15,
        phone: "+567 456 789",
        img: "5.jpg",
        contactname: "Addison Graham",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-dark",
        role: "HR",
        joindate: "10-9-2015"
      },
      {
        id: 16,
        phone: "+678 456 789",
        img: "6.jpg",
        contactname: "Carter Schulist",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "10-5-2013"
      },
      {
        id: 17,
        phone: "+123 456 789",
        img: "1.jpg",
        contactname: "Christian Weissnat",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-primary",
        role: "Manager",
        joindate: "05-10-2012"
      },
      {
        id: 18,
        phone: "+234 456 789",
        img: "2.jpg",
        contactname: "Colton Runolfsdottir",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-warning text-white",
        role: "Chairman",
        joindate: "11-10-2014"
      },
      {
        id: 19,
        phone: "+345 456 789",
        img: "3.jpg",
        contactname: "Grayson Reichert",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-danger",
        role: "Designer",
        joindate: "12-5-2017"
      },
      {
        id: 20,
        phone: "+456 456 789",
        img: "4.jpg",
        contactname: "Hudson DuBuque",
        contactmail: "hgover@gmail.com",
        rolebg: "badge badge-pill badge-info text-white",
        role: "Developer",
        joindate: "18-5-2009"
      }
    ],
    selected: [],
    id: "",
    contactname: "",
    contactmail: "",
    phone: "",
    joindate: "",
    role: "",
    img: ""
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
      return this.id.length > 0;
    }
  },
  components: {}
};
</script>
