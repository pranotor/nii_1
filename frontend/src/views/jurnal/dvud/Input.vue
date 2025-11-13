<template>
  <GeneralForm :jenisJurnal="1" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/jurnal/GeneralForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Voucher Utang (DVUD/DHHD)',
       url : '/api/ju',
       routeBack : 'dvudlist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>