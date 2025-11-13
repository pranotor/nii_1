<template>
  <JurnalList jenisJurnal="6" :title="title"  :routeInput="input" :routeEdit="edit" />
</template>

<script>
import JurnalList from '@/views/jurnal/JurnalList.vue';

export default {
    components: {
        JurnalList
    },
    data:() => ({
       title : 'Jurnal Umum',
       input : 'juinput',
       edit : 'juedit'
    }),
    methods: {
       
    }
}
</script>


<style>

</style>