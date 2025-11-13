<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-row>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Line Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Line Chart</h3>
            <div>
              <LineChart />
            </div>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Line Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Line Chart</h3>
            <div>
              <BarChart />
            </div>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Pie Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Pie Chart</h3>
            <div>
              <PieChart :height="300" :width="300" />
            </div>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Doughnut Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Doughnut Chart</h3>
            <div>
              <DoughnutChart :height="300" :width="300" />
            </div>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Radar Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Radar Chart</h3>
            <div>
              <RadarChart :height="300" :width="300" />
            </div>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="6" vs-xs="12">
          <!--
        /////////////////
        Bubble Chart
        /////////////////
          -->
          <vs-card>
            <h3 class="card-title d-flex">Bubble Chart</h3>
            <div>
              <BubbleChart :height="300" :width="300" />
            </div>
          </vs-card>
        </vs-col>
      </vs-row>
    </vs-col>
  </vs-row>
</template>

<script>
import BarChart from "./BarChart";
import LineChart from "./LineChart";
import PieChart from "./PieChart";
import DoughnutChart from "./Doughnut";
import RadarChart from "./RadarChart";
import BubbleChart from "./BubbleChart";

export default {
  name: "ChartJs",
  data: () => ({
    title: "ChartJs"
  }),
  components: {
    BarChart,
    LineChart,
    PieChart,
    DoughnutChart,
    RadarChart,
    BubbleChart
  }
};
</script>


