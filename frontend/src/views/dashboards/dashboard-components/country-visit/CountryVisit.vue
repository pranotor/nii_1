<template>
  <div>
    <CountryMap></CountryMap>
    <vs-row class="mb-3">
      <vs-col vs-lg="3" vs-xs="3" vs-sm="3">
        <span>India</span>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="7" vs-sm="7">
        <vs-progress :percent="48" color="primary"></vs-progress>
      </vs-col>
      <vs-col vs-lg="2" vs-xs="2" vs-sm="2">
        <span>28%</span>
      </vs-col>
    </vs-row>
    <vs-row class="mb-3">
      <vs-col vs-lg="3" vs-xs="3" vs-sm="3">
        <span>UK</span>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="7" vs-sm="7">
        <vs-progress :percent="33" color="danger"></vs-progress>
      </vs-col>
      <vs-col vs-lg="2" vs-xs="2" vs-sm="2">
        <span>28%</span>
      </vs-col>
    </vs-row>
    <vs-row class="mb-3">
      <vs-col vs-lg="3" vs-xs="3" vs-sm="3">
        <span>USA</span>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="7" vs-sm="7">
        <vs-progress :percent="40" color="success"></vs-progress>
      </vs-col>
      <vs-col vs-lg="2" vs-xs="2" vs-sm="2">
        <span>28%</span>
      </vs-col>
    </vs-row>
    <vs-row class="mb-3">
      <vs-col vs-lg="3" vs-xs="3" vs-sm="3">
        <span>Chaina</span>
      </vs-col>
      <vs-col vs-lg="7" vs-xs="7" vs-sm="7">
        <vs-progress :percent="15" color="warning"></vs-progress>
      </vs-col>
      <vs-col vs-lg="2" vs-xs="2" vs-sm="2">
        <span>28%</span>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
import CountryMap from "./CountryMap";

export default {
  name: "CountryVisits",
  data: () => ({
    title: "CountryVisits"
  }),
  components: {
    CountryMap
  }
};
</script>