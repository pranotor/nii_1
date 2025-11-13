<template>
  <div>
    <vs-row class="mt-4">
      <vs-col vs-lg="5" vs-xs="12" vs-sm="6" class="d-flex align-items-center">
        <div>
          <vs-select class="w-100px" v-model="month1">
            <vs-select-item
              :key="index"
              :value="item.value"
              :text="item.text"
              v-for="(item,index) in monthoptions1"
            />
          </vs-select>
        </div>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="12" vs-sm="6">
        <div>
          <vue-apex-charts
            type="area"
            height="90px"
            :options="MonthBounceRateChart.monthrateChart.chartOptions"
            :series="MonthBounceRateChart.monthrateChart.series"
          ></vue-apex-charts>
        </div>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import MonthBounceRateChart from "./MonthBounceRateData";

export default {
  name: "MonthBounceRate",
  data: () => ({
    title: "MonthBounceRate",
    MonthBounceRateChart: MonthBounceRateChart,
    // Month Table
    month1: 0,
    monthoptions1: [
      { text: "April", value: 0 },
      { text: "May", value: 1 },
      { text: "June", value: 2 },
      { text: "July", value: 3 }
    ]
  }),
  components: {
    VueApexCharts
  }
};
</script>