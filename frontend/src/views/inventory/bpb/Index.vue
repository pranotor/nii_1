<template>
  <JurnalList jenisJurnal="4" :title="title"  :routeInput="input" :routeEdit="edit" canUnposting="false"/>
</template>

<script>
import JurnalList from '@/views/inventory/BpbList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Good Recieve (BPB)',
       input : 'bpbinput',
       edit : 'bpbedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>