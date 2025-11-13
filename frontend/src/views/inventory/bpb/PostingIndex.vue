<template>
  <JurnalList jenisJurnal="bpbposting" :title="title"  :routeInput="input" :routeEdit="edit" bpbmode="posting" canPrint="false" canDelete="false" />
</template>

<script>
import JurnalList from '@/views/inventory/BpbList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Posting BPB',
       input : 'postingbpbinput',
       edit : 'postingbpbedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>