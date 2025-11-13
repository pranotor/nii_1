<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Transaksi Bank Masuk</h3>
        <div class="ml-auto">
          <vs-button @click="inputTrans"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Transaksi
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @rowclick="myGridOnRowClick($event)"
            @contextmenu="myGridOnContextMenu()"
            @rowselect="bankGridOnRowSelect($event)" 
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="30" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Batal</li>
            </ul>
        </JqxMenu>
        <br/>
        <h3>Detail Pembayaran </h3>
        <JqxGrid ref="detailGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns3" :showstatusbar="true" :showaggregates="true" :columnsresize="true">
                  
        </JqxGrid>
        
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
          { text: 'Tgl', datafield: 'trans_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No Trans', datafield: 'trans_no', width:'10%' },
          { text: 'Jumlah', datafield: 'trans_jumlah', width: '15%',cellsformat:'D' },
          { text: 'Invoice', datafield: 'inv_no', width: '15%',cellsformat:'D' },
          { text: 'Cuctomer', datafield: 'nama', width: '28%'},
          { text: 'Nick', datafield: 'nick', width: '22%'}
        ],
        columns3: [
          { text: 'Inv No', datafield: 'inv_no', width: '20%'},
          { text: 'Jumlah', datafield: 'jumlah', width: '30%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            let total = rowdata.jumlah;
                            let tipe = rowdata.tipe;
                            if(tipe == 'outcome')
                                total = -1*total
                            return '<div style="margin: 8px;" class="jqx-left-align">' + this.dataAdapter.formatNumber(total, 'd2') + '</div>';}},
          { text: 'Keterangan', datafield: 'keterangan', width: '50%' },
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
        inputTrans: function(){
            this.$router.push({name : 'bankininput'});
        },
        inputForm: function(){
            this.$router.push({name : 'quotationinput'});
        },
        myGridOnContextMenu: function () {
                return false;
        },
        bankGridOnRowSelect: function(event) {
                let bankin_id = event.args.row.id;
                let dataSource2 = {
                    datafields: [
                        { name: 'inv_no', type: 'string'},
                        { name: 'jumlah', type: 'number'},
                        { name: 'keterangan', type: 'string'},
                        { name: 'tipe', type: 'string'}
                    ],
                    datatype: 'json',
                    data:{ bankin_id: bankin_id },
                    id: 'id',
                    url: this.$urlbackend+'/api/bankindet',
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
            if (args.innerHTML == 'Batal') {
              this.editrow = rowindex;
              let rowid = this.$refs.myGrid.getrowid(rowindex)
              let data = this.$refs.myGrid.getrowdatabyid(rowid)
              console.log(data);
              
              if(confirm("Anda yakin akan membatalkan Pembayaran ini...?")){
                  this.$http.delete('/api/bankin/' + data.id).then(resp => {
                      this.$refs.myGrid.deleterow(rowid);
                      this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                  }).catch(error => {
                      this.$vs.notify({title:'GAGAL..',text:'Gagal hapus data..',color:'danger'})
                  })
              }
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'trans_tgl', type: 'date'},
          { name: 'trans_jumlah', type:'number'},
          { name: 'keterangan', type:'string'},
          { name: 'trans_no', type:'string'},
          { name: 'inv_no', type:'string'},
          { name: 'nama', type:'string'},
          { name: 'nick', type:'string'},
          { name: 'id', type:'number'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/bankin',
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

