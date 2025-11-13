<template>
    <vs-row vs-justify="center">
     <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
       <vs-card>  
         <div class="d-md-flex align-items-center pb-2">
         <h3 class="card-title mb-0">Proses Produksi (WO)</h3>
         <div class="ml-auto">
           <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
             <i class="mdi mdi-border-color mr-1"></i> Input Data
           </vs-button>
         </div>  
         </div>
         <hr class="custom-hr" />  
         <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
             @rowselect="cuttingGridOnRowSelect($event)"
             :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
             :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
             :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
         />
         <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                  :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
             <ul>
                 <li>Edit</li>
                 <li>Batal</li>
             </ul>
         </JqxMenu>
         <br/>
         <h3>Produksi Detail</h3>
         <JqxGrid ref="detailGrid"
                  :width="width" :height="250"
                  :keyboardnavigation="false" :columns="columns3" >
                   
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
          { text: 'Tgl Produksi', datafield: 'prod_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Tgl SO', datafield: 'so_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No Dok', datafield: 'prod_no', width: '20%' },
          { text: 'No PO', datafield: 'po_no', width: '20%' },
          { text: 'Customer', datafield: 'customer', width: '40%'}
        ],
         columns3: [
           { text: 'Kode Barang', datafield: 'kode_barang', width: '18%'},
           { text: 'Nama', datafield: 'nama_barang2', width: '12%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            console.log(rowdata)
                            let ret = "";
                            if(!rowdata.detailquot)
                                ret = ""
                            else ret = rowdata.detailquot.nama_barang2;
                            return '<div style="margin: 8px;" class="jqx-left-align">' + ret + '</div>';}},
           { text: 'Size', datafield: 'size', width: '50%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            console.log(rowdata)
                            let ret = "";
                            if(!rowdata.detailquot)
                                ret = ""
                            else ret = rowdata.detailquot.size;
                            return '<div style="margin: 8px;" class="jqx-left-align">' + ret + '</div>';}},
           { text: 'Qty', datafield: 'qty', width: '10%' },        
           { text: 'FP', datafield: 'fp', width: '10%' }  
         ],
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
         
         cuttingGridOnRowSelect: function(event) {
                 let ct_id = event.args.row.id;
                 let dataSource2 = {
                     datafields2: [
                         { name: 'kode_barang', type: 'string'},
                         { name: 'harga', type: 'number'},
                         { name: 'namabarang', map: 'detailquot>nama_barang2'},
                         { name: 'qty', type: 'number'},
                         { name: 'fp', type: 'number'}
                     ],
                     datatype: 'json',
                     data:{ prod_id: ct_id },
                     id: 'id',
                     url: this.$urlbackend+'/api/produksidet',
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
         
         inputForm: function(){
             this.$router.push({name : 'produksiinput'});
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
             if (args.innerHTML == 'Batal') {
                 this.editrow = rowindex;
                 let rowid = this.$refs.myGrid.getrowid(rowindex)
                 let data = this.$refs.myGrid.getrowdatabyid(rowid)
                 if(confirm("Yakin menghapus data?")){
                         this.$http.delete('/api/produksi/' + data.id).then(resp => {
                             console.log(resp)
                             this.$refs.myGrid.deleterow(rowid);
                             this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                         }).catch(error => {
                             console.log(error);
                             this.$vs.notify({title:'GAGAL..',text:'Gagal hapus data..',color:'danger'})
                         })
                     }
             }
             else if (args.innerHTML == 'Edit') {
                 this.editrow = rowindex;
                 let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                 let referensi = dataRecord.prod_no;
                 this.$router.push({name : 'produksiedit', params: { ref: referensi }});
             }
         }
     },
     beforeCreate: function () {
       this.source = {
        datafields: [
          { name: 'prod_no', type: 'string'},
          { name: 'wo_no', type: 'string'},
          { name: 'id', type: 'number'},
          { name: 'prod_tgl', type: 'date'},
          { name: 'customer', map: 'work>quotation>qcustomer>nick'},
          { name: 'po_no', map: 'work>quotation>po_cust'},
          { name: 'so_tgl', map: 'work>quotation>so_tgl', type:'date'},
          { name: 'id', type: 'number'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/produksilist',
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
 
 