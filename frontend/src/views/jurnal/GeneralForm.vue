<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">{{title}} {{this.$route.params.ref}}</h3>
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
            v-model="datetrans"
            format="DD-MM-YYYY"
            type="date"
            placeholder="Pilih Tgl" input-class="vs-input--input"
            :disabled-date="disabledBeforeYear">
            </date-picker>
            </div>
            </div>
          </vs-col>
          <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
            <vs-input label="REFERENSI" class="w-100 mt-4" v-model="formdata.referensi"/>  
          </vs-col>
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4" v-if="jenisJurnal==4">  
            <vs-input label="NO CEK" class="w-100 mt-4" v-model="formdata.no_cheq"/> 
          </vs-col>
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4" v-else>  
            <vs-select autocomplete class="w-100 mt-4" ref="rekanan" label="REKANAN" v-model="formdata.rekanan">
              <vs-select-item
                :key="index"
                :value="item.kode"
                :text="item.nama"
                v-for="(item,index) in optrekanan"
              />
            </vs-select>
          </vs-col>
          <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
            <vs-select class="w-100 mt-4" ref="unit" label="UNIT" v-model="formdata.unit">
              <vs-select-item
                :key="index"
                :value="item.unit"
                :text="item.nm_unit"
                v-for="(item,index) in optunit"
              />
            </vs-select>
          </vs-col>
          </vs-row>
          <vs-row>
             <vs-col vs-lg="12" vs-xs="12" vs-sm="4"> 
              <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="formdata.uraian" class="mt-4 w-100" />
             </vs-col>
          </vs-row>
          
                
          <vs-row>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-input disabled label="ANGGARAN" class="w-100 mt-4" />  
            </vs-col>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-input disabled label="REALISASI" class="w-100 mt-4" />  
            </vs-col>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-input disabled label="SISA" class="w-100 mt-4" />  
            </vs-col>
          </vs-row>

          <vs-row>
            <vs-col vs-lg="7" vs-xs="12" vs-sm="4">  
                <vs-select autocomplete  class="w-100 mt-4" label="PERKIRAAN" ref="perkiraan" v-model="perkiraan" 
                danger-text="Kode Perkiraan tidak sesuai"
                :danger="errorPerkiraan">
                <vs-select-item
                    :key="index"
                    :value="item.kode_perk"
                    :text="parseNama(item.nama)"
                    v-for="(item,index) in optperkiraan"
                />
                </vs-select>
            </vs-col>
            <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
               <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                <label class="vs-input--label" >&nbsp;</label>
                <div class="vs-con-input"> 
                    <vs-switch v-model="dk" ref="dk">
                        <span slot="on">Debet</span>
                        <span slot="off">Kredit</span>
                    </vs-switch>
                </div>
               </div>
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="NILAI TRANSAKSI" class="w-100 mt-4" ref="transval" v-model="transval" v-currency="options"/>  
            </vs-col>
            <vs-col vs-lg="1" vs-xs="12" vs-sm="1">  
                <div class="vs-component vs-con-input-label vs-input w-100 mt-3 vs-input-primary">  
                <label class="vs-input--label" >&nbsp;</label>
                <div class="vs-con-input">
                <vs-button color="success" type="filled" @click="addTrans">OK</vs-button>
                </div>
                </div>
            </vs-col>
          </vs-row>
         
        </div>
        <hr class="custom-hr" />
        <div>            
            <vs-table  :data="formdata.datatrans" class="text-nowrap">
                 <template slot="header">
                <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                    <h5>JURNAL TRANSAKSI</h5>
                </vs-col>
                <vs-col vs-type="flex" vs-justify="center" vs-align="center" vs-lg="5" vs-sm="4" vs-xs="12">
                    <h5>TOTAL DEBET : <span style="color:red">{{sumDebet}}</span></h5> 
                </vs-col>
                <vs-col vs-type="flex" vs-justify="center" vs-align="center" vs-lg="5" vs-sm="4" vs-xs="12">
                    <h5>TOTAL KREDIT : <span style="color:red">{{sumKredit}}</span></h5> 
                </vs-col>


                </template>
                <template slot="thead">
                    <vs-th>Kode Perkiraan</vs-th>
                    <vs-th>Debet</vs-th>
                    <vs-th>Kredit</vs-th>
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ data[indextr].kode }}&nbsp;&nbsp;&nbsp;{{getNamaperk(data[indextr].kode)}}</vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].debet" v-currency="options"/>
                    </vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].kredit" v-currency="options"/>
                    </vs-td>
                    <vs-td>
                        <div class="d-flex align-items-center">
                        <vs-button radius color="danger" :id="data[indextr].id" size="small" type="gradient" icon="delete_outline" @click="removeTrans(data[indextr].id)"></vs-button>
                        </div>
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
  

  export default {
    components: {
      DatePicker,
    },
    directives: {
        currency: CurrencyDirective
    },
    props: {
        jenisJurnal : Number,
        jurnalUrl : String,
        title : String,
        routeBack : String
    },
    data: function () {
      return {
        // eslint-disable-next-line
        locale: 'de',
        currency: null,
        datetrans: '',
        formdata: {
            referensi:'',
            rekanan:'',
            unit:process.env.VUE_APP_DEFAULT_UNIT,
            uraian:'',
            jenis:this.jenisJurnal,
            tanggal:'',
            opr:'',
            voucher:'-',
            no_cheq:'',
            datatrans:[],
            isEdit: false,
            prevReferensi:''
        },
        optunit:[],
        perkiraan:'',
        optperkiraan:[],
        optrekanan:[],
        dk:true,
        transval:'',
        errors:[],
        errorPerkiraan:false,
        numSumDebet:0,
        numSumKredit:0,
        idtrans:0,
        bayarUrl:'/api/bayardata'
        
        //formatTgl:''
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
            this.$http.get('/api/satker').then(response => {
                //console.log(response.data);
                this.optunit = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/perkiraan').then(response => {
                //console.log(response.data);
                this.optperkiraan = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/rekanan').then(response => {
                //console.log(response.data);
                this.optrekanan = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.jurnalUrl+"/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    for(const key of keys){
                        let subkeys = Object.keys(response.data[key])
                        subkeys.forEach(item => {
                            if (this.formdata.hasOwnProperty(item))
                                this.formdata[item] = response.data[key][item]
                            if(item == 'debet' || item == 'kredit'){
                                this.formdata.datatrans[key][item] =  new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(response.data[key][item])
                            }
                            if(item == 'id')
                                this.idtrans = response.data[key][item]
                            if(item == 'tanggal'){
                                let tgldata = response.data[key][item]
                                let arrtgl = tgldata.split("-")
                                let d = new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2])
                                console.log('--pengolahan tanggal--')
                                console.log(d)
                                let dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
                                let [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
                                //var tgltrans = `${day}-${month}-${year}`
                                this.datetrans = new Date(`${year},${month},${day}`)
                            }  
                            if(item == 'referensi')
                                this.formdata.prevReferensi = response.data[key][item]
                            if(item == 'voucher')
                                this.formdata.voucher = response.data[key][item]    
                        })
                    }
                }).catch(error=>{
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                });
                
                this.$http.get(this.bayarUrl+"/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    this.formdata.no_cheq = response.data.no_cheq
                }).catch(error=>{
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
        getNamaperk: function(kode){
            let nama_perk = this.optperkiraan.find(x => x.kode_perk==kode).nama;
            let parts = nama_perk.split('+');
            return parts[1];
        },
        addTrans: function(){
            if(this.perkiraan!=''){
                let idjurnal = ++this.idtrans
                let data = {};
                data.kode = this.perkiraan
                data.referensi = this.formdata.referensi
                data.unit = this.formdata.unit
                data.rekanan = this.formdata.rekanan
                data.uraian = this.formdata.uraian
                data.jenis = this.formdata.jenis
                data.tanggal = this.formdata.tanggal
                data.opr = this.formdata.opr
                data.id = idjurnal
                if(this.dk){
                    data.debet = this.transval
                    data.kredit = 0
                }   
                else{
                    data.kredit = this.transval
                    data.debet = 0
                }
                this.formdata.datatrans.push(data)
                this.clearTrans()
            } 
            else{
                this.errorPerkiraan = true
                //this.setFocusOnInput('perkiraan');
            }            
        },
        removeTrans: function(id) {
            let index = this.formdata.datatrans.findIndex(x => x.id == id)
            this.formdata.datatrans.splice(index,1)
        },
        clearTrans: function(){
            this.transval = "";
            this.setFocusOnInput('uraian');
            this.errorPerkiraan = false;
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
        hitungSelisih: function(){
            let jumlahDebet = this.numberValue(this.numSumDebet)
            let jumlahKredit = this.numberValue(this.numSumKredit)
            let selisih = 0;
            if(this.dk){ //debet transaksi
                if(jumlahDebet < jumlahKredit)
                selisih = jumlahKredit - jumlahDebet
            } 
            else{
                if(jumlahKredit < jumlahDebet)
                selisih = jumlahDebet - jumlahKredit
                //setValue(this.$refs.transval, selisih)
            }
            this.transval = new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(selisih)
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            if(this.numSumDebet != this.numSumKredit){
                this.errors.push("Transaksi Debet dan Kredit tidak sama..")
            }
            if(this.formdata.tanggal == ''){
                this.errors.push("Tanggal belum terisi...")
            }
            if(this.formdata.referensi == ''){
                this.errors.push("Referensi belum terisi...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post(this.jurnalUrl,{
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
            this.$router.push({name : this.routeBack});
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
        sumDebet() {
            let initialValue = 0
            let sum = this.formdata.datatrans.reduce(
                (accumulator, currentValue) => Number(accumulator) + Number(this.numberValue(currentValue.debet))
                , initialValue)
            this.numSumDebet = new Intl.NumberFormat('de-DE').format(sum)       
            return new Intl.NumberFormat('de-DE',{style:'currency',currency:'IDR'}).format(sum)
        },
        sumKredit() {
            let initialValue = 0
            let sum = this.formdata.datatrans.reduce(
                (accumulator, currentValue) => Number(accumulator) + Number(this.numberValue(currentValue.kredit))
                , initialValue)
            this.numSumKredit = new Intl.NumberFormat('de-DE').format(sum)    
            return new Intl.NumberFormat('de-DE',{style:'currency',currency:'IDR'}).format(sum)
        }
    },
    watch:{
        perkiraan: function(){
            this.setFocusOnInput('dk');
            this.hitungSelisih();
        },
        'formdata.rekanan': function(){
            this.setFocusOnInput('unit')
        },
        'formdata.unit': function(){
            this.setFocusOnInput('uraian')
        },
        dk: function(){
            this.hitungSelisih();
            this.setFocusOnInput('transval');
        },
        datetrans: function(){
            let d = new Date(this.datetrans);
            const dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
            const [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
            var tgltrans = `${year }-${month}-${day}`
            this.formdata.tanggal = tgltrans
            if(this.formdata.isEdit == false){
                this.$http.post('/api/referensi',{
                    jenis : this.formdata.jenis,
                    tglJurnal : tgltrans
                }).then(response => {
                    this.formdata.referensi = response.data
                }).catch(error=>{
                    console.log(error)
                })
            }
        }
    },
    mounted: function () {
        this.getAllData();
    },
  }
</script>