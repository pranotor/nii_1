<template>
  <div>
    <vs-table :data="monthtable" class="text-nowrap">
      <template slot="thead">
        <vs-th>Team Lead</vs-th>
        <vs-th>Project</vs-th>
        <vs-th>Team</vs-th>
        <vs-th>Status</vs-th>
        <vs-th>Weeks</vs-th>
        <vs-th>Budget</vs-th>
      </template>

      <template slot-scope="{data}">
        <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td>
            <div class="d-flex no-block align-items-center">
              <div class="mr-2">
                <img
                  :src="require('@/assets/images/users'+ data[indextr].img)"
                  alt="user"
                  class="rounded-circle"
                  width="45"
                />
              </div>
              <div class>
                <h5 class="mb-0 font-16 font-medium">{{ data[indextr].leadname }}</h5>
                <span>{{ data[indextr].leademail }}</span>
              </div>
            </div>
          </vs-td>
          <vs-td>{{ data[indextr].projectname }}</vs-td>
          <vs-td class="tag-item">
            <div
              class="popover-icon"
              v-for="tagbtn in data[indextr].tagbtns"
              :tagbtn="tagbtn"
              :key="tagbtn.tagbtntitle"
            >
              <vs-button
                radius
                color
                v-bind:class="[tagbtn.btnbgClass, tagbtn.hoverClass, tagbtn.btntype]"
                type
                size="large"
              >{{ tagbtn.btntext }}</vs-button>
            </div>
          </vs-td>
          <vs-td>
            <div class="d-flex align-items-center">
              <vs-tooltip :text="data[indextr].statustext" :color="data[indextr].statuscolor">
                <i class="mdi mdi-checkbox-blank-circle" :class="[data[indextr].status]"></i>
              </vs-tooltip>
            </div>
          </vs-td>
          <vs-td>{{ data[indextr].week }}</vs-td>
          <vs-td>
            <span class="font-medium">{{ data[indextr].money }}</span>
          </vs-td>
        </vs-tr>
      </template>
    </vs-table>
  </div>
</template>
<script>
export default {
  name: "MonthTable",
  data: () => ({
    title: "MonthTable",

    // Data for data[indextr]
    monthtable: [
      {
        img: "/5.jpg",
        leadname: "Ujang Karang",
        leademail: "hgover@gmail.com",
        projectname: "W-12345",
        tagbtns: [
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "SS"
          },
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-success",
            hoverClass: "popover-item",
            btntext: "DS"
          },
          {
            btntype: "vs-button-filled",
            btnbgClass: "bg-info",
            hoverClass: "popover-item",
            btntext: "SS"
          },
          {
            btntype: "vs-button-border plus-btn",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "+"
          }
        ],
        statustext: "In Progress",
        statuscolor: "warning",
        status: "text-warning",
        week: "35",
        money: "$96K"
      },
      {
        img: "/2.jpg",
        leadname: "Asep Sunandar",
        leademail: "Kristeen@gmail.com",
        projectname: "w-12346",
        tagbtns: [
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "SS"
          },
          {
            btntype: "vs-button-filled",
            btnbgClass: "bg-info",
            hoverClass: "popover-item",
            btntext: "DS"
          },
          {
            btntype: "vs-button-border plus-btn",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "+"
          }
        ],
        statustext: "Active",
        statuscolor: "success",
        status: "text-success",
        week: "35",
        money: "$96K"
      },
      {
        img: "/4.jpg",
        leadname: "Iwa Kartiwa",
        leademail: "Josephs@gmail.com",
        projectname: "w-123467",
        tagbtns: [
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "SS"
          },
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-success",
            hoverClass: "popover-item",
            btntext: "DS"
          },
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-warning",
            hoverClass: "popover-item",
            btntext: "SS"
          },
          {
            btntype: "vs-button-border plus-btn",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "+"
          }
        ],
        statustext: "Active",
        statuscolor: "success",
        status: "text-success",
        week: "35",
        money: "$96K"
      },
      {
        img: "/6.jpg",
        leadname: "Pranoto",
        leademail: "hgover@gmail.com",
        projectname: "W-12358",
        tagbtns: [
          {
            btntype: "vs-button-filled",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "SS"
          },
          {
            btntype: "vs-button-border plus-btn",
            btnbgClass: "vs-button-primary",
            hoverClass: "",
            btntext: "+"
          }
        ],
        statustext: "In Progress",
        statuscolor: "warning",
        status: "text-warning",
        week: "35",
        money: "$96K"
      }
    ]
  })
};
</script>