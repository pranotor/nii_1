<template>
    <vs-row vs-justify="center">
     <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
       <vs-card>
         <div class="d-md-flex align-items-center pb-2">
         <h3 class="card-title mb-0">Transaksi Bank Masuk</h3>
         <div class="ml-auto">
           <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
            Batal
           </vs-button>
         </div> 
         </div>
         <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
             <b>Terdapat Kesalahan..</b>
             <ul class="common-list">
             <li v-for="error in errors" :key="error">{{ error }}</li>
             </ul>
         </vs-alert>
           <div>
           <vs-row>
           <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
             <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
             <label class="vs-input--label" >Tanggal</label>
             <div class="vs-con-input">
             <date-picker
             v-model="formdata.trans_tgl"
             format="DD-MM-YYYY"
             value-type="format"
             placeholder="Pilih Tgl" input-class="vs-input--input"
             :disabled-date="disabledBeforeYear">
             </date-picker>
             </div>
             </div>
           </vs-col>
           <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
            <vs-input label="Trans NO" class="w-100 mt-4" v-model="formdata.referensi"/>  
          </vs-col>
           </vs-row>
           <br/>
           <div>
            <h5 class="mb-0">Pilih Invoice</h5>
                <JqxDropDownButton ref="myDropDownButton" :width="150" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="onRowSelect($event)" @rowunselect="onRowUnselect($event)" @bindingcomplete="onBindingComplete"
                           :width="width" :source="dataAdapter" :columns="columns" :selectionmode="'checkbox'" 
                           :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true"
                            :virtualmode="true" :rendergridrows="rendergridrows"
                           :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                   >
                    </JqxGrid>
                </JqxDropDownButton>
            </div>
           <div>
            <br/>
            <h5>Total Bayar : {{ sumJumlah }}</h5>
            <br/>
            <vs-table  :data="formdata.invoices" class="text-nowrap">
                <template slot="header">
                <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                    <h5>Data Pembayaran</h5>
                </vs-col>
                </template>
                <template slot="thead">
                    <vs-th>No Invoice</vs-th>
                    <vs-th>Jumlah Blm dibayar</vs-th>
                    <vs-th>Jumlah Dibayar </vs-th>
                    <vs-th>+/-</vs-th>
                    <vs-th>Lunas</vs-th>
                    <vs-th>Keterangan</vs-th>
                    <vs-th>Aksi</vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ data[indextr].inv_no}}</vs-td>
                    <vs-td vs-align="right">
                        <vs-input :value="new Intl.NumberFormat('us-US',{style: 'decimal',maximumFractionDigits: 2}).format(data[indextr].total_num || 0)" disabled style="width:150px"/>
                    </vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].bayar" v-currency="options" style="width:150px"/>
                    </vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].selisih" v-currency="options" disabled style="width:150px"/>
                    </vs-td>
                    <vs-td>
                        <vs-switch v-model="data[indextr].status" vs-icon-off="close" vs-icon-on="done"/>
                    </vs-td>
                    <!-- <vs-td v-if="(data[indextr].status != data[indextr].status_real) || data[indextr].selisih_num >= 0">
                        <vs-input v-model="data[indextr].keterangan" />
                    </vs-td> -->
                    <vs-td>
                        <vs-input v-model="data[indextr].keterangan" />
                    </vs-td>
                    <vs-td>
                        <vs-button size="small" icon="delete" color="danger" type="flat" @click.stop="onRemoveInvoiceRow(data[indextr], indextr)"></vs-button>
                    </vs-td>
                    </vs-tr>
                </template>
            </vs-table>
           </div>
         </div>
         <br/>
         <div class="btn-alignment mt-4">
           <vs-button color="success" type="filled" @click.stop.prevent="checkForm">Simpan</vs-button>
           <vs-button color="dark" @click="backpage" type="filled">Batal</vs-button>
         </div>
       </vs-card>
     </vs-col>
     
    </vs-row>
 </template>
 
 <script>
   import DatePicker from 'vue2-datepicker';
   import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
   import JqxDropDownButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxdropdownbutton.vue";
   import JqxWindow from "jqwidgets-scripts/jqwidgets-vue/vue_jqxwindow.vue";
   import JqxInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxinput.vue";
   import JqxNumberInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxnumberinput.vue";
   import JqxButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxbuttons.vue";
   import JqxMenu from "jqwidgets-scripts/jqwidgets-vue/vue_jqxmenu.vue";
   import 'vue2-datepicker/index.css';
   import { CurrencyDirective, parseCurrency, setValue } from 'vue-currency-input'
   import _ from 'lodash'
 
   export default {
     components: {
        DatePicker,
        JqxGrid,
        JqxDropDownButton,
        JqxWindow,
        JqxInput,
        JqxNumberInput,
        JqxButton,
        JqxMenu
     },
     directives: {
         currency: CurrencyDirective
     },
     props: {
         jenisForm : String,
         jurnalUrl : String,
         title : String,
         routeBack : String
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
        // persistent selection across pages
        selectedMap: {}, // { [id]: rowObject }
        selectedOrder: [], // [id]
       columns: [
          { text: 'Tgl', datafield: 'inv_tgl', filtertype: 'range', width: '15%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Customer', datafield: 'nama', width: '40%'},
          { text: 'No Invoice', datafield: 'inv_no', width: '20%' },
          { text: 'Total', datafield: 'jumlah', width: '20%',cellsrenderer: (index, datafield, value, defaultvalue, column, rowdata) => {
                            let total = rowdata.total;
                            return '<div style="margin: 8px;" class="jqx-left-align">' + this.dataAdapter.formatNumber(total, 'd2') + '</div>';}}
        ],
         locale: 'us',
         currency: null,
         formdata: {
             trans_jumlah:0,
             referensi:'',
             trans_tgl:'',
             invoices:[],
             inv_id:[],
             inv_no:[],
             cust_id:'',
             keterangan:'',
             jenis:'',
             isEdit: false,
             prevReferensi:this.$route.params.ref,
         },
         errors:[],
         optjenis:['invoice','lainnya'],
        
       }
     },
     methods:{
         backpage: function(){
             this.$router.go(-1);
         },
         disabledBeforeYear(date) {
             return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
         },
         // Normalize and clone a row coming from the grid so calculations use numbers
         normalizeRecord(row) {
             const rec = JSON.parse(JSON.stringify(row))
             const totalRaw = (row.total !== undefined && row.total !== null)
                 ? row.total
                 : ((row.jumlah !== undefined && row.jumlah !== null) ? row.jumlah : 0)
             rec.total_num = Number(totalRaw)
             if (rec.bayar === undefined || rec.bayar === null || rec.bayar === '') {
                 rec.bayar = 0
             }
             const diff = Number(this.numberValue(rec.bayar) - rec.total_num)
             rec.selisih_num = diff
             rec.selisih = new Intl.NumberFormat('us-US',{style: 'decimal',maximumFractionDigits: 2}).format(diff)
             return rec
         },
         addOrMergeSelection(rec) {
             const id = rec.id
             if (!this.selectedMap[id]) {
                 this.selectedOrder.push(id)
                 this.formdata.inv_id.push(id)
                 this.formdata.inv_no.push(rec.inv_no)
                 if (!this.formdata.cust_id) this.formdata.cust_id = rec.cust_id
             } else {
                 // keep user edits that may already exist
                 const exist = this.selectedMap[id]
                 rec.bayar = exist.bayar
                 rec.status = exist.status
                 rec.keterangan = exist.keterangan
                 rec.selisih = exist.selisih
                 rec.selisih_num = exist.selisih_num
             }
             this.selectedMap[id] = rec
         },
         removeSelectionById(id) {
             if (this.selectedMap[id]) delete this.selectedMap[id]
             this.selectedOrder = this.selectedOrder.filter(x => x !== id)
             this.formdata.inv_id = this.formdata.inv_id.filter(x => x !== id)
             // also drop inv_no to keep simple rebuild later
             this.formdata.inv_no = this.selectedOrder.map(x => (this.selectedMap[x] ? this.selectedMap[x].inv_no : null)).filter(Boolean)
             if (this.selectedOrder.length === 0) this.formdata.cust_id = ''
         },
         rebuildInvoicesFromMap() {
             this.formdata.invoices = this.selectedOrder.map(id => this.selectedMap[id]).filter(Boolean)
         },
         onRowSelect(event) {
             const args = event.args || {}
             const rowIndex = args.rowindex
             const dataRecord = this.$refs.myGrid.getrowdata(rowIndex)
             if (!dataRecord) return
             const rec = this.normalizeRecord(dataRecord)
             this.addOrMergeSelection(rec)
             this.rebuildInvoicesFromMap()
         },
         onRowUnselect(event) {
             const args = event.args || {}
             const rowIndex = args.rowindex
             const dataRecord = this.$refs.myGrid.getrowdata(rowIndex)
             if (!dataRecord) return
             this.removeSelectionById(dataRecord.id)
             this.rebuildInvoicesFromMap()
         },
         onBindingComplete() {
             // keep invoices list in sync when grid rebinds (e.g., paging)
             this.rebuildInvoicesFromMap()
         },
         onRemoveInvoiceRow(rec, index) {
             const id = rec && rec.id
             if (id !== undefined && id !== null) {
                 this.removeSelectionById(id)
             } else {
                 // Fallback: remove by index and rebuild the selection structures
                 if (Array.isArray(this.formdata.invoices)) {
                     this.formdata.invoices.splice(index, 1)
                 }
                 this.selectedOrder = (this.formdata.invoices || []).map(x => x && x.id).filter(x => x !== undefined && x !== null)
                 const newMap = {}
                 ;(this.formdata.invoices || []).forEach(x => { if (x && x.id !== undefined && x.id !== null) newMap[x.id] = x })
                 this.selectedMap = newMap
                 this.formdata.inv_id = this.selectedOrder.slice()
                 this.formdata.inv_no = this.selectedOrder.map(x => (this.selectedMap[x] ? this.selectedMap[x].inv_no : null)).filter(Boolean)
                 if (this.selectedOrder.length === 0) this.formdata.cust_id = ''
             }
             this.rebuildInvoicesFromMap()
         },
         myGridOnRowSelect: function (event) {
            let args = event.args;
            let row = this.$refs.myGrid.getrowdata(args.rowindex);
            //console.log(row);
            let dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 5px;">' + row['inv_no'] + '</div>';
            this.$refs.myDropDownButton.setContent(dropDownContent);
            this.formdata.inv_no = row['inv_no']
            this.formdata.cust_id = row['cust_id']
            ///* this.formdata.qt_id = row['id']
            //this.getInv(row)
        },
       
         getAllData: function(){
             //edit mode init data
             if(this.$route.params.ref != null){
                 this.formdata.isEdit = true
                 this.$http.get("/api/sj/"+this.$route.params.ref).then(response => {
                     const keys = Object.keys(response.data)
                     for(const key of keys){
                         console.log(key)
                         if (this.formdata.hasOwnProperty(key))
                             this.formdata[key] = response.data[key]
                         if(key == 'sj_tgl'){
                                 console.log('tgl sj masuk')
                                 let tgldata = response.data[key]
                                 let arrtgl = tgldata.split("-")
                                 let d = new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2])
                                 let dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
                                 let [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
                                 let tgltrans = `${day}-${month}-${year}`
                                 this.formdata.sj_tgl = tgltrans
                             }      
                     }
                 }).catch(error=>{
                     console.log(error)
                     this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                 })
             }
             //edit mode init data
 
             this.formdata.opr = this.$store.getters.getUserName
         },
         acceptAlert: function(){
             this.$vs.notify({
                 color:this.colorAlert,
                 title:'Accept Selected',
                 text:'Lorem ipsum dolor sit amet, consectetur'
             })
         },
         
         clearTrans: function(){
             this.qty_pesan = ""
             this.qty_terima = ""
             this.barang = ""
             this.stock = ""
             //this.setFocusOnInput('uraian');
             this.errorBarang = false;
         },
         setFocusOnInput(inputName) {
             /** 
             * @see https://vuejs.org/v2/api/#vm-el 
             * @see https://vuejs.org/v2/api/#vm-refs
             */
             // you could just call this.$refs[inputName].focusInput() but i'm not shure if it belongs to the public API
             let inputEl = this.$refs[inputName].$el.querySelector('input');
             //console.log(inputEl.focus) // <== See if `focus` method avaliable
             inputEl.focus() //  <== This time the focus will work properly
         },
         numberValue (value) {
             //alert(value)
             const val = value ? value : 0;
             return parseCurrency(val.toString(), this.options)
         },
         
         checkForm:function(e) {
             this.errors = [];
             //cek dari client dulu...
             if(this.formdata.trans_tgl == ''){
                 this.errors.push("Tanggal belum terisi...")
             }
             if(this.errors.length > 0){
                 this.handleError(this.error,'local')
             }
             else{
                 this.$vs.loading({'type':'radius'})
                 this.$http.post(`/api/bankin`,{
                     formdata : this.formdata
                 }).then(response => {
                     this.handleSuccess();
                     //
                 }).catch(error=>{
                     this.handleError(error)
                 })
             }
         },
         handleError: function(error,type=''){
             if(type != 'local'){
                 if (error.response != null){
                     let status = error.response.status
                     console.log(status)
                     switch(status){
                         case 422 :
                             const keys = Object.keys(error.response.data.errors)
                             //console.log(keys);
                             for(const key of keys){
                                 //console.log(key)
                                 //console.log(error.response.data.errors[key][0])
                                 this.errors.push(error.response.data.errors[key][0])
                             }
                         break
                         case 500 :
                             this.errors.push('Internal server error.. silahkan hubungi administrator')
                             break
                         default:
                             this.errors.push('Terjadi error di server..')
                             break
                     }
                 }
                 else
                     this.errors.push('Terjadi error di server..')
             }
             console.log('handle error')
             this.notify()
             this.$vs.loading.close()
         },
         handleSuccess: function(){
             this.$vs.loading.close()
             this.$vs.notify({title:'Success',text:'Data berhasil disimpan..',color:'success'})
             //this.$router.push('/jurnal/ju');
             this.$router.push({name : 'bankinlist'});
         },
         notify: function(){
             this.$vs.notify({
                 title:'Gagal Menyimpan Data',
                 text:'Click untuk melihat',
                 color:'danger',
                 fixed:false,
                 icon: 'warning',
                 click:()=>{
                     window.scrollTo(0,0);
                 },
             })
         }
     },
     beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'inv_no', type: 'string'},
          { name: 'inv_tgl', type: 'date'},
          { name: 'nama', type:'string'},
          { name: 'cust_id', type:'number'},
          { name: 'po_cust',type:'string'},
          { name: 'total', map:'selisih'},
          { name: 'status', type:'boolean'},
        ],
        cache: false,
        beforeprocessing: (data) => {
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
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/piutanggrid',
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
     computed: {
         options () {
             return {
                 locale: this.locale,
                 currency: this.currency
             }
         },
         gunaDanTgl() {
            return `${this.formdata.guna_id}|${this.formdata.trans_tgl}`;
         },
         sumJumlah() {
            let initialValue = 0
            let sum = this.formdata.invoices.reduce(
                (accumulator, currentValue) => Number(accumulator) + Number(this.numberValue(currentValue.bayar))
                , initialValue)
            this.formdata.trans_jumlah = sum      
            return new Intl.NumberFormat('de-DE',{style:'currency',currency:'IDR'}).format(sum)
        },
         
         
     },
     watch:{
        gunaDanTgl(newVal) {
                console.log('berubah')
                const [newGuna, newTgl] = newVal.split('|');
                let guna_id = newGuna
                let tanggal = newTgl
                if(tanggal){
                    this.$http.post('/api/referensi',{
                        jenis : 'bankin',
                        tglJurnal : tanggal
                    }).then(response => {
                        this.formdata.referensi = response.data
                    }).catch(error=>{
                        console.log(error)
                    })
                }
        },
        'formdata.invoices': {
            deep: true,
            handler (newValue) {
                console.log(newValue);
                newValue.forEach((item) => {
                    let bayar = 0
                    if(!item.bayar)
                        bayar = 0;
                    else
                        bayar = item.bayar
                    const tagihan = Number(item.total_num != null ? item.total_num : this.numberValue(item.total))
                    const payment = Number(this.numberValue(bayar))
                    if(Number(payment - tagihan) >= 0) {
                        item.status = true
                        item.status_real = true
                        
                    }
                    else{
                        item.status_real = false
                    }
                    item.selisih = new Intl.NumberFormat('us-US',{style: 'decimal',maximumFractionDigits: 2}).format(Number(payment - tagihan))
                    item.selisih_num = Number(payment - tagihan);
                })
            },
        }
    },
     mounted: function () {
         this.getAllData();
     },
   }
 </script>
 <style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
 <style lang="stylus">
 .cetak
   height: 90vh
 </style>