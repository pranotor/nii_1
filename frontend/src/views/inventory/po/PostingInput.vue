<template>
  <GeneralForm jenisJurnal="bpbposting" :title="title" :routeBack="routeBack" :jurnalUrl="url" bpbmode="posting" />
</template>

<script>
import GeneralForm from '@/views/inventory/BpbForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Posting BPB : ',
       url : '/api/bpb',
       routeBack : 'postingbpblist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>