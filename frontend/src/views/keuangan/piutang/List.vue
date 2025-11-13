<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Piutang </h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
          <vs-button @click="exportData"  color="orange" type="filled" class="mt-4 ml-2 mt-md-0" >
             Export Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            @rowselect="piutangGridOnRowSelect($event)"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns"  @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Batal</li>
            </ul>
        </JqxMenu>
        <h3>History Bayar / Retur</h3>
         <JqxGrid ref="detailGrid"
                  :width="width" :height="250"
                  :keyboardnavigation="false" :columns="columns3" >
                   
         </JqxGrid>
      </vs-card>
      <vs-prompt @cancel="val=''" @accept="exportDataAct"  title="Pilih Tanggal" :active.sync="activePrompt" accept-text="Proses" cancel-text="Batal">
       <div class="con-exemple-prompt">
        <date-picker
            v-model="tanggal"
            format="DD-MM-YYYY"
            value-type="format"
            placeholder="Pilih Tgl" input-class="vs-input--input"
            >
            </date-picker> 
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
    const FileDownload = require('js-file-download');
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    
  export default {
    components: {
        JqxGrid,
        JqxWindow,
        JqxInput,
        JqxNumberInput,
        JqxButton,
        JqxMenu,
        DatePicker,
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
          { text: 'Tgl', datafield: 'inv_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Customer', datafield: 'nama', width: '15%'},
          { text: 'Nick', datafield: 'nick', width: '10%'},
          { text: 'No Invoice', datafield: 'inv_no', width: '10%' },
          { text: 'No PO', datafield: 'po_cust', width: '10%' },
          { text: 'P', filtertype: 'list', filteritems: this.items, datafield: 'p', width: '4%'},
          { text: 'Jatuh Tempo', datafield: 'payment_due', filtertype: 'date', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Total', datafield: 'total', width: '11%' ,cellsformat: 'D'},
          { text: 'Bayar', datafield: 'bayar', width: '11%' ,cellsformat: 'D'},
          { text: 'Status', filtertype: 'list', filteritems: this.items, datafield: 'status_piutang', width: '9%' ,cellsformat: 'D'},
          
        ],
        columns3: [
          { text: 'Tgls', datafield: 'tgl', width: '10%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            let arrtgl = rowdata.tgl.split("-")
                            return '<div style="margin: 8px;" class="jqx-right-align">' + this.dataAdapter.formatDate(new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2]), 'dd-MM-yyyy') + '</div>';
          }},
          { text: 'No Invoice', datafield: 'inv_no', width: '30%' },
          { text: 'Jumlah', datafield: 'jumlah', width: '20%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            return '<div style="margin: 8px;" class="jqx-right-align">' + this.dataAdapter.formatNumber(rowdata.jumlah, 'd2') + '</div>';
          }},
          { text: 'Keterangan', datafield: 'keterangan', width: '40%' ,cellsformat: 'D'},
        ],
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: '',
        dataCetak:[],
        tanggal:'',
        activePrompt:false,
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({name : 'piutanginput'});
        },
        exportData: function() {
            this.activePrompt = true;
        },
        exportDataAct : function() {      
            if(!this.tanggal){
                alert('Tgl belum dipilih...');
                this.activePrompt = true;
            }
            else{
                this.$vs.loading()
                let [tgl, bln, thn] = this.tanggal.split('-');
                
                this.$httpxls.get('/api/piutangxls/'+this.tanggal,{responseType: 'blob'}).then( response => {
                    FileDownload(response.data, 'piutang_'+tgl+'-'+bln+'-'+thn+'.xlsx');
                    this.$vs.loading.close()
                })  
            }
        },
        myGridOnContextMenu: function () {
                return false;
        },
        piutangGridOnRowSelect: function(event) {
                 let inv_no = event.args.row.inv_no;
                 let dataSource2 = {
                     datafields2: [
                         { name: 'tgl', type: 'date'},
                         { name: 'inv_no', type: 'string'},
                         { name: 'jumlah', type: 'number'},
                         { name: 'keterangan', type: 'string'}
                     ],
                     datatype: 'json',
                     data:{ inv_no: inv_no },
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
              
              if(confirm("Anda yakin akan membatalkan Invoice ini, data pembayaran akan terhapus juga...?")){
                  this.$http.delete('/api/piutang/' + data.inv_no).then(resp => {
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
          { name: 'inv_no', type: 'string'},
          { name: 'inv_tgl', type: 'date'},
          { name: 'payment_due', type: 'date'},
          { name: 'nama', type:'string'},
          { name: 'nick', type:'string'},
          { name: 'po_cust',type:'string'},
          { name: 'p',type:'string'},
          { name: 'total', type:'number'},
          { name: 'bayar', type:'number'},
          { name: 'status_piutang', type:'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/piutanglist',
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

.mx-datepicker-popup
  z-index : 80000

.cetak
  height: 80vh

</style>

