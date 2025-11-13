<template>
  <GeneralForm :jenisJurnal="4" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/jurnal/GeneralForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Jurnal Bayar Kas',
       url : '/api/ju',
       routeBack : 'jbklist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>