<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Quotation</h3>
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
        />
        <br/>
        <h3>Order Detail</h3>
        <JqxGrid ref="detailGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns3" >
                  
        </JqxGrid>
        <br/>
        <h3>Document History</h3>
        <JqxGrid ref="historyGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns2" :rowdetails="true" 
                 :initrowdetails="initRowDetails" :rowdetailstemplate="rowdetailstemplate">
        </JqxGrid>
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Cetak</li>
                <li>Edit</li>
                <li>Konversi ke Sales Order</li>
                <li v-if="canDelete==true">Delete</li>
            </ul>
        </JqxMenu>
      </vs-card>
      <vs-prompt @cancel="val=''" @accept="konversiPo" @close="close" :active.sync="activePrompt" accept-text="Simpan" cancel-text="Batal">
       <div class="con-exemple-prompt">
          Masukan Nomor PO
         <vs-input placeholder="Nomor PO" v-model="nomor_po"/>  
       </div>
      </vs-prompt>
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
          { text: 'Tgl', datafield: 'qt_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No PH', datafield: 'qt_no', width: '14%' },
          { text: 'Customer', datafield: 'customer', width: '30%'},
          { text: 'Nick', datafield: 'nick', width: '20%'},
          { text: 'Status', filtertype: 'list', filteritems: this.items, datafield: 'posting', width: '35%'}
        ],
        columns2: [
          { text: 'Tgl', datafield: 'created_at', filtertype: 'range', width: '30%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Revisi', datafield: 'rev', width: '70%' },
        ],
        columns3: [
          { text: 'Kode Barang', datafield: 'kode_barang', width: '10%'},
          { text: 'Kode 2', datafield: 'kode2', width: '10%'},
          { text: 'Size', datafield: 'size', width: '30%' },
          { text: 'Qty Pesan', datafield: 'qty_pesan', width: '10%' },
          { text: 'Harga', datafield: 'harga', width: '10%', cellsformat: 'D'},
          { text: 'Discount', datafield: 'discount', width: '10%' },
          { text: 'Total', datafield: 'total', width: '10%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            let total = parseFloat(rowdata.harga) * parseFloat(rowdata.qty_pesan) * (100 - parseFloat(rowdata.discount))/100;
                            return '<div style="margin: 4px;" class="jqx-right-align">' + this.dataAdapter.formatNumber(total, 'd2') + '</div>';
                        } },
          { text: 'Total + PPN', datafield: 'totalppn', width: '10%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            let total = parseFloat(rowdata.harga) * parseFloat(rowdata.qty_pesan) * (100 - parseFloat(rowdata.discount))/100 * (1 + parseFloat(rowdata.ppn));
                            return '<div style="margin: 4px;" class="jqx-right-align">' + this.dataAdapter.formatNumber(total, 'd2') + '</div>';
                        } },              
        ],
        rowdetailstemplate: {
            rowdetails: '<div id="nestedGrid" style="margin: 10px;"></div>', rowdetailsheight: 220, rowdetailshidden: true
        },
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: '',
        nomor_po:'',
        activePrompt:false,
        qt_no: '',
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({name : 'quotationinput'});
        },
        myGridOnContextMenu: function () {
                return false;
        },
        quotationGridOnRowSelect: function(event) {
                let qt_no = event.args.row.qt_no;
                let qt_id = event.args.row.id;
                console.log(event)
                let dataSource = {
                    datafields: [
                        { name: 'created_at', type: 'date'},
                        { name: 'qt_no', type: 'string'},
                        { name: 'rev', type: 'string'}
                    ],
                    datatype: 'json',
                    data:{ qt_no: qt_no },
                    id: 'id',
                    url: this.$urlbackend+'/api/quotationhistory',
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
                this.$refs.historyGrid.source = adapter;
                let dataSource2 = {
                    datafields2: [
                        { name: 'kode_barang', type: 'string'},
                        { name: 'kode2', type: 'string'},
                        { name: 'size', type: 'string'},
                        { name: 'qty_pesan', type: 'number'},
                        { name: 'harga', type: 'number'},
                        { name: 'discount', type: 'number'},
                        { name: 'ppn', type: 'number'},
                    ],
                    datatype: 'json',
                    data:{ qt_id: qt_id },
                    id: 'id',
                    url: this.$urlbackend+'/api/quotationdet',
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
                this.$refs.detailGrid.source = adapter2;
                //this.$refs.historyGrid.updatebounddata()
        },
        initRowDetails: function (index, parentElement, gridElement, record) {
            let row = index;
            let rowdata = this.$refs.myGrid.getrowdata(row);
            console.log(record)
            let nestedGridContainer = parentElement.children[0];
            this.nestedGrids[index] = nestedGridContainer;
            let ordersSource = {
                datafields: [
                    { name: 'item_id', type: 'string' },
                    { name: 'kode_barang', map: 'itembarang>kode_barang' },
                    { name: 'size',type: 'string' },
                    { name: 'qty_pesan', type: 'number' },
                    { name: 'harga', type: 'number' },
                    { name: 'discount', type: 'number' }
                ],
                datatype: 'json',
                id: 'id',
                data:{ qt_no: record.qt_no, rev : record.rev },
                url: this.$urlbackend+'/api/quotationdh',
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
                    height: 100,
                    source: nestedGridAdapter,
                    columns: [
                        { text: 'Kode Barang', datafield: 'kode_barang', width: 200 },
                        { text: 'Size', datafield: 'size', width: 300 },
                        { text: 'Qty Pesan', datafield: 'qty_pesan', width: 100 },
                        { text: 'Harga', datafield: 'harga', width: 100, cellsformat: 'd2', cellsalign: 'right'},
                        { text: 'Discount', datafield: 'discount', width: 100, cellsformat: 'd2', cellsalign: 'right'}
                    ]
                };
                jqwidgets.createInstance(`#${nestedGridContainer.id}`, 'jqxGrid', settings);
            }
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
            else if(args.innerHTML == 'Konversi ke Sales Order'){
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                this.qt_no = dataRecord.qt_no
                
                if(dataRecord.posting=='SUDAH PO'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'PH SUDAH MENJADI SALES ORDER, TIDAK BISA DIKONVERSI LAGI....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else{
                    this.activePrompt = true;
                    /* if(confirm("Yakin konversi Quotation ke Sales Order?")){
                        
                        this.$http.post('/api/convtoso',{
                                referensi : qt_no
                        }).then(resp => {
                            //this.$refs.myGrid.updaterow(rowid,{posting: 'BLM POSTING'});
                            this.$refs.myGrid.updatebounddata();
                            this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dikonversi..',color:'danger'})
                        }).catch(error => {
                            console.log(error);
                        })
                    } */
                }
                
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
        },
        konversiPo : function() {      
            this.$http.post('/api/convtoso',{
                    referensi : this.qt_no,
                    nomor_po : this.nomor_po
            }).then(resp => {
                //this.$refs.myGrid.updaterow(rowid,{posting: 'BLM POSTING'});
                this.$refs.myGrid.updatebounddata();
                this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dikonversi..',color:'danger'})
            }).catch(error => {
                console.log(error);
            })
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'qt_no', type: 'string'},
          { name: 'qt_tgl', type: 'date'},
          { name: 'customer', map: 'qcustomer>nama'},
          { name: 'nick', map: 'qcustomer>nick'},
          { name: 'posting', map: 'status>status_name'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/quotationlist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      }
      this.dataFields2 = [
            { name: 'created_at', type: 'date' },
            { name: 'rev'}
      ];
      
      this.nestedGrids = [];
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

