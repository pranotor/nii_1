<template>
  <GeneralForm jenisJurnal="bppposting" :title="title" :routeBack="routeBack" :jurnalUrl="url" bppmode="posting" />
</template>

<script>
import GeneralForm from '@/views/inventory/BppForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Posting BPP : ',
       url : '/api/bpp',
       routeBack : 'postingbpplist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>