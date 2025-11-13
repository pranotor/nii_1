<template>
  <GeneralForm jenisForm="so" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/crm/FormOrder.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Sales Order',
       url : '/api/quotation',
       routeBack : 'so',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>