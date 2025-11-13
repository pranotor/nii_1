<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
    <vs-card actionable class="cardx">
    <h3 class="mb-3">{{ msg }}</h3>
    <p>
      For a guide and recipes on how to configure / customize this project,<br>
      check out the
      <a href="https://cli.vuejs.org" target="_blank" rel="noopener">vue-cli documentation</a>.
    </p>
  </vs-card>
  </vs-col>
  </vs-row>  
  
</template>

<script>
export default {
  name: 'HelloWorld',
  data:()=>({
    msg: "Hello Vue"
  })
}
</script>


