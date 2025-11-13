<template>
  <GeneralForm jenisForm="sr" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/crm/FormOrder.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Sample Request',
       url : '/api/quotation',
       routeBack : 'sr',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>