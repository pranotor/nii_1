<template>
    <vs-row vs-justify="center">
     <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
       <vs-card>
         <div class="d-md-flex align-items-center pb-2">
         <h3 class="card-title mb-0">Blokir Customer</h3>
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
           <div>
            <h5 class="mb-0">Pilih Customer</h5>
                <JqxDropDownButton ref="myDropDownButton" :width="150" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="onRowselect($event)" @rowunselect="onRowselect($event)" 
                            :width="width" :source="dataAdapter" :columns="columns" :selectionmode="'checkbox'" 
                            :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true" 
                            :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                    >
                    </JqxGrid>
                </JqxDropDownButton>
            </div>
            <br/>
            <p>Customer yang dipilih: </p>
            {{ formdata.cust_nick }}
         </div>
         <br/>
         <div class="btn-alignment mt-4">
           <vs-button color="success" type="filled" @click.stop.prevent="checkForm">Blokir</vs-button>
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
        width: "100%",
        columns: [
          { text: 'Nama', datafield: 'nama', width: '40%', },
          { text: 'Nick', datafield: 'nick', width: '30%', },
          { text: 'Kota', datafield: 'kota', width: '35%'},
        ],
         locale: 'us',
         currency: null,
         formdata: {
             cust_id:[],
             cust_nick:[],
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
         onRowselect: async function (event) {
            //console.log(event)
            var self = this;
            let rows = this.$refs.myGrid.getselectedrowindexes();
            self.formdata.cust_id = [];
            self.formdata.cust_nick = [];
            await rows.forEach(item => {
                let dataRecord = this.$refs.myGrid.getrowdata(item)
                self.formdata.cust_id.push(dataRecord.id)
                self.formdata.cust_nick.push(dataRecord.nick)
            });
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
             if(this.errors.length > 0){
                 this.handleError(this.error,'local')
             }
             else{
                 this.$vs.loading({'type':'radius'})
                 this.$http.post(`/api/customerblokir`,{
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
             this.$router.push({name : 'blokir'});
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
          { name: 'nama', type: 'string'},
          { name: 'nick', type: 'string'},
          { name: 'alamat', type: 'string'},
          { name: 'kota', type: 'string'},
          { name: 'kontak_person', type: 'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/customeraktif',
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
                    let tagihan = this.numberValue(item.total)
                    let payment = this.numberValue(bayar)
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