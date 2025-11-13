<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Jurnal Umum</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid v-if="$store.state.thnValuta" ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="58" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Edit</li>
                <li>Delete</li>
            </ul>
        </JqxMenu>
      </vs-card>
    </vs-col>
   </vs-row>
</template>

<script>
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    import JqxWindow from "jqwidgets-scripts/jqwidgets-vue/vue_jqxwindow.vue";
    import JqxInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxinput.vue";
    import JqxNumberInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxnumberinput.vue";
    import JqxButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxbuttons.vue";
    import JqxMenu from "jqwidgets-scripts/jqwidgets-vue/vue_jqxmenu.vue";
    
  export default {
    components: {
        JqxGrid,
        JqxWindow,
        JqxInput,
        JqxNumberInput,
        JqxButton,
        JqxMenu
    },
    data: function () {
      return {
        // eslint-disable-next-line
        dataAdapter: new jqx.dataAdapter(this.source, {
            formatData: function (data) {
                return  JSON.stringify(data);
            },
            beforeSend: function (jqxhr, settings) { 
                settings.xhrFields = {
                    withCredentials: true
                },
                jqxhr.setRequestHeader("X-XSRF-TOKEN",window.$cookies.get('XSRF-TOKEN'))
            }
        }),
        width: "100%",
        columns: [
          { text: 'Referensi', datafield: 'referensi', width: '10%' },
          { text: 'Tgl', datafield: 'tanggal', filtertype: 'date', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Uraian', datafield: 'uraian', width: '50%' },
          { text: 'Debet', datafield: 'debet', width: '15%', cellsformat: 'd2', cellsalign: 'right' },
          { text: 'Kredit', datafield: 'kredit', width: '15%', cellsformat: 'd2', cellsalign: 'right' }
        ],
        selectrow: 0
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({path : '/jurnal/ju/input'});
        },
        myGridOnContextMenu: function () {
                return false;
        },
        myGridOnRowClick: function (event) {
            if (event.args.rightclick) {
                this.$refs.myGrid.selectrow(event.args.rowindex);
                let scrollTop = window.scrollY;
                let scrollLeft = window.scrollX;
                this.$refs.myMenu.open(parseInt(event.args.originalEvent.clientX) + 5 + scrollLeft, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);
                return false;
            }
        },
        myMenuOnItemClick: function (event) {
            let args = event.args;
            let rowindex = this.$refs.myGrid.getselectedrowindex();
            if (args.innerHTML == 'Edit') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.referensi
                this.$router.push({name : 'juedit',params: { ref: referensi }});
                //this.$refs.firstName.value = dataRecord.firstname;
                //this.$refs.myWindow.open();
                
            }
            else {
                //let rowid = this.$refs.myGrid.getrowid(rowindex);
                //this.$refs.myGrid.deleterow(rowid);
            }
        },
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'referensi', type: 'string'},
          { name: 'tanggal', type: 'date'},
          { name: 'uraian', type: 'string'},
          { name: 'debet', type: 'number'},
          { name: 'kredit', type: 'number'}
        ],
        datatype: 'json',
        id: 'jid',
        url: this.$urlbackend+'/api/jurnallist',
        type: 'post'
      }
      this.editrow = -1;
    },
    created: function(){
        let processdata=function (data) {
            data.THN = window.$cookies.get('THN')
            data.jenis = 6
        }
        this.source.processdata=processdata
    }, 
    mounted: function () {
        document.addEventListener('contextmenu', event => event.preventDefault());
    },
  }
</script>

<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>

