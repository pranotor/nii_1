<template>
  <GeneralForm jenisJurnal="bpp" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/crm/so/Form.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Sales Order',
       url : '/api/bpp',
       routeBack : 'so',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>