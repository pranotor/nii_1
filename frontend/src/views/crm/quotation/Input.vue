<template>
  <GeneralForm jenisForm="qt" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/crm/FormOrder.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Quotation',
       url : '/api/quotation',
       routeBack : 'quotation',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>