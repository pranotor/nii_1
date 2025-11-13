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
        <h3 class="card-title mb-0">Surat Jalan {{this.$route.params.ref}}</h3>
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
                    <vs-th>Nama 2</vs-th>
                    <vs-th>Satuan</vs-th>
                    <vs-th>Jumlah Blm Terkirim</vs-th>
                    <vs-th>Jumlah Dikirim</vs-th>
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].nama2 }}</vs-td>
                    <vs-td vs-align="right">{{ data[indextr].satuan }}</vs-td>
                    <vs-td vs-align="right">
                        <vs-input v-model="data[indextr].qty_pesan" v-currency="options" disabled/>
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
            cust_id:'',
            jenis:this.jenisForm,
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
        optcustomer:[],
        
        optpenggunaan:[],
        optpegawai:[],
        optitem:[],
        optjenis:[],
        optbarang:[],
        perkiraan:'',
        optperkiraan:[],
        optunit:[],
        bppjurnal:[],
        kelompok:'',
        jenis:'',
        barang:'',
        satuan:'',
        harga:'',
        discount:'0',
        stock:'',
        qty_pesan:'',
        qty_terima:'',
        nama2:'',
        size:'',
        transval:'',
        errors:[],
        errorPerkiraan:false,
        errorBarang:false,
        errorQty:false,
        idtrans:0,
        numSumDebet:0,
        numSumKredit:0,
        popupPdf: false,
        pdfsrc: '',
        
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

            this.$http.get('/api/perkiraan').then(response => {
                //console.log(response.data);
                this.optperkiraan = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/customer').then(response => {
                //console.log(response.data);
                this.optcustomer = response.data;
            }).catch(error=>{
                //console.log(error);
            });

        

            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get("/api/quotation/"+this.$route.params.ref).then(response => {
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
                this.$http.get('/api/bppjurnal/'+this.$route.params.ref).then(response => {
                    //console.log(response.data);
                    this.bppjurnal = response.data;
                    this.formdata.datajurnal = response.data;
                }).catch(error=>{
                    //console.log(error);
                })
            }
            //edit mode init data

            this.formdata.opr = this.$store.getters.getUserName
        },
        parseNama: function(nama){
            var parts = nama.split('+');
            return parts[0].replace(/=/g, '\u00a0') + parts[1];        
        },
        sisaStock: function(kode_perk,kode_barang) {
            let jenis = kode_perk.substr(0,5)
            console.log(jenis)
            let arrBarang = this.optitem.filter(item => item.kode_perk == jenis)
            console.log(arrBarang)
            let optbarang = arrBarang.map(item => item.subperkiraan).flat()
            console.log(optbarang)
            let obj_items = optbarang.find(x => x.kode_perk==kode_perk)
            console.log(obj_items)
            let obj_barang = obj_items.itembarang.find(x => x.kode_barang==kode_barang)
            console.log(obj_barang)
            let stok_barang = Number(obj_barang.stock.sisa)
            return stok_barang
        },
        cetakBpp: function(referensi){
            this.pdfsrc = this.$urlbackend+'/api/bpppdf/'+referensi
            this.popupPdf = true
        },
        addTrans: function(){
            this.errorQty = false
            if(this.barang!='' && this.qty_pesan!=''){
                //cek permintaan terhadap stock
                let minta = Number(this.qty_terima)
                let sisa = Number(this.stock)
                if(sisa - minta >=0){
                    var self = this
                    let idjurnal = ++this.idtrans
                    let dataBarang = {}
                    let obj_barang = this.optbarang.find(x => x.kode_barang==self.barang)
                    console.log('--pilih barang--')
                    console.log(obj_barang)
                    let stok_barang = Number(obj_barang.stock.sisa)
                    stok_barang = stok_barang - Number(this.qty_terima)
                    obj_barang.stock.sisa = stok_barang
                    console.log('stok barang')
                    console.log(stok_barang)
                    let nama_barang = obj_barang.nama
                    let parts = nama_barang.split('+')
                    dataBarang.kode_barang = this.barang
                    dataBarang.kode_perk = obj_barang.kode_perk
                    dataBarang.nama_barang = parts[1]
                    dataBarang.item_id = obj_barang.item_id
                    dataBarang.satuan = this.satuan
                    dataBarang.nama2 = this.nama2
                    dataBarang.size = this.size
                    dataBarang.harga = this.harga
                    dataBarang.discount = this.discount
                    dataBarang.referensi = this.formdata.referensi
                    dataBarang.rekanan = this.formdata.rekanan
                    dataBarang.tanggal = this.formdata.tanggal
                    dataBarang.opr = this.formdata.opr
                    dataBarang.qty_terima = this.qty_terima
                    dataBarang.qty_pesan = this.qty_pesan
                    //dataBarang.qty_sisa = stok_barang
                    dataBarang.id = idjurnal
                    this.formdata.datatrans.push(dataBarang)
                    this.clearTrans()
                }
                else{
                     this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'Stock Tidak cukup....',
                        'accept-text': 'Ya saya mengerti..'
                    })
                }
                
            } 
            else{
                this.$vs.dialog({
                        color:"danger",
                        title: 'Error',
                        text: 'Pastikan Kode Barang, Jumlah permintaan dan Jumlah dikeluarkan terisi dengan benar..',
                        'accept-text': 'Ya saya mengerti..'
                })
            }   
                     
        },
        addTransJurnal: function(){
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
                this.formdata.datajurnal.push(data)
                this.clearTrans()
            } 
            else{
                this.errorPerkiraan = true
                //this.setFocusOnInput('perkiraan');
            }            
        },
        getNamaperk: function(kode){
            let nama_perk = this.optperkiraan.find(x => x.kode_perk==kode).nama;
            let parts = nama_perk.split('+');
            return parts[1];
        },
        acceptAlert: function(){
            this.$vs.notify({
                color:this.colorAlert,
                title:'Accept Selected',
                text:'Lorem ipsum dolor sit amet, consectetur'
            })
        },
        removeTrans: function(id) {
            let index = this.formdata.datatrans.findIndex(x => x.id == id)
            let kode_perk = this.formdata.datatrans[index].kode_perk
            let jenis = kode_perk.substr(0,5)
            console.log(jenis)
            let kode_barang = this.formdata.datatrans[index].kode_barang
            let qty_terima = this.formdata.datatrans[index].qty_terima
            let arrBarang = this.optitem.filter(item => item.kode_perk == jenis)
            console.log(arrBarang)
            let optbarang = arrBarang.map(item => item.subperkiraan).flat()
            console.log(optbarang)
            let obj_items = optbarang.find(x => x.kode_perk==kode_perk)
            console.log(obj_items)
            let obj_barang = obj_items.itembarang.find(x => x.kode_barang=kode_barang)
            console.log(obj_barang)
            let stok_barang = Number(obj_barang.stock.sisa)
            stok_barang = stok_barang + Number(qty_terima)
            obj_barang.stock.sisa = stok_barang
            this.formdata.datatrans.splice(index,1)
        },
        removeTransJurnal: function(id) {
            console.log('hapus' + id)
            let index = this.formdata.datajurnal.findIndex(x => x.id == id)
            this.formdata.datajurnal.splice(index,1)
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
            this.transval = new Intl.NumberFormat('en-EN',{notation: 'standard',}).format(selisih)
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

            if(this.formdata.pemohon == ''){
                this.errors.push("Kolom diminta oleh belum terisi...")
            }

            if(this.formdata.disetujui == ''){
                this.errors.push("Kolom disetujui oleh belum terisi...")
            }

            if(this.formdata.mengeluarkan == ''){
                this.errors.push("Kolom dikeluarkan belum terisi...")
            }

            if(this.formdata.penerima == ''){
                this.errors.push("Kolom penerima barang belum terisi...")
            }

            if(this.formdata.jenis=='bppposting'){
                if(this.numSumDebet != this.numSumKredit){
                    this.errors.push("Transaksi Debet dan Kredit tidak sama..")
                }
            }
            

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
            this.$router.push({name : 'sjlist'});
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
            let sum = this.formdata.datajurnal.reduce(
                (accumulator, currentValue) => accumulator + this.numberValue(currentValue.debet)
                , initialValue)
            this.numSumDebet = sum       
            return new Intl.NumberFormat('en-EN',{style:'currency',currency:'IDR'}).format(sum)
        },
        sumKredit() {
            let initialValue = 0
            let sum = this.formdata.datajurnal.reduce(
                (accumulator, currentValue) => accumulator + this.numberValue(currentValue.kredit)
                , initialValue)
            this.numSumKredit = sum    
            return new Intl.NumberFormat('en-EN',{style:'currency',currency:'IDR'}).format(sum)
        },
        gunaDanTgl() {
          return `${this.formdata.guna_id}|${this.formdata.tanggal}`;
        },

    },
    watch:{
        gunaDanTgl(newVal) {
          const [newGuna, newTgl] = newVal.split('|');
          let guna_id = this.numberValue(newGuna)
          let tanggal = newTgl
        this.$http.post('/api/referensi',{
            jenis : 'sj',
            guna_id: guna_id,
            tglJurnal : tanggal
        }).then(response => {
            this.formdata.referensi = response.data
        }).catch(error=>{
            console.log(error)
        })
        },
        kelompok: function(){
            this.jenis = ''
            var self = this
            let arrJenis = this.optitem.filter(item => item.kode_perk == self.kelompok)
            //console.log(arrJenis)
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
            if (self.barang != ''){
                let objBarang = this.optbarang.find(x => x.kode_barang == self.barang);
                this.satuan = objBarang.satuan
                this.stock = objBarang.stock.sisa
            }
        },
        perkiraan: function(){
            this.setFocusOnInput('dk');
            this.hitungSelisih();
        },
        dk: function(){
            this.hitungSelisih();
            this.setFocusOnInput('transval');
        },
        'formdata.pemohon_nik': function(){
            let nik = this.formdata.pemohon_nik
            let jabatan = this.optpegawai.filter(item => item.nik==nik).map(item => item.jabatan).join('')
            let nama = this.optpegawai.filter(item => item.nik==nik).map(item => item.nama).join('')
            this.formdata.pemohon_jabatan = jabatan
            this.formdata.pemohon = nama
        },
        'formdata.disetujui_nik': function(){
            let nik = this.formdata.disetujui_nik
            let jabatan = this.optpegawai.filter(item => item.nik==nik).map(item => item.jabatan).join('')
            let nama = this.optpegawai.filter(item => item.nik==nik).map(item => item.nama).join('')
            this.formdata.disetujui_jabatan = jabatan
            this.formdata.disetujui = nama
        },
        'formdata.mengeluarkan_nik': function(){
            let nik = this.formdata.mengeluarkan_nik
            let jabatan = this.optpegawai.filter(item => item.nik==nik).map(item => item.jabatan).join('')
            let nama = this.optpegawai.filter(item => item.nik==nik).map(item => item.nama).join('')
            this.formdata.mengeluarkan_jabatan = jabatan
            this.formdata.mengeluarkan = nama
        },
        'formdata.penerima_nik': function(){
            let nik = this.formdata.penerima_nik
            let jabatan = this.optpegawai.filter(item => item.nik==nik).map(item => item.jabatan).join('')
            let nama = this.optpegawai.filter(item => item.nik==nik).map(item => item.nama).join('')
            this.formdata.penerima_jabatan = jabatan
            this.formdata.penerima = nama
        }
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