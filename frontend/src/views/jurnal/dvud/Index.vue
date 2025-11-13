<template>
  <JurnalList jenisJurnal="1" :title="title"  :routeInput="input" :routeEdit="edit" />
</template>

<script>
import JurnalList from '@/views/jurnal/JurnalList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Voucher Utang (DVUD/DHHD)',
       input : 'dvudinput',
       edit : 'dvudedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>