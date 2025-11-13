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
          <vs-row v-if="formdata.jenis!='sr'">
            <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
                <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                <label class="vs-input--label" >&nbsp;</label>
                <div class="vs-con-input"> 
                    <vs-switch v-model="formdata.p" ref="p">
                        <span slot="on">P</span>
                        <span slot="off">N</span>
                    </vs-switch>
                </div>
               </div>
            </vs-col>
          </vs-row>
          <vs-row>
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
            <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
            <label class="vs-input--label" >DATE</label>
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
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
            <vs-input label="DOC NO" class="w-100 mt-4" v-model="formdata.referensi"/>  
          </vs-col>
          <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-select autocomplete class="w-100 mt-4" ref="rekanan" label="CUSTOMER" v-model="formdata.cust_id">
                <vs-select-item
                    :key="index"
                    :value="item.id"
                    :text="item.nick"
                    v-for="(item,index) in optcustomer"
                />
                </vs-select>
            </vs-col>  
          <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-select autocomplete class="w-100 mt-4" ref="rekanan" label="SALES" v-model="formdata.sales_id">
                <vs-select-item
                    :key="index"
                    :value="item.sales_id"
                    :text="item.nama"
                    v-for="(item,index) in optsales"
                />
                </vs-select>
            </vs-col>  
          </vs-row>
          <vs-row v-if="formdata.jenis!='sr'">
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4" >  
            <vs-input label="PO NO (Customer)" class="w-100 mt-4" v-model="formdata.po_no" v-on:input="debounceInput"/>  
          </vs-col>
          <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
            <vs-input label="LEAD TIME (DAYS)" class="w-100 mt-4" v-model="formdata.leadtime"/>  
          </vs-col>
          <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
            <vs-input label="VALIDITY (DAYS)" class="w-100 mt-4" v-model="formdata.validity"/>  
          </vs-col>
          <vs-col vs-lg="5" vs-xs="12" vs-sm="4">  
            <vs-input label="PAYMENT" class="w-20 mt-4" v-model="formdata.payment"/> Days after received the goods  
          </vs-col>
         
          </vs-row>
          <br/>
          <vs-row>
            <vs-col>
                <vs-textarea label="CATATAN" ref="uraian" v-model.lazy="formdata.notes" />
            </vs-col>
          </vs-row>
          
          <vs-divider>
                Item Penjualan
          </vs-divider>
          <div>
            <vs-row>
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-select   class="w-100 mt-4" label="Kelompok Persediaan" ref="kelompok" v-model="kelompok" 
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
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <vs-input label="Kode 2" class="w-100 mt-1" v-model="kode2" /> 
                </vs-col>
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <vs-input label="Nama 2" class="w-100 mt-1" v-model="nama2" /> 
                </vs-col>
            </vs-row>
            <vs-row>
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <vs-input label="Stok" class="w-100 mt-1" v-model="stock" v-currency="options" disabled/> 
                </vs-col>
                <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                    <vs-input label="Satuan" class="w-100 mt-1" v-model="satuan"/> 
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="HARGA SATUAN" class="w-100 mt-1" v-model="harga" v-currency="options"/> 
                </vs-col>
                <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                    <vs-input label="DISC (%)" class="w-100 mt-1" v-model="discount" v-currency="options"/> 
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="DISC (Rp)" class="w-100 mt-1" v-model="discount_rp" v-currency="options"/> 
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4">  
                    <vs-input label="JUMLAH PESAN" class="w-100 mt-1" v-model="qty_pesan" v-currency="options"/> 
                </vs-col>
                <vs-col vs-lg="2" vs-xs="12" vs-sm="4" v-if="seen">  
                    <vs-input label="JUMLAH DIKELUARKAN" class="w-100 mt-1" v-model="qty_terima" v-currency="options"/> 
                </vs-col>
                
            </vs-row>
            <vs-row>
                <vs-col vs-lg="10" vs-xs="12" vs-sm="4"> 
                    <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="size" class="mt-4 w-100" />
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
                    <vs-th></vs-th>
                    <vs-th>Kode Barang - Nama Barang</vs-th>
                    <vs-th>Nama 2</vs-th>
                    <vs-th>Satuan</vs-th>
                    <vs-th>Harga</vs-th>
                    <vs-th>Discount</vs-th>
                    <vs-th>Jumlah Pesan</vs-th>
                    
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                        <vs-td>
                            <div class="d-flex align-items-center">
                            <vs-button radius color="danger" :id="data[indextr].id" size="small" type="gradient" icon="delete_outline" @click="removeTrans(data[indextr].id)"></vs-button>
                            </div>
                        </vs-td>
                        <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                        <vs-td vs-align="right"><vs-input v-model="data[indextr].nama2" /></vs-td>
                        <vs-td vs-align="right"><vs-input v-model="data[indextr].satuan" /></vs-td>
                        <vs-td vs-align="right">
                            <vs-input v-model="data[indextr].harga" v-currency="options"/>
                        </vs-td>
                        <vs-td vs-align="right">
                            <vs-input v-model="data[indextr].discount" v-currency="options"/>
                        </vs-td>
                        <vs-td vs-align="right">
                            <vs-input v-model="data[indextr].qty_pesan" v-currency="options"/>
                        </vs-td>
                        <vs-td vs-align="right">
                            
                            <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="data[indextr].size" width="300px" />
                        </vs-td>
                    
                    
                        
                        <vs-td v-if="jenisJurnal=='bpp'">
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
            p:(this.jenisForm == 'sr') ? false : true,
            referensi:'',
            po_no:'',
            cust_id:'',
            sales_id:'',
            jenis:this.jenisForm,
            tanggal:'',
            opr:'',
            leadtime:'0',
            validity:'0',
            payment:'14',
            notes:'',
            datatrans:[],
            datahapus:[],
            isEdit: false,
            prevReferensi:'',
            prevP:'',
        },
        dk:true,
        seen:false,
        optcustomer:[],
        optsales:[],
        
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
        harga:'0',
        discount:'0',
        discount_rp:'0',
        stock:'',
        qty_pesan:'',
        qty_terima:'',
        nama2:'',
        kode2:'',
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
        debounceInput: _.debounce(function (e) {
            if (typeof e != "undefined" && e != null &&  e.trim() != '') {
                if(this.formdata.cust_id != '' && this.formdata.tanggal != ''){
                    this.$http.post('/api/cekpocust',{
                        tanggal : this.formdata.tanggal,
                        cust_id : this.formdata.cust_id,
                        po_no : e,
                    }).then(response => {
                        console.log(response.data);
                        let resp_po = response.data;
                        if(resp_po.status_po=='ok'){
                            this.formdata.po_no = e;
                        }
                        else{
                            this.$vs.dialog({
                                color:'danger',
                                title: `Error PO`,
                                text: 'Nomor PO sudah digunakan',
                            })
                            this.formdata.po_no = '';
                        }
                        
                    }).catch(error=>{
                        console.log(error);
                    });
                }
                else{
                    this.$vs.dialog({
                        color:'danger',
                        title: `Error PO`,
                        text: 'Customer / Tanggal belum dipilih',
                    })
                    this.formdata.po_no = '';
                }
            }
            else{
                this.formdata.po_no = '';
            }
            //this.formdata.po_no = e;
        }, 1000),
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
            this.$http.get('/api/sales').then(response => {
                //console.log(response.data);
                this.optsales = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            


            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.jurnalUrl+"/"+this.$route.params.ref).then(response => {
                    //this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    for(const key of keys){
                        let subkeys = Object.keys(response.data[key])
                        subkeys.forEach(item => {
                            if (this.formdata.hasOwnProperty(item))
                                this.formdata[item] = response.data[key][item]
                            
                            if(item == 'datatrans'){
                                this.formdata.datatrans = response.data[key][item];
                                let arr = Object.values(response.data[key][item]);
                                console.log(arr)
                                let maxId = _.maxBy(arr, function(o) { return o.id; })
                                //console.log(maxId)
                                //let min = Math.min(...arr);
                                //let max = Math.max(...arr);

                                this.idtrans = maxId.id
                            }
                                
                            if(item == 'qt_no'){
                                this.formdata.referensi = response.data[key][item]   
                                this.formdata.prevReferensi = this.formdata.referensi
                            }
                            if(item == 'po_cust'){
                                this.formdata.po_no = response.data[key][item]   
                            }
                            if(item == 'payment'){
                                this.formdata.payment = parseInt(response.data[key][item])   
                            }
                            if(item == 'qt_tgl'){
                                let tgldata = response.data[key][item]
                                let arrtgl = tgldata.split("-")
                                let d = new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2])
                                console.log('--pengolahan tanggal--')
                                console.log(d)
                                let dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
                                let [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
                                let tgltrans = `${day}-${month}-${year}`
                                this.formdata.tanggal = tgltrans
                            }  
                            if(item == 'id')
                                this.formdata.prevReferensi = response.data[key][item]
                            if(item == 'p')
                                this.formdata.prevP = response.data[key][item]
                        })
                    }
                }).catch(error=>{
                    console.log(error)
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
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
                    dataBarang.kode2 = this.kode2
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
            this.formdata.datahapus.push(this.formdata.datatrans[index])
            this.formdata.datatrans.splice(index,1)
            //this.formdata.datatrans[index].deleted=true
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
            if(this.formdata.validity == ''){
                this.errors.push("Validity belum terisi...")
            }
            if(this.formdata.cust_id == ''){
                this.errors.push("Customer belum dipilih...")
            }
            if(this.formdata.sales_id == ''){
                this.errors.push("Sales belum dipilih...")
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
                this.$http.post(this.jurnalUrl,{
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
          return `${this.formdata.p}|${this.formdata.tanggal}`;
        },
        hargaDisc() {
          return `${this.harga}|${this.discount}|${this.discount_rp}`;
        },

    },
    watch:{
        gunaDanTgl(newVal) {
          const [newGuna, newTgl] = newVal.split('|');
          let guna_id = newGuna
          let tanggal = newTgl
          //console.log(tanggal)
          if(this.formdata.isEdit == false){
                this.$http.post('/api/referensi',{
                    jenis : this.formdata.jenis,
                    guna_id: guna_id,
                    tglJurnal : tanggal
                }).then(response => {
                    this.formdata.referensi = response.data
                }).catch(error=>{
                    console.log(error)
                })
          } 
          
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
        'formdata.cust_id': function(){
            console.log('customer')
            let cust = this.formdata.cust_id

            let custselected = this.optcustomer.find(item => item.id==cust)
            let sales_name = custselected.sales
            let block_status =  Number(custselected.block)
            if(block_status > 0){
                this.$vs.dialog({
                        color:"danger",
                        title: 'Peringatan',
                        text: 'Status cusomer dalam keadaan blokir...!!',
                        acceptText: 'Ya saya mengerti..'
                })
            }
            let sales = this.optsales.find(item => item.sales_id==sales_name)
            console.log(sales)
            this.formdata.sales_id = sales.sales_id
            //if(this.formdata.isEdit == false)
                this.formdata.payment = custselected.kredit_term
        },
        hargaDisc(newVal) {
          const [newHarga, newDiscount, newDiscountRp] = newVal.split('|');
          let harga = Number(newHarga)
          let discount = Number(newDiscount)
          let discount_rp = Number(newDiscountRp)
          
          
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