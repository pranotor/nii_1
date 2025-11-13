<template>
  <div>
    <vs-row class="mt-4">
      <vs-col
        vs-xs="12"
        v-for="(totalincome, index) in totalincomes"
        :key="index"
        :class="[ totalincome.gridlg, totalincome.gridsm ]"
      >
        <div class="d-flex align-items-center">
          <div class="mr-2">
            <span class="display-4" :class="[ totalincome.iconcolor ]">
              <i :class="[ totalincome.icon]"></i>
            </span>
          </div>
          <div>
            <span :class="[ totalincome.textcolor ]">{{ totalincome.text }}</span>
            <h3 class="font-medium mb-0">{{ totalincome.money }}</h3>
          </div>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
export default {
  name: "MonthlyProfitList",
  props: {
    totalincomes: {
      type: Array,
      required: true
    },
    gridlg: {
      type: String,
      default: ""
    },
    gridsm: {
      type: String,
      default: ""
    },
    iconcolor: {
      type: String,
      default: ""
    },
    icon: {
      type: String,
      default: ""
    },
    textcolor: {
      type: String,
      default: ""
    },
    text: {
      type: String,
      default: ""
    },
    money: {
      type: String,
      default: ""
    }
  },
  data: () => ({
    title: "MonthlyProfitList"
  }),
  components: {}
};
</script>