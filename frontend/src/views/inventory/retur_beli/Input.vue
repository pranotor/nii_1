<template>
    <vs-row vs-justify="center">
     <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
       <vs-card>
         <div class="d-md-flex align-items-center pb-2">
         <h3 class="card-title mb-0">Retur Pembelian {{this.$route.query.ref || this.$route.params.id}}</h3>
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
             v-model="formdata.tanggal"
             format="DD-MM-YYYY"
             value-type="format"
             placeholder="Pilih Tgl" input-class="vs-input--input"
             :disabled-date="disabledBeforeYear">
             </date-picker>
             </div>
             </div>
           </vs-col>
           <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
             <vs-input label="RETUR NO" class="w-100 mt-4" v-model="formdata.referensi"/>
           </vs-col>
           </vs-row>
          
           <hr class="custom-hr" />
         </div>
         <hr class="custom-hr" />
         <div >            
             <vs-table  :data="formdata.datatrans" class="text-nowrap">
                 <template slot="header">
                 <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                     <h5>DATA BARANG BPB</h5>
                 </vs-col>
                 </template>
                 <template slot="thead">
                     <vs-th>Kode Barang</vs-th>
                     <vs-th>Qty Terima</vs-th>
                     <vs-th>Qty Retur</vs-th>
                 </template>
                 <template slot-scope="{data}">
                     <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                     <vs-td>{{ data[indextr].kode_barang}}</vs-td>
                     <vs-td vs-align="right">{{ data[indextr].qty_terima }}</vs-td>
                     <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].qty_retur" v-currency="options"/>
                    </vs-td>
                     </vs-tr>
                 </template>
             </vs-table>
         </div>
         
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
   import 'vue2-datepicker/index.css';
   import { CurrencyDirective, parseCurrency } from 'vue-currency-input'
 
   export default {
     components: {
       DatePicker,
     },
     directives: {
         currency: CurrencyDirective
     },
     data: function () {
       return {
         // eslint-disable-next-line
         locale: 'us',
         currency: null,
         formdata: {
             referensi:'',
             guna_id:this.$route.params.id, // use BPB id for server-side reference
             jenis:'bpb',
             tanggal:'',
             opr:'',
             bpb_no:this.$route.query.ref || '',
             datatrans:[],
             isEdit: false,
         },
         errors:[],
       }
     },
     methods:{
         backpage: function(){
             this.$router.go(-1);
         },
         disabledBeforeYear(date) {
             return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
         },
         getAllData: function(){
             // init data from BPB detail using bpb_id
             const bpb_id = this.$route.params.id
             if(!bpb_id) return
             this.$http.post('/api/bpbdet',{ bpb_id: bpb_id }).then(res => {
                 const rows = res.data || []
                 // map into retur structure and add qty_retur
                 this.formdata.datatrans = rows.map((r, idx) => ({
                    id: idx+1,
                    kode_barang: r.kode_barang,
                    qty_terima: r.qty_terima,
                    qty_retur: 0
                 }))
             }).catch(() => {
                 this.$vs.notify({title:'Error',text:'Gagal memuat detail BPB',color:'danger'})
             })

             this.formdata.opr = this.$store.getters.getUserName
         },
         numberValue (value) {
             return parseCurrency(value.toString(), this.options)
         },
         checkForm:function() {
             this.errors = [];
             if(this.formdata.tanggal == ''){
                 this.errors.push("Tanggal belum terisi...")
             }
             if(this.formdata.referensi == ''){
                 this.errors.push("Referensi belum terisi...")
             }
             if(this.errors.length > 0){
                 this.notify()
             }
             else{
                 this.$vs.loading({'type':'radius'})
                 // NOTE: backend endpoint name may differ; adjust if needed
                 this.$http.post('/api/returbeli',{
                     formdata : this.formdata
                 }).then(() => {
                     this.$vs.loading.close()
                     this.$vs.notify({title:'Success',text:'Data berhasil disimpan..',color:'success'})
                     this.$router.push({name : 'returbelilist'});
                 }).catch(() => {
                     this.$vs.loading.close()
                     this.$vs.notify({title:'Error',text:'Gagal menyimpan data..',color:'danger'})
                 })
             }
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
     computed: {
         options () {
             return {
                 locale: this.locale,
                 currency: this.currency
             }
         }
     },
     watch:{
         'formdata.tanggal'(newTgl){
             const guna_id = this.formdata.guna_id
             const tanggal = newTgl
             if(tanggal){
                 this.$http.post('/api/referensi',{
                     jenis : 'rb', // retur beli
                     guna_id: guna_id,
                     tglJurnal : tanggal
                 }).then(response => {
                     this.formdata.referensi = response.data
                 }).catch(() => {})
             }
         }
     },
     mounted(){
        this.getAllData()
     }
   }
 </script>
 
 <style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
 <style lang="stylus">
 .cetak
   height: 90vh
 </style>
