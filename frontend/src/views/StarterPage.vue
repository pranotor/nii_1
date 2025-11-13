<template>
 <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="6" vs-xs="12" code-toggler>
      <vs-card actionable class="cardx">
        <div slot="header">
          <h4>
            Hello world !
          </h4>
        </div>
        
        <div >
          
          <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
          
        </div>
        
      </vs-card>
    </vs-col>
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="6" vs-xs="12">
      <vs-card actionable class="cardx">
        <div slot="header">
          <h4>
            Hello world !
          </h4>
        </div>
        <div>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit
        </div>
      </vs-card>

    </vs-col>

  </vs-row>
</template>

<script>

export default {
	name : 'StarterPage',

}
</script>
<style>
.ps {
  height: 100px;
}
</style>
