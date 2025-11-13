<template>
  <div>
    <ul class="list-inline mt-3">
      <li class="d-flex align-items-center">
        <vs-radio v-model="radios1" vs-name="radios1" vs-value="a">
          <b class="mr-1">A</b> 30 Minutes
        </vs-radio>
      </li>
      <li class="mt-3 d-flex align-items-center">
        <vs-radio v-model="radios1" vs-name="radios1" vs-value="b">
          <span class="text-primary">
            <b class="mr-1">B</b> More than 30 minutes
          </span>
        </vs-radio>
      </li>
      <li class="mt-3 d-flex align-items-center">
        <vs-radio v-model="radios1" vs-name="radios1" vs-value="c">
          <b class="mr-1">C</b> 1 Hour
        </vs-radio>
      </li>
      <li class="mt-3 d-flex align-items-center">
        <vs-radio v-model="radios1" vs-name="radios1" vs-value="d">
          <b class="mr-1">D</b> More than 1 hour
        </vs-radio>
      </li>
    </ul>
    <vs-button color="success" class="mt-2" type="filled">Submit Answer</vs-button>
  </div>
</template>
<script>
export default {
  name: "PollWeek",
  data: () => ({
    title: "PollWeek",
    radios1: ""
  })
};
</script>