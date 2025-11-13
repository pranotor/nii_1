<template>
    <vs-row vs-justify="center">
     <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
       <vs-card>  
         <div class="d-md-flex align-items-center pb-2">
         <h3 class="card-title mb-0">Daftar Retur (Hutang) </h3>
         <div class="ml-auto">
           <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
             <i class="mdi mdi-border-color mr-1"></i> Input Data
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
                 <li>Edit</li>
             </ul>
         </JqxMenu>
         <h3>History Bayar / Retur</h3>
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
           { text: 'Tgl', datafield: 'ret_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
           { text: 'Customer', datafield: 'nama', width: '50%'},
           { text: 'No Retur', datafield: 'ret_no', width: '20%' },
           { text: 'Total', datafield: 'jumlah', width: '20%' ,cellsformat: 'D'},
           
         ],
         columns3: [
           { text: 'Tgl', datafield: 'inv_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
           { text: 'Jenis', datafield: 'jenis', width: '10%'},
           { text: 'No Invoice', datafield: 'inv_no', width: '20%' },
           { text: 'Total', datafield: 'total', width: '20%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                             return '<div style="margin: 8px;" class="jqx-right-align">' + this.dataAdapter.formatNumber(rowdata.total, 'd2') + '</div>';
           }},
           { text: 'Keterangan', datafield: 'keterangan', width: '40%' ,cellsformat: 'D'},
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
             this.$router.push({name : 'piutanginput'});
         },
         myGridOnContextMenu: function () {
                 return false;
         },
         piutangGridOnRowSelect: function(event) {
                  let ct_id = event.args.row.inv_no;
                  let dataSource2 = {
                      datafields2: [
                          { name: 'inv_tgl', type: 'date'},
                          { name: 'inv_no', type: 'string'},
                          { name: 'jenis', type: 'string'},
                          { name: 'total', type: 'number'},
                          { name: 'keterangan', type: 'string'}
                      ],
                      datatype: 'json',
                      data:{ inv_no: ct_id },
                      id: 'id',
                      url: this.$urlbackend+'/api/piutangbayarret',
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
             if (args.innerHTML == 'Edit') {
                 this.editrow = rowindex;
                 let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                 //console.log(dataRecord)
                 let referensi = dataRecord.inv_no
                 //cek apakah sudah posting
                 this.$router.push({name : 'piutangedit', params: { ref: referensi }});
             }
             else if (args.innerHTML == 'Cetak Invoice') {
                 this.editrow = rowindex;
                 let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                 let referensi = dataRecord.inv_no
                 this.pdfsrc = this.$urlbackend+'/api/invoicepdf/'+referensi
                 this.popupPdf = true
             }
         }
     },
     beforeCreate: function () {
       this.source = {
         datafields: [
           { name: 'ret_no', type: 'string'},
           { name: 'ret_tgl', type: 'date'},
           { name: 'nama', type:'number'},
           { name: 'jumlah', type:'number'},
         ],
         datatype: 'json',
         id: 'referensi',
         url: this.$urlbackend+'/api/returlist',
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
 
 