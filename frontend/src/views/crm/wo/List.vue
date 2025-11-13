<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Work Order</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            @rowselect="quotationGridOnRowSelect($event)"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
            :rowdetails="true" :initrowdetails="initRowDetails" :rowdetailstemplate="rowdetailstemplate"
        />
        <br/>
        <h3>WO Item</h3>
        <JqxGrid ref="itemGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns2" 
                  >
        </JqxGrid>
        
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Cetak</li>
            </ul>
        </JqxMenu>
      </vs-card>
      <vs-popup fullscreen title="CETAK" :active.sync="popupPdf">
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
          { text: 'Tgl', datafield: 'wo_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No WO', datafield: 'wo_no', width: '20%' },
          { text: 'No SO', datafield: 'so_no', width: '20%' },
          { text: 'No PO', datafield: 'po_cust', width: '20%' },
          { text: 'Customer', datafield: 'customer', width: '30%'}
        ],
        columns2: [
          { text: 'Kode Barang', datafield: 'kode_barang',  width: '30%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Qty', datafield: 'qty', width: '70%' }
        ],
        rowdetailstemplate: {
            rowdetails: '<div id="nestedGrid" style="margin: 10px;"></div>', rowdetailsheight: 220, rowdetailshidden: true
        },
        
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: ''
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        quotationGridOnRowSelect: function(event) {
                let wo_no = event.args.row.id;
                let dataSource = {
                    datafields: [
                        { name: 'item_id', type: 'string' },
                        { name: 'kode_barang', type: 'string' },
                        { name: 'qty', type: 'number' },
                        { name: 'wo_id', type: 'number' }
                    ],
                    datatype: 'json',
                    data:{ wo_id: wo_no },
                    id: 'id',
                    url: this.$urlbackend+'/api/woitem',
                    type: 'post',
                }
                let adapter = new jqx.dataAdapter(dataSource , { 
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
                this.$refs.itemGrid.source = adapter;
                //this.$refs.historyGrid.updatebounddata()
        },
        initRowDetails: function (index, parentElement, gridElement, record) {
            let nestedGridContainer = parentElement.children[0];
            this.nestedGrids[index] = nestedGridContainer;
            let ordersSource = {
                datafields: [
                    { name: 'item_id', type: 'string' },
                    { name: 'kode_barang', map: 'itembarang>kode_barang' },
                    { name: 'kode2', type: 'string' },
                    { name: 'size', type: 'string' },
                    { name: 'qty_pesan', type: 'number' },
                    { name: 'tot_kirim', type: 'number' },
                    { name: 'harga', type: 'number' },
                    { name: 'discount', type: 'number' },
                    { name: 'wo_id', type: 'number' }
                ],
                datatype: 'json',
                id: 'id',
                data:{ wo_id: record.id},
                url: this.$urlbackend+'/api/quotationdet',
                type: 'post',
            }
            let nestedGridAdapter = new jqx.dataAdapter(ordersSource,{ 
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
            if (nestedGridContainer != null) {
                let settings = {
                    width: 800,
                    height: '95%',
                    source: nestedGridAdapter,
                    columns: [
                        { text: 'Kode Barang', datafield: 'kode_barang', width: 150 },
                        { text: 'Kode2', datafield: 'kode2', width: 150 },
                        { text: 'Size', datafield: 'size', width: 400 },
                        { text: 'Qty Pesan', datafield: 'qty_pesan', width: 150 }
                    ]
                };
                jqwidgets.createInstance(`#${nestedGridContainer.id}`, 'jqxGrid', settings);
            }
        },
        initRowDetails2: function (index, parentElement, gridElement, record) {
            let nestedGridContainer = parentElement.children[0];
            this.nestedGrids[index] = nestedGridContainer;
            let ordersSource = {
                datafields: [
                    { name: 'item_id', type: 'string' },
                    { name: 'kode_barang', map: 'itembarang>kode_barang' },
                    { name: 'qty_kirim', type: 'number' }
                ],
                datatype: 'json',
                id: 'id',
                data:{ sj_id: record.id},
                url: this.$urlbackend+'/api/sjdet',
                type: 'post',
            }
            let nestedGridAdapter = new jqx.dataAdapter(ordersSource,{ 
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
            if (nestedGridContainer != null) {
                let settings = {
                    width: 400,
                    height: 100,
                    source: nestedGridAdapter,
                    columns: [
                        { text: 'Kode Barang', datafield: 'kode_barang', width: 200 },
                        { text: 'Qty Kirim', datafield: 'qty_kirim', width: 200 }
                    ]
                };
                jqwidgets.createInstance(`#${nestedGridContainer.id}`, 'jqxGrid', settings);
            }
        },
        inputForm: function(){
            this.$router.push({name : 'woinput'});
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
                //console.log(dataRecord)
                let referensi = dataRecord.qt_no
                //cek apakah sudah posting
                if(dataRecord.posting=='SUDAH PO'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'PH TIDAK BISA DIEDIT KARENA SUDAH MENJADI SALES ORDER....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else
                    this.$router.push({name : 'quotationedit', params: { ref: referensi }});
            }
            else if (args.innerHTML == 'Cetak') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.qt_no
                this.pdfsrc = this.$urlbackend+'/api/quotationpdf/'+referensi
                this.popupPdf = true
            }
            else if(args.innerHTML == 'Perintah Kirim'){
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let qt_no = dataRecord.qt_no
                
                
                
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
          { name: 'wo_no', type: 'string'},
          { name: 'id', type: 'number'},
          { name: 'wo_tgl', type: 'date'},
          { name: 'customer', map: 'quotation>qcustomer>nama'},
          { name: 'po_cust', map:'quotation>po_cust'},
          { name: 'so_no', map:'quotation>so_no'},
          { name: 'posting', map: 'status>status_name'},
          { name: 'id', type: 'number'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/wolist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      }
      this.nestedGrids = [];
      this.nestedGrids2 = [];
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

