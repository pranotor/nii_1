<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">{{title}}</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0" v-if="bpbmode=='bpb'">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            @rowselect="bpbGridOnRowSelect($event)"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li v-if="canPrint==true">Cetak</li>
                <li v-if="canEdit==true">Edit</li>
                <li v-if="canDelete==true">Delete</li>
                <li v-if="canUnposting==true">UnPosting</li>
            </ul>
        </JqxMenu>
        <br/>
        <h3>BPB Detail</h3>
        <JqxGrid ref="detailGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns3" v-if="jenisJurnal=='bpbposting'">
                  
        </JqxGrid>
        <JqxGrid ref="detailGrid2"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns2" v-else>
                  
        </JqxGrid>
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
        bpbmode : {
            default: 'bpb',
            type: String
        },
        canPrint: {
            default: true,
            type: Boolean
        },
        canEdit: {
            default: true,
            type: Boolean
        },
        canDelete: {
            default: true,
            type: Boolean
        },
        canUnposting: {
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
        width: "100%",
        columns: [
          { text: 'Tgl', datafield: 'bpb_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No BPB', datafield: 'bpb_no', width: '30%' },
          { text: 'No PO', datafield: 'po_no', width: '30%'},
          { text: 'Status', filtertype: 'list', filteritems: this.items, datafield: 'posting', width: '30%'}
        ],
        columns2: [
          { text: 'Kode Barang', datafield: 'kode_barang', width: '28%'},
          { text: 'Qty Pesan', datafield: 'qty_pesan', width: '10%' },        
          { text: 'Qty Terima', datafield: 'qty_terima', width: '10%' },      
        ],
        columns3: [
          { text: 'Kode Barang', datafield: 'kode_barang', width: '28%'},
          { text: 'Qty Pesan', datafield: 'qty_pesan', width: '10%' },        
          { text: 'Qty Terima', datafield: 'qty_terima', width: '10%' },        
          { text: 'Harga', datafield: 'harga', width: '13%',cellsformat: 'D0' },        
          { text: 'PPN', datafield: 'ppn', width: '13%',cellsformat: 'D2' },        
          { text: 'Duty Cost', datafield: 'duty_cost', width: '13%',cellsformat: 'D2' },        
          { text: 'Freight Cost', datafield: 'freight_cost', width: '13%',cellsformat: 'D2' },        
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
        bpbGridOnRowSelect: function(event) {
                let bpb_no = event.args.row.bpb_no;
                let bpb_id = event.args.row.id;
                let dataSource2 = {
                    datafields2: [
                        { name: 'kode_barang', type: 'string'},
                        { name: 'qty_pesan', type: 'number'},
                        { name: 'qty_terima', type: 'number'},
                        { name: 'harga', type: 'number'},
                        { name: 'ppn', type: 'number'},
                        { name: 'duty_cost', type: 'number'},
                        { name: 'freight_cost', type: 'number'}
                    ],
                    datatype: 'json',
                    data:{ bpb_id: bpb_id },
                    id: 'id',
                    url: this.$urlbackend+'/api/bpbdet',
                    type: 'post',
                }
                let adapter2 = new jqx.dataAdapter(dataSource2 , { 
                    formatData: function (data) {
                            return  JSON.stringify(data);
                    },
                    beforeSend: function (jqxhr, settings) { 
                        settings.xhrFields = {
                            withCredentials: true
                        },
                        jqxhr.setRequestHeader("X-XSRF-TOKEN",window.$cookies.get('XSRF-TOKEN'))
                    }
                });
                if(this.jenisJurnal=='bpbposting')
                    this.$refs.detailGrid.source = adapter2;
                else
                    this.$refs.detailGrid2.source = adapter2;
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
                //console.log(dataRecord)
                let referensi = dataRecord.bpb_no
                //cek apakah sudah posting
                if(dataRecord.posting=='SUDAH POSTING'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'BPB TIDAK BISA DIEDIT KARENA SUDAH DI POSTING....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else
                    this.$router.push({name : this.routeEdit, params: { ref: referensi }});
            }
            else if (args.innerHTML == 'Cetak') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.bpb_no
                this.pdfsrc = this.$urlbackend+'/api/bpbpdf/'+referensi
                this.popupPdf = true
            }
            else if (args.innerHTML == 'UnPosting') {
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                if(data.posting=='BLM POSTING'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'BPB INI BELUM DIPOSTING, KENAPA JUGA HARUS UNPOSTING, TINGGAL EDIT SAJA BOS....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else{
                    if(confirm("Anda dengan sadar atau diluar sadar akan unposting data ini, sudah yakin kah?")){
                        this.$http.post('/api/unpostingbpb',{
                                referensi : data.bpb_no
                        }).then(resp => {
                            //this.$refs.myGrid.updaterow(rowid,{posting: 'BLM POSTING'});
                            this.$refs.myGrid.updatebounddata();
                            this.$vs.notify({title:'BERHASIL..',text:'Data berhasil unposting..',color:'danger'})
                        }).catch(error => {
                            console.log(error);
                        })
                    }
                }
                
            }
            else {
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                if(data.posting=='SUDAH POSTING'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'MAAF BOS, BPB TIDAK BISA DIHAPUS KARENA SUDAH DI POSTING....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else{
                    if(confirm("Yakin menghapus data?")){
                        this.$http.delete('/api/bpb/' + data.bpb_no).then(resp => {
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
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'bpb_no', type: 'string'},
          { name: 'bpb_tgl', type: 'date'},
          { name: 'po_no', type:'string'},
          { name: 'posting', map: 'status>status_name'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/bpblist',
        type: 'post',
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
            data.bpbmode = self.bpbmode
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
<style lang="stylus">
.cetak
  height: 90vh
</style>

