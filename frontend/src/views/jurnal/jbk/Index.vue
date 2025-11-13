<template>
  <JurnalList jenisJurnal="4" :title="title"  :routeInput="input" :routeEdit="edit"/>
</template>

<script>
import JurnalList from '@/views/jurnal/JurnalList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Jurnal Pembayaran Kas/Bank (JBK)',
       input : 'jbkinput',
       edit : 'jbkedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>