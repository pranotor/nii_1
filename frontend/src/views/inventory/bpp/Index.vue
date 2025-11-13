<template>
  <JurnalList jenisJurnal="4" :title="title"  :routeInput="input" :routeEdit="edit" canUnposting="false"/>
</template>

<script>
import JurnalList from '@/views/inventory/BppList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Permintaan Pemakaian Barang (BPP)',
       input : 'bppinput',
       edit : 'bppedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>