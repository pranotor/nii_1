<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-popup fullscreen title="BUKTI PERMINTAAN" :active.sync="popupPdf">
        <div class="cetak">  
            <iframe width="100%" height="100%" :src="pdfsrc" frameborder="0" />
        </div>
      </vs-popup>
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Surat Jalan Sample {{this.$route.params.ref}}</h3>
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
            <vs-input label="SJ NO" class="w-100 mt-4" v-model="formdata.referensi"/>  
          </vs-col>
          </vs-row>
         
          <hr class="custom-hr" />
          
          
          
        </div>
        <hr class="custom-hr" />
        <div >            
            <vs-table  :data="formdata.datatrans" class="text-nowrap">
                <template slot="header">
                <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                    <h5>DATA PERMINTAAN BARANG</h5>
                </vs-col>
                

                </template>
                <template slot="thead">
                    <vs-th>Kode Barang - Nama Barang</vs-th>
                    <vs-th>Size</vs-th>
                    <vs-th>Nama 2</vs-th>
                    <vs-th>Stok</vs-th>
                    <vs-th>Satuan</vs-th>
                    <vs-th>Jumlah Blm Terkirim</vs-th>
                    <vs-th>Jumlah Dikirim</vs-th>
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                    <vs-td>{{ data[indextr].size}}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].nama2 }}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].stock }}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].satuan }}</vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].qty_pesan2" v-currency="options" disabled/>
                    </vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].qty_kirim" v-currency="options"/>
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
  import { CurrencyDirective, parseCurrency, setValue } from 'vue-currency-input'
  import _ from 'lodash'

  export default {
    components: {
      DatePicker,
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
        locale: 'de',
        currency: null,
        formdata: {
            referensi:'',
            guna_id:this.$route.params.ref,
            cust_id:'',
            jenis:'sr',
            tanggal:'',
            opr:'',
            leadtime:'',
            validity:'',
            datatrans:[],
            datajurnal:[],
            isEdit: false,
            prevReferensi:this.$route.params.ref,
        },
        dk:true,
        seen:false,
        errors:[],
        idtrans:0,
        numSumDebet:0,
        numSumKredit:0,
        popupPdf: false,
        pdfsrc: '',
        
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        disabledBeforeYear(date) {
            return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
        },
        getDetQuot: function(tanggal=''){
            let url = "/api/quotation/"+this.$route.params.ref;
            if(tanggal != '')
                url = url + "/" + tanggal;
            this.$http.get(url).then(response => {
                this.formdata.datatrans = response.data;
                const keys = Object.keys(response.data)
                for(const key of keys){
                    let subkeys = Object.keys(response.data[key])
                    subkeys.forEach(item => {
                        if (this.formdata.hasOwnProperty(item))
                            this.formdata[item] = response.data[key][item]
                        
                        if(item == 'datatrans'){
                            let arr = Object.values(response.data[key][item]);
                            console.log(arr)
                            let maxId = _.maxBy(arr, function(o) { return o.id; })
                            //console.log(maxId)
                            //let min = Math.min(...arr);
                            //let max = Math.max(...arr);

                            this.idtrans = maxId.id
                        }
                        
                        
                    })
                }
            }).catch(error=>{
                console.log(error)
                this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
            })
        },
        getAllData: function(){
            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.getDetQuot()
            }
            //edit mode init data

            this.formdata.opr = this.$store.getters.getUserName
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
            return parseCurrency(value.toString(), this.options)
        },
        
        checkForm:function(e) {
            var self = this;
            this.errors = [];
            //cek dari client dulu...
            if(this.formdata.tanggal == ''){
                this.errors.push("Tanggal belum terisi...")
            }
            if(this.formdata.referensi == ''){
                this.errors.push("Referensi belum terisi...")
            }

            //cek qty keluar
            this.formdata.datatrans.forEach((item) => {
                let kode_barang = item.kode_barang;
                let qty_pesan2 = Number(self.numberValue(item.qty_pesan2));
                let stok = Number(item.stock);
                let qty_kirim = Number(self.numberValue(item.qty_kirim));
                if(qty_kirim > stok || qty_kirim > qty_pesan2){
                    self.errors.push("Jumlah kirim "+ kode_barang +" tidak boleh melebihi stok atau jumlah yang belum terkirim..");
                }
                    
            })

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/sj',{
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
            this.$router.push({name : 'sjsrlist'});
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
        },
        gunaDanTgl() {
          return `${this.formdata.guna_id}|${this.formdata.tanggal}`;
        },

    },
    watch:{
        gunaDanTgl(newVal) {
            const [newGuna, newTgl] = newVal.split('|');
            let guna_id = newGuna
            let tanggal = newTgl
            if(tanggal){
                this.$http.post('/api/referensi',{
                    jenis : 'sjsr',
                    guna_id: guna_id,
                    tglJurnal : tanggal
                }).then(response => {
                    this.formdata.referensi = response.data
                }).catch(error=>{
                    console.log(error)
                })
                this.getDetQuot(tanggal)
            }
            
        },
        
    },
    mounted: function () {
        this.getAllData();
    },
  }
</script>
<style lang="stylus">
.cetak
  height: 90vh
</style>