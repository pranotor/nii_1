<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Nomor Faktur Pajak </h3>
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
            :source="dataAdapter" :columns="columns"  @rowclick="myGridOnRowClick($event)"
        />
        
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
          { text: 'Kode Depan', datafield: 'kode_depan', width: '20%' },
          { text: 'No Seri Fp', datafield: 'no_fp', width: '30%' },
          { text: 'Status', datafield: 'status', width: '50%' }
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
        inputForm: function(){
            this.$router.push({name : 'nofpinput'});
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
            else if (args.innerHTML == 'Cetak SJ') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                /* this.pdfsrc = this.$urlbackend+'/api/sjprint/'+referensi
                this.popupPdf = true */
                let self = this
                /* var data = [
                    '\x1B' + '\x69' + '\x61' + '\x00' + '\x1B' + '\x40', // set printer to ESC/P mode and clear memory buffer
                    //'\x1B' + '\x69' + '\x4C' + '\x01', // set landscape mode
                    '\x1B' + '\x55' + '\x02', '\x1B' + '\x33' + '\x0F', // set margin (02) and line feed (0F) values
                    '\x1B' + '\x6B' + '\x0B' + '\x1B' + '\x58' + '\x00' + '\x3c' + '\x00', // set font and font size esc/p2
                    '\x1B' + '\x77' + '\x01', // set font and font size esc/p 2x 10.5
                    '\x1B' + '\x21' + '\x04', // set font condensed 
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    'Tanggal ', // "Printed by "
                    '25-Jan-22', // "QZ-Tray"
                    '\x0A'+'\x0A',// line feed 2 times
                    //'\x1B' + '\x61' + '\x02' ,// align right
                    //'Kepada YTH ', // "Printed by "
                    //'\x0A'+'\x0A',// line feed 2 times
                    //'Kepada YTH ', // "Printed by "
                    '\x0A' +'\x0A',// line feed 2 times
                    '\x0C' // <--- Tells the printer to print 
                ];
                console.log(data); */
                /* qz.websocket.connect().then(() => {
                        //return qz.printers.find("LABEL");
                        return qz.printers.getDefault()
                        //return qz.printers.find("xprinter");
                    }).then((printer) => {
                        let config = qz.configs.create(printer);
                        return qz.print(config, data)
                    }).then(() => {
                        return qz.websocket.disconnect();
                    }).then(() => {
                        //process.exit(0);
                    }).catch((err) => {
                        console.error(err);
                        // process.exit(1);
                    }); */

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
            else if (args.innerHTML == 'Export FP') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.inv_no
                
                this.$http.get('/api/fpcsv/'+referensi).then( resp => {
                    
                })  
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
          { name: 'kode_depan', type: 'string'},
          { name: 'no_fp', type: 'string'},
          { name: 'status', type: 'number'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/nomorfp',
        type: 'get',
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

