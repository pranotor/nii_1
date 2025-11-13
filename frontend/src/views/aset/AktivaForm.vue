<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Input Aktiva {{this.$route.params.ref}}</h3>
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
            <label class="vs-input--label" >TGL</label>
            <div class="vs-con-input">
            <date-picker
            v-model="formdata.tanggal"
            format="DD-MM-YYYY"
            value-type="format"
            placeholder="Pilih Tgl" input-class="vs-input--input">
            </date-picker>
            </div>
            </div>
          </vs-col>
          <vs-col vs-lg="2" vs-xs="12" vs-sm="4"> 
              <vs-input label="NO BUKTI / VOUCHER" class="w-100 mt-4" v-model="formdata.nmr_bukti" /> 
             </vs-col>
          <vs-col vs-lg="5" vs-xs="12" vs-sm="4">  
            <vs-select class="w-100 mt-4" label="KELOMPOK AKTIVA" ref="perkiraan" v-model="formdata.kode">
            <vs-select-item
                :key="index"
               :value="item.kode_perk"
                :text="parseNama(item.nama)"
                v-for="(item,index) in optjenisAktiva"
            />    
            </vs-select> 
          </vs-col>
          <vs-col vs-lg="2" vs-xs="12" vs-sm="4"> 
              <vs-input label="KODE ASSET" class="w-100 mt-4" v-model="formdata.kode_asset" v-currency="options"/> 
             </vs-col>
          </vs-row>
          <vs-row>
              <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
                <vs-select class="w-100 mt-4" label="GOLONGAN TARIF" ref="perkiraan" v-model="formdata.gol">
                    <vs-select-item
                        :key="index"
                    :value="item.gol"
                        :text="item.gol"
                        v-for="(item,index) in optGol"
                    />    
                </vs-select>
              </vs-col>
              <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
                <vs-input label="MASA (TAHUN)" disabled class="w-100 mt-4" v-model="formdata.masa" v-currency="options"/> 
              </vs-col>
              <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
                <vs-input label="TARIF %" disabled class="w-100 mt-4" v-model="formdata.tarif" v-currency="options"/> 
              </vs-col>
              <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
                <vs-input label="METODE SUSUT" disabled class="w-100 mt-4" v-model="formdata.metode" v-currency="options"/> 
              </vs-col>
          </vs-row>
          <vs-row>
             <vs-col vs-lg="12" vs-xs="12" vs-sm="4"> 
              <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="formdata.uraian" class="mt-4 w-100" />
             </vs-col>
          </vs-row>
          <vs-row>
             <vs-col vs-lg="1" vs-xs="12" vs-sm="4"> 
                 <vs-input label="SATUAN" :disabled="enable_edit" class="w-100 mt-1" v-model="formdata.satuan" /> 
             </vs-col>
             <vs-col vs-lg="3" vs-xs="12" vs-sm="4"> 
                 <vs-input label="JUMLAH" :disabled="enable_edit" class="w-100 mt-1" v-model="formdata.jumlah" v-currency="options"/> 
             </vs-col>
             <vs-col vs-lg="3" vs-xs="12" vs-sm="4"> 
                 <vs-input label="HARGA SATUAN" :disabled="enable_edit" class="w-100 mt-1" v-model="formdata.harga_unit" v-currency="options"/> 
             </vs-col>
             <vs-col vs-lg="3" vs-xs="12" vs-sm="4"> 
                 <vs-input label="TOTAL HARGA" disabled class="w-100 mt-1" v-model="formdata.harga_beli" v-currency="options"/> 
             </vs-col>
          </vs-row>
          
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
  import { CurrencyDirective, parseCurrency, setValue } from 'vue-currency-input'
  import _ from 'lodash'

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
        locale: 'de',
        currency: null,
        datetrans: '',
        formdata: {
            tanggal:'',
            nmr_bukti:'',
            kode:'',
            kode_asset:'',
            uraian:'',
            satuan:'',
            jumlah:'0',
            harga_unit:'0',
            harga_beli:'',
            gol:'',
            opr:'',
            posting:'0',
            isEdit: false,
            prevReferensi:''
        },
        enable_edit: false,
        optjenisAktiva:[],
        optGol:[],
        errors:[],
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        getAllData: function(){
            this.$http.get('/api/perkiraan').then(response => {
                //console.log(response.data);
                this.optjenisAktiva = response.data.filter(item =>{
                    if(item.kode_perk.substring(0,2)=='31' && item.kode_perk.substring(0,5)!='31.10')
                        return item;
                }) 
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/tarif').then(response => {
                console.log(response.data);
                this.optGol = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get("/api/aktiva/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                     const keys = Object.keys(response.data)
                    console.log(keys)
                    var self = this
                    for(const key of keys){
                        //console.log(key)
                        if (this.formdata.hasOwnProperty(key))
                            this.formdata[key] = response.data[key]
                        if(key == 'asset_id'){
                            console.log('kode masuk')
                            this.formdata.prevReferensi = response.data[key]
                        }
                        if(key == 'posting'){
                            if(response.data[key]==1)
                                this.enable_edit = true
                            else
                                this.enable_edit = false
                        }
                            
                    }
                }).catch(error=>{
                    console.log(error)
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                });
            }
            //edit mode init data

            this.formdata.opr = this.$store.getters.getUserName
        },
        parseNama: function(nama){
            var parts = nama.split('+');
            return parts[0].replace(/=/g, '\u00a0') + parts[1];        
        },
       
        numberValue (value) {
            //alert(value)
            return parseCurrency(value.toString(), this.options)
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            if(this.formdata.tanggal == ''){
                this.errors.push("Tanggal belum terisi...")
            }
            if(this.formdata.kode == ''){
                this.errors.push("Kelompok Aktiva belum terisi...")
            }
            if(this.formdata.gol == ''){
                this.errors.push("Golongan Tarif belum dipilih...")
            }
            if(this.formdata.uraian == ''){
                this.errors.push("Uraian belum terisi...")
            }
            if(this.formdata.satuan == ''){
                this.errors.push("Satuan belum terisi...")
            }
            if(this.formdata.harga_beli == '0'){
                this.errors.push("Harga dan jumlah belum terisi...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/aktiva',{
                    formdata : this.formdata
                }).then(response => {
                    console.log(response);
                    this.handleSuccess();
                    //
                }).catch(error=>{
                    console.log(error.response)
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
            this.$router.push({name : 'aktivalist'});
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
        },
    },
    computed: {
        options () {
            return {
                locale: this.locale,
                currency: this.currency
            }
        },
        jumlahDanHarga() {
          return `${this.formdata.jumlah}|${this.formdata.harga_unit}`;
        },
    },
    watch:{
        'formdata.gol': function(){
            var self = this
            let gol = this.formdata.gol
            let objGol = this.optGol.find(item => item.gol == gol)
            this.formdata.masa = objGol.masa
            this.formdata.tarif = objGol.tarif
            this.formdata.metode = objGol.jenis
        },
        jumlahDanHarga(newVal) {
          const [newJumlah, newHarga] = newVal.split('|');
          let jumlah = this.numberValue(newJumlah)
          let harga = this.numberValue(newHarga)
          let harga_beli = jumlah * harga
          this.formdata.harga_beli = new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(harga_beli)
          
        },

    },
    mounted: function () {
        this.getAllData();
    },
  }
</script>