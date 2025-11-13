<template>
  <JurnalList jenisJurnal="bppposting" :title="title"  :routeInput="input" :routeEdit="edit" bppmode="posting" canDelete=false />
</template>

<script>
import JurnalList from '@/views/inventory/BppList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Posting BPP',
       input : 'postingbppinput',
       edit : 'postingbppedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>