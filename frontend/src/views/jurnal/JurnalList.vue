<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">{{title}}</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :virtualmode="true" :rendergridrows="rendergridrows"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Cetak</li>
                <li v-if="canEdit==true">Edit</li>
                <li v-if="canDelete==true">Delete</li>
            </ul>
        </JqxMenu>
      </vs-card>
      <vs-popup fullscreen title="CETAK JURNAL" :active.sync="popupPdf">
        <div class="cetak">  
            <iframe width="100%" height="100%" :src="pdfsrc" frameborder="0" />
        </div>
      </vs-popup>
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
    props: {
        title: String,
        jenisJurnal : String,
        routeInput : String,
        routeEdit : String,
        canEdit: {
            default: true,
            type: Boolean
        },
        canDelete: {
            default: true,
            type: Boolean
        },

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
        rendergridrows: (params) => {
          return params.data;
        },
        width: "100%",
        columns: [
          { text: 'Tgl', datafield: 'tanggal', filtertype: 'range', width: '9%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Referensi', datafield: 'referensi', width: '9%', },
          { text: 'Uraian', datafield: 'uraian', width: '46%' },
          { text: 'Dokumen', datafield: 'document', width: '12%' },
          { text: 'Debet', datafield: 'debet', width: '12%', cellsformat: 'd2', cellsalign: 'right' },
          { text: 'Kredit', datafield: 'kredit', width: '12%', cellsformat: 'd2', cellsalign: 'right' }
        ],
        selectrow: 0,
        jenis: this.jenisJurnal,
        popupPdf: false,
        pdfsrc: ''
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({name : this.routeInput});
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
                this.$router.push({name : this.routeEdit, params: { ref: referensi }});
            }
            else if (args.innerHTML == 'Cetak') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.referensi
                this.pdfsrc = this.$urlbackend+'/api/pdf/'+referensi
                this.popupPdf = true
            }
            else {
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                console.log(data)
                if(confirm("Yakin menghapus data?")){
                    this.$http.post('/api/jurnaldelete',{
                        referensi : data.referensi
                    }).then(resp => {
                        console.log(resp)
                        this.$refs.myGrid.deleterow(rowid);
                        this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                    }).catch(error => {
                        console.log(error);
                    })
                }
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'tanggal', type: 'date'},  
          { name: 'referensi', type: 'string'},
          { name: 'uraian', type: 'string'},
          { name: 'document', type: 'string'},
          { name: 'debet', type: 'number'},
          { name: 'kredit', type: 'number'}
        ],
        datatype: 'json',
        id: 'referensi',root: "Rows",
        cache: false,
        beforeprocessing: (data) => {
          console.log('total record')
          console.log(data)
          this.source.totalrecords = data.TotalRows;
        },
        filter: () => {
                    // update the grid and send a request to the server.
                    this.$refs.myGrid.updatebounddata("filter");
        },
        sort: () => {
                    // update the grid and send a request to the server.
                    this.$refs.myGrid.updatebounddata("sort");
        },
        url: this.$urlbackend+'/api/jurnallist',
        type: 'post',
        sortcolumn: 'tanggal',
        sortdirection: 'des',
        deleterow: function (rowid, commit) {
            commit(true);
        }
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
    watch: {
        popupPdf: function(){
            if (this.popupPdf == false)
                this.pdfsrc = 'about:blank'
            else
                this.pdfsrc = this.pdfsrc + "#zoom=100"
        }
    }
  }
</script>

<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>
<style lang="stylus">
.cetak
  height: 90vh
</style>

