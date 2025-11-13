<template>
  <GeneralForm :jenisJurnal="6" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/jurnal/GeneralForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Jurnal Umum',
       url : '/api/ju',
       routeBack : 'julist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>