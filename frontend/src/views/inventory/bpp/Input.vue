<template>
  <GeneralForm jenisJurnal="bpp" :title="title" :routeBack="routeBack" :jurnalUrl="url" />
</template>

<script>
import GeneralForm from '@/views/inventory/BppForm.vue';

export default {
    components: {
        GeneralForm
    },
    data:() => ({
       title : 'Permintaan Pemakaian Barang (BPP)',
       url : '/api/bpp',
       routeBack : 'bpplist',
    }),
    methods: {
       
    }
}
</script>


<style>

</style>