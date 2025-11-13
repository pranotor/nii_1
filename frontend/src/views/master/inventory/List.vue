<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Inventory</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true" 
            @cellclick="myGridOnCellClick($event)"
            :width="width" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="58" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li id="edit">Edit</li>
                
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
        /* eslint-disable no-console */

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
          { text: 'Kode Perkiraan', datafield: 'kode_perk', width: '15%', },
          { text: 'Kode Barang', datafield: 'kode_barang', width: '15%', },
          { text: 'Uraian', datafield: 'uraian', width: '40%', },
          { text: 'Satuan', datafield: 'satuan', width: '20%'},
          { text: 'Stok', datafield: 'stok', width: '10%'}
        ],
        selectrow: 0,
        routeEdit: 'inventoryedit'
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({name : 'inventoryinput'});
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
        myGridOnCellClick: function (event) {
            if (event.args.column.datafield=='Edit') {
                this.$refs.myGrid.selectrow(event.args.rowindex);
                let scrollTop = window.scrollY;
                const left = this.$refs.myMenu.width + 10;
                   
                this.$refs.myMenu.open(parseInt(event.args.originalEvent.clientX) - left, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);
                return false;
            }
        },
        myMenuOnItemClick: function (event) {
            let args = event.args;
            let rowindex = this.$refs.myGrid.getselectedrowindex();
            if (args.innerHTML == 'Edit') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                console.log(dataRecord)
                let referensi = dataRecord.id
                this.$router.push({name : this.routeEdit, params: { ref: referensi }});
            }
            else {
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                console.log(data)
                if(confirm("Yakin menghapus data?")){
                    this.$http.delete('/api/ajuan/' + data.ref).then(resp => {
                        console.log(resp)
                        this.$refs.myGrid.deleterow(rowid);
                        this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                    }).catch(error => {
                        console.log(error);
                        this.$vs.notify({title:'GAGAL..',text:'Gagal hapus data..',color:'danger'})
                    })
                }
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'kode_perk', type: 'string'},
          { name: 'kode_barang', type: 'string'},
          { name: 'uraian', type: 'string'},
          { name: 'satuan', type: 'string'},
          { name: 'stok', map: 'stock>sisa'},
        ],
        datatype: 'json',
        id: 'kode',
        url: this.$urlbackend+'/api/item',
        type: 'get',
        deleterow: function (rowid, commit) {
            commit(true);
        },
      }
      this.editrow = -1;
    },
    created: function(){
        var self = this
        let processdata=function (data) {
            data.THN = window.$cookies.get('THN')
            data.jenis = self.jenisJurnal
        }
        this.source.processdata = processdata
    }, 
    mounted: function () {
        document.addEventListener('contextmenu', event => event.preventDefault());
    },
  }
</script>

<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style>
.jqx-grid-cell input[type="button"]{
    width:100%;
    z-index:10;
    opacity:0.01!important;
    z-index:5!important;
    display:block!important;
}
</style>
