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
          <vs-col vs-lg="7" vs-xs="12" vs-sm="4">  
            <vs-select autocomplete class="w-100 mt-4" ref="rekanan" label="REKANAN" v-model="formdata.rekanan">
              <vs-select-item
                :key="index"
                :value="item.kode"
                :text="item.nama"
                v-for="(item,index) in optrekanan"
              />
            </vs-select>
          </vs-col>
          
          </vs-row>
          <vs-row>
            <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                <vs-input label="PO NO" class="w-100 mt-4" v-model="formdata.po_no"/>  
            </vs-col>
            <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                <vs-input label="NOMOR SURAT JALAN" class="w-100 mt-4" v-model="formdata.sj_no"/>  
            </vs-col>
          </vs-row>
          
          <div v-if="jenisJurnal=='bpb'">
            <vs-row>
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-select class="w-100 mt-4" label="Kelompok Persediaan" ref="kelompok" v-model="kelompok" 
                    danger-text="Kode Perkiraan tidak sesuai"
                    :danger="errorPerkiraan">
                    <vs-select-item
                        :key="index"
                        :value="item.kode_perk"
                        :text="parseNama(item.nama)"
                        v-for="(item,index) in optitem"
                    />
                    </vs-select>
                </vs-col>
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-select  class="w-100 mt-4" label="Jenis Persediaan" ref="jenis" v-model="jenis" 
                    danger-text="Kode Perkiraan tidak sesuai"
                    :danger="errorPerkiraan">
                    <vs-select-item
                        :key="index"
                        :value="item.kode_perk"
                        :text="parseNama(item.nama)"
                        v-for="(item,index) in optjenis"
                    />
                    </vs-select>
                </vs-col>
                
            </vs-row>

            <vs-row>
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-select autocomplete class="w-100 mt-1" label="Kode Barang - Nama Barang" ref="barang" v-model="barang" 
                    danger-text="Kode Perkiraan tidak sesuai"
                    :danger="errorBarang">
                    <vs-select-item
                        :key="index"
                        :value="item.kode_barang"
                        :text="parseNama(item.nama)"
                        v-for="(item,index) in optbarang"
                    />
                    </vs-select>
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="Jumlah" class="w-100 mt-1" v-model="qty" v-currency="options" :danger="errorQty" danger-text="Qty invalid"/> 
                </vs-col>
                <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                    <vs-input label="Satuan" class="w-100 mt-1" v-model="satuan" disabled/> 
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="Catatan Kondisi Barang" class="w-100 mt-1" v-model="catatan" /> 
                </vs-col>
                <vs-col vs-lg="1" vs-xs="12" vs-sm="1">  
                    <div class="vs-component vs-con-input-label vs-input w-100 mt-0 vs-input-primary">  
                    <label class="vs-input--label" >&nbsp;</label>
                    <div class="vs-con-input">
                    <vs-button color="success" type="filled" @click="addTrans">OK</vs-button>
                    </div>
                    </div>
                </vs-col>
            </vs-row>
          </div>
          <vs-row v-else>
            <vs-col vs-lg="12" vs-xs="12" vs-sm="4">  
                <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="formdata.uraian" class="mt-4 w-100" />
            </vs-col>
          </vs-row>
          
        </div>
        <hr class="custom-hr" />
        <div>            
            <vs-table  :data="formdata.datatrans" class="text-nowrap">
                <template slot="header">
                <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                    <h5>DATA PENERIMAAN BARANG</h5>
                </vs-col>
                

                </template>
                <template slot="thead">
                    <vs-th>Kode Barang - Nama Barang</vs-th>
                    <vs-th>Qty</vs-th>
                    <vs-th>Satuan</vs-th>
                    <vs-th>Catatan</vs-th>
                    <vs-th v-if="jenisJurnal=='bpbposting'">Harga</vs-th>
                    <vs-th v-if="jenisJurnal=='bpbposting'">PPN</vs-th>
                    <vs-th v-if="jenisJurnal=='bpbposting'">Duty Cost</vs-th>
                    <vs-th v-if="jenisJurnal=='bpbposting'">Freight Cost</vs-th>
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].qty_pesan" style="width: 100px;" v-currency="options"/>
                    </vs-td>
                    
                    <vs-td vs-align="right">{{ data[indextr].satuan }}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].catatan }}</vs-td>
                    <vs-td vs-align="right" v-if="jenisJurnal=='bpbposting'">
                        <vs-input class="w-1" v-model="data[indextr].harga" v-currency="options" style="width: 200px;"/>
                    </vs-td>
                    <vs-td vs-align="right" v-if="jenisJurnal=='bpbposting'">
                        <vs-input class="w-1" v-model="data[indextr].ppn" v-currency="options" style="width: 100px;"/>
                    </vs-td>
                    <vs-td vs-align="right" v-if="jenisJurnal=='bpbposting'">
                        <vs-input class="w-1" v-model="data[indextr].duty_cost" v-currency="options" style="width: 100px;"/>
                    </vs-td>
                    <vs-td vs-align="right" v-if="jenisJurnal=='bpbposting'">
                        <vs-input class="w-1" v-model="data[indextr].freight_cost" v-currency="options" style="width: 100px;"/>
                    </vs-td>
                    <vs-td v-if="jenisJurnal=='bpb'">
                        <div class="d-flex align-items-center">
                        <vs-button radius color="danger" :id="data[indextr].id" size="small" type="gradient" icon="delete_outline" @click="removeTrans(data[indextr].id)"></vs-button>
                        </div>
                    </vs-td>
                    </vs-tr>
                </template>
            </vs-table>
        </div>
        <br/>
        <div v-if="jenisJurnal=='bpbpostings'">            
            <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                <h5>BIAYA-BIAYA LAIN</h5>
            </vs-col>

            <vs-row>
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-select class="w-100 mt-1" label="Kode Biaya - Nama Biaya" ref="barang" v-model="barang" 
                    danger-text="Kode Perkiraan tidak sesuai"
                    :danger="errorBarang">
                    <vs-select-item
                        :key="index"
                        :value="item.kode_barang"
                        :text="parseNama(item.nama)"
                        v-for="(item,index) in optbarang"
                    />
                    </vs-select>
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="Jumlah" class="w-100 mt-1" v-model="qty" v-currency="options" :danger="errorQty" danger-text="Qty invalid"/> 
                </vs-col>
                <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                    <vs-input label="Satuan" class="w-100 mt-1" v-model="satuan" disabled/> 
                </vs-col>
                
                <vs-col vs-lg="1" vs-xs="12" vs-sm="1">  
                    <div class="vs-component vs-con-input-label vs-input w-100 mt-0 vs-input-primary">  
                    <label class="vs-input--label" >&nbsp;</label>
                    <div class="vs-con-input">
                    <vs-button color="success" type="filled" @click="addTrans">OK</vs-button>
                    </div>
                    </div>
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
    props: {
        jenisJurnal : String,
        jurnalUrl : String,
        title : String,
        routeBack : String
    },
    data: function () {
      return {
        // eslint-disable-next-line
        locale: 'us',
        currency: null,
        datetrans: '',
        formdata: {
            referensi:'',
            rekanan:'',
            unit:'',
            uraian:'',
            jenis:this.jenisJurnal,
            tanggal:'',
            opr:'',
            datatrans:[],
            ppn:'0',
            duty_cost:'0',
            freight_cost:'0',
            isEdit: false,
            prevReferensi:'',
            po_no:'',
            sj_no:''
        },
        optitem:[],
        optjenis:[],
        optbarang:[],
        optrekanan:[],
        kelompok:'',
        jenis:'',
        barang:'',
        satuan:'',
        qty:'',
        catatan:'',
        dk:true,
        transval:'',
        errors:[],
        errorPerkiraan:false,
        errorBarang:false,
        errorQty:false,
        numSumDebet:0,
        numSumKredit:0,
        idtrans:0,
        
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
            this.$http.get('/api/itemlist').then(response => {
                //console.log(response.data);
                this.optitem = response.data
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
                            if(item == 'bpbd'){
                                console.log('bpbd')
                                console.log(response.data[key][item].flat())
                                let objBpb = response.data[key][item]
                                /* var self = this
                                Object.keys(objBpb).forEach(key => {
                                    //console.log(key, objBpb[key]);
                                    self.formdata.datatrans = [...objBpb[key],objBpb[key].itembarang]
                                }); */

                                this.formdata.datatrans = [...response.data[key][item]]
                            }
                            if(item == 'datatrans'){
                                let arr = Object.values(response.data[key][item]);
                                console.log(arr)
                                let maxId = _.maxBy(arr, function(o) { return o.id; })
                                console.log(maxId)
                                //let min = Math.min(...arr);
                                //let max = Math.max(...arr);

                                this.idtrans = maxId.id
                            }
                                

                            if(item == 'bpb_no'){
                                this.formdata.referensi = response.data[key][item]   
                                this.formdata.prevReferensi = this.formdata.referensi
                            }
                            if(item == 'bpb_tgl'){
                                let tgldata = response.data[key][item]
                                let arrtgl = tgldata.split("-")
                                let d = new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2])
                                let dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
                                let [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
                                //var tgltrans = `${day}-${month}-${year}`
                                this.datetrans = new Date(`${year},${month},${day}`)
                            }  
                            if(item == 'referensi')
                                this.formdata.prevReferensi = response.data[key][item]
                        })
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
       
        addTrans: function(){
            if(this.qty == ''){
                this.errorQty = true
            }
            else{
                this.errorQty = false
                if(this.barang!=''){
                    var self = this
                    let idjurnal = ++this.idtrans
                    let dataBarang = {}
                    let obj_barang = this.optbarang.find(x => x.kode_barang==self.barang)
                    let nama_barang = obj_barang.nama
                    let parts = nama_barang.split('+')
                    dataBarang.kode_barang = this.barang
                    dataBarang.nama_barang = parts[1]
                    dataBarang.item_id = obj_barang.item_id
                    dataBarang.qty_pesan = this.qty
                    dataBarang.satuan = this.satuan
                    dataBarang.catatan = this.catatan
                    dataBarang.referensi = this.formdata.referensi
                    dataBarang.rekanan = this.formdata.rekanan
                    dataBarang.tanggal = this.formdata.tanggal
                    dataBarang.opr = this.formdata.opr
                    dataBarang.id = idjurnal
                    this.formdata.datatrans.push(dataBarang)
                    this.clearTrans()
                } 
                else{
                    this.errorBarang = true
                    //this.setFocusOnInput('perkiraan');
                }   
            }
                     
        },
        removeTrans: function(id) {
            let index = this.formdata.datatrans.findIndex(x => x.id == id)
            this.formdata.datatrans.splice(index,1)
        },
        clearTrans: function(){
            this.qty = "";
            this.catatan = "",
            //this.barang = "";
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
            return parseCurrency(value.toString(), this.options)
        },
        hitungSelisih: function(){
            let jumlahDebet = this.numSumDebet
            let jumlahKredit = this.numSumKredit
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
            this.transval = new Intl.NumberFormat('us-US',{notation: 'standard',}).format(selisih)
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
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
        }
    },
    watch:{
        kelompok: function(){
            this.jenis = ''
            var self = this
            let arrJenis = this.optitem.filter(item => item.kode_perk == self.kelompok)
            this.optjenis = arrJenis.map(item => item.subperkiraan).flat()
            
        },
        jenis: function(){
            this.barang = ''
            var self = this
            let arrBarang = this.optjenis.filter(item => item.kode_perk == self.jenis)
            this.optbarang = arrBarang.map(item => item.itembarang).flat()
            
        },
        barang: function(){
            this.satuan = ''
            var self = this
            let objBarang = this.optbarang.find(x => x.kode_barang == self.barang);
            console.log(objBarang)
            this.satuan = objBarang.satuan
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