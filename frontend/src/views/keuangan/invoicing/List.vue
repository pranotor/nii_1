<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Invoice </h3>
        <div class="ml-auto">
          <vs-button @click="convertFp"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Konversi Faktur Pajak
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="220" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Cetak Invoice</li>
                <li>Cetak Invoice (epson)</li>
                <li>Cetak SJ</li>
                <li>Edit No FP</li>
                <li>Edit Bank</li>
                <li>Export Invoice xls</li>
                <li>Export FP xls</li>
                <li>Export FP csv</li>
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
    import qz from "qz-tray";
    const FileDownload = require('js-file-download');

    
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
          { text: 'Tgl', datafield: 'sj_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No Invoice', datafield: 'inv_no', width: '15%' },
          { text: 'No SJ', datafield: 'sj_no', width: '15%' },
          { text: 'No PO', datafield: 'po_no', width: '15%' },
          { text: 'Customer', datafield: 'customer', width: '25%'},
          { text: 'Nick', datafield: 'nick', width: '20%'}
        ],
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: '',
        dataCetak:[],
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        convertFp: function(){
            this.$router.push({name : 'convertFp'});
        },
        inputForm: function(){
            this.$router.push({name : 'quotationinput'});
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
            else if (args.innerHTML == 'Cetak Invoice') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                this.pdfsrc = this.$urlbackend+'/api/invoicepdf/'+referensi
                this.popupPdf = true
            }
            else if (args.innerHTML == 'Cetak Td Terima') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                this.pdfsrc = this.$urlbackend+'/api/pdftt/'+referensi
                this.popupPdf = true
            }
            else if (args.innerHTML == 'Cetak Invoice (epson)') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                let p = dataRecord.p
                if(p=='P'){
                    this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'Tipe Faktur tidak sesuai....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                else{
                    let self = this
                    this.$http.get('/api/invprint/'+referensi).then( resp => {
                        self.msgCetak = resp.data;
                        //console.log(resp.data);
                        qz.websocket.connect().then(() => {
                            //return qz.printers.find("LABEL");
                            return qz.printers.getDefault()
                            //return qz.printers.find("xprinter");
                        }).then((printer) => {
                            let config = qz.configs.create(printer);
                            var data = self.msgCetak
                            data.map(item => {
                                console.log(item)
                                let newitem = item.toString().replace(/_/g, '\x20')
                                self.dataCetak.push(newitem)
                            })
                            return qz.print(config, self.dataCetak); 
                            //return qz.print(config, data)
                        }).then(() => {
                            self.dataCetak = [];
                            return qz.websocket.disconnect();
                        }).then(() => {
                            //process.exit(0);
                        }).catch((err) => {
                            console.error(err);
                            // process.exit(1);
                        });
                    })  
                }
                
            }
            else if (args.innerHTML == 'Cetak SJ') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                /* this.pdfsrc = this.$urlbackend+'/api/sjprint/'+referensi
                this.popupPdf = true */
                let self = this
                this.$http.get('/api/sjprint/'+referensi).then( resp => {
                    self.msgCetak = resp.data;
                    //console.log(resp.data);
                    qz.websocket.connect().then(() => {
                        //return qz.printers.find("LABEL");
                        return qz.printers.getDefault()
                        //return qz.printers.find("xprinter");
                    }).then((printer) => {
                        let config = qz.configs.create(printer);
                        var data = self.msgCetak
                        data.map(item => {
                            console.log(item)
                            let newitem = item.toString().replace(/_/g, '\x20')
                            self.dataCetak.push(newitem)
                        })
                        return qz.print(config, self.dataCetak); 
                        //return qz.print(config, data)
                    }).then(() => {
                        self.dataCetak = [];
                        return qz.websocket.disconnect();
                    }).then(() => {
                        //process.exit(0);
                    }).catch((err) => {
                        console.error(err);
                        // process.exit(1);
                    });
                })  
            }
            else if (args.innerHTML == 'Edit No FP') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                this.$router.push({name : 'invoiceedit', params: { ref: referensi }});
            }
            else if (args.innerHTML == 'Edit Bank') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                this.$router.push({name : 'invoicebankedit', params: { ref: referensi }});
            }
            else if (args.innerHTML == 'Export Invoice xls') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                
                this.$httpxls.get('/api/invoicexls/'+referensi,{responseType: 'blob'}).then( response => {
                    FileDownload(response.data, 'inv_'+referensi+'.xlsx');
                })  
            }
            else if (args.innerHTML == 'Export FP xls') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                
                this.$httpxls.get('/api/fp/'+referensi,{responseType: 'blob'}).then( response => {
                    FileDownload(response.data, 'fp_'+referensi+'.xlsx');
                })  
            }
            else if (args.innerHTML == 'Export FP csv') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                
                this.$httpxls.get('/api/fpcsv/'+referensi,{responseType: 'blob'}).then( response => {
                    FileDownload(response.data, 'fp_'+referensi+'.csv');
                })  
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'inv_no', type: 'string'},
          { name: 'sj_tgl', type: 'date'},
          { name: 'customer', map: 'sales>qcustomer>nama'},
          { name: 'nick', map: 'sales>qcustomer>nick'},
          { name: 'po_no', map: 'sales>po_cust'},
          { name: 'sj_no', type:'string'},
          { name: 'p', type:'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/invoicinglist',
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

