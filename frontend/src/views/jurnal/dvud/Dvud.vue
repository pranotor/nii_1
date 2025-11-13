<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Jurnal</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm" color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div>
        </div>
        <hr class="custom-hr" />  
        <JqxGrid 
            :width="width" :theme="'material-green'" 
            :source="dataAdapter" :columns="columns" 
        />
      </vs-card>
    </vs-col>
   </vs-row>
</template>

<script>
  import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";

  export default {
    components: {
      JqxGrid
    },
    data: function () {
      return {
        // eslint-disable-next-line
        dataAdapter: new jqx.dataAdapter(this.source),
        width: "100%",
        columns: [
          { text: 'Referensi', datafield: 'ContactName', width: 240 },
          { text: 'Tgl', datafield: 'Title', width: 240 },
          { text: 'Uraian', datafield: 'City', width: 150 },
          { text: 'Debet', datafield: 'Country' },
          { text: 'Kredit', datafield: 'Country' }
        ]
      }
    },
    methods: {
        inputForm: function(){
            this.$router.push({path : 'jurnal'});
        }
    },
    beforeCreate: function () {
      this.source = {
        localdata: [
          ['Maria Anders', 'Sales Representative', 'Berlin', 'Germany'],
          ['Ana Trujillo', 'Owner', 'Mxico D.F.', 'Mexico'],
          ['Antonio Moreno', 'Owner', 'Mxico D.F.', 'Mexico'],
          ['Thomas Hardy', 'Sales Representative', 'London', 'UK'],
          ['Christina Berglund', 'Order Administrator', 'Lule', 'Sweden'],
          ['Hanna Moos', 'Sales Representative', 'Mannheim', 'Germany'],
          ['Frdrique Citeaux', 'Marketing Manager', 'Strasbourg', 'France'],
          ['Martn Sommer', 'Owner', 'Madrid', 'Spain'],
          ['Owner', 'Marseille', 'France'],
          ['Elizabeth Lincoln', 'Accounting Manager', 'Tsawassen', 'Canada'],
          ['Victoria Ashworth', 'Sales Representative', 'London', 'UK'],
          ['Patricio Simpson', 'Sales Agent', 'Buenos Aires', 'Argentina'],
          ['Francisco Chang', 'Marketing Manager', 'Mxico D.F.', 'Mexico'],
          ['Yang Wang', 'Owner', 'Bern', 'Switzerland'],
          ['Pedro Afonso', 'Sales Associate', 'Sao Paulo', 'Brazil'],
          ['Elizabeth Brown', 'Sales Representative', 'London', 'UK'],
          ['Sven Ottlieb', 'Order Administrator', 'Aachen', 'Germany'],
          ['Janine Labrune', 'Owner', 'Nantes', 'France'],
          ['Ann Devon', 'Sales Agent', 'London', 'UK'],
          ['Roland Mendel', 'Sales Manager', 'Graz', 'Austria']
        ],
        datafields: [
          { name: 'ContactName', type: 'string', map: '0' },
          { name: 'Title', type: 'string', map: '1' },
          { name: 'City', type: 'string', map: '2' },
          { name: 'Country', type: 'string', map: '3' }
        ],
        datatype: 'array'
      };
    }
  }
</script>

<style src='../../../assets/styles/jqwidgets/jqx.base.css'></style>
<style src='../../../assets/styles/jqwidgets/jqx.material-green.css'></style>