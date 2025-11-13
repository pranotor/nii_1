<template>
    <div>
        <vs-row vs-justify="center">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-popup fullscreen title="BUKTI BAYAR" :active.sync="popupPdf">
                <iframe width="100%" height="515" :src="pdfsrc" frameborder="0" />
            </vs-popup>
            <vs-card>
                <div class="d-md-flex align-items-center pb-2">
                    <h3 class="card-title mb-0">Proses Cutting {{this.$route.params.ref}}</h3>
                    <div class="ml-auto">
                    <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
                    Batal
                    </vs-button>
                    </div> 
                </div>
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
                        <vs-input label="NO DOC" class="w-100 mt-4" v-model="formdata.referensi"/>  
                    </vs-col>
                    
                </vs-row>
                <vs-divider>
                    Bahan Baku
                </vs-divider>
                
                <div> 
                    <vs-row>
                        <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                            <vs-select   class="w-100 mt-4" label="Kelompok Persediaan" ref="kelompok_baku" v-model="kelompok_baku" 
                            danger-text="Kode Perkiraan tidak sesuai"
                            :danger="errorPerkiraan">
                            <vs-select-item
                                :key="index"
                                :value="item.kode_perk"
                                :text="parseNama(item.nama)"
                                v-for="(item,index) in optitem_baku"
                            />
                            </vs-select>
                        </vs-col>
                        <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                            <vs-select  class="w-100 mt-4" label="Jenis Persediaan" ref="jenis_baku" v-model="jenis_baku" 
                            danger-text="Kode Perkiraan tidak sesuai"
                            :danger="errorPerkiraan">
                            <vs-select-item
                                :key="index"
                                :value="item.kode_perk"
                                :text="parseNama(item.nama)"
                                v-for="(item,index) in optjenis_baku"
                            />
                            </vs-select>
                        </vs-col>
                    </vs-row>           
                    <vs-row>
                        <vs-col vs-lg="7" vs-xs="12" vs-sm="4">  
                            <vs-select autocomplete class="w-100 mt-1" label="Kode Barang - Nama Barang" ref="barang_baku" v-model="barang_baku" 
                            danger-text="Kode Perkiraan tidak sesuai"
                            :danger="errorBarangBaku">
                            <vs-select-item
                                :key="index"
                                :value="item.kode_barang"
                                :text="item.kode_barang"
                                v-for="(item,index) in optbarang_baku"
                            />
                            </vs-select>
                        </vs-col>
                        <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                            <vs-input label="Stok" class="w-100 mt-1" v-model="stock_baku" v-currency="options" disabled/> 
                        </vs-col>
                        <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                            <vs-input label="Satuan" class="w-100 mt-1" v-model="satuan_baku" disabled/> 
                        </vs-col>
                    </vs-row>
                    <vs-row>
                        <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                            <vs-input label="JUMLAH" class="w-100 mt-1" v-model="qty_baku" v-currency="options"/> 
                        </vs-col>
                        <vs-col vs-lg="1" vs-xs="12" vs-sm="1">  
                            <div class="vs-component vs-con-input-label vs-input w-100 mt-0 vs-input-primary">  
                            <label class="vs-input--label" >&nbsp;</label>
                            <div class="vs-con-input">
                            <vs-button color="success" type="filled" @click="addTransBaku">OK</vs-button>
                            </div>
                            </div>
                        </vs-col>
                    </vs-row>
            
                </div>
                <vs-divider>
                    Hasil Cutting
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
                        <vs-col vs-lg="7" vs-xs="12" vs-sm="4">  
                            <vs-select autocomplete class="w-100 mt-1" label="Kode Barang - Nama Barang" ref="barang" v-model="barang" 
                            danger-text="Kode Perkiraan tidak sesuai"
                            :danger="errorBarang">
                            <vs-select-item
                                :key="index"
                                :value="item.kode_barang"
                                :text="item.kode_barang"
                                v-for="(item,index) in optbarang"
                            />
                            </vs-select>
                        </vs-col>
                        <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                            <vs-input label="FP" class="w-100 mt-1" v-currency="options" v-model="fp"/> 
                        </vs-col>
                        <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                            <vs-input label="Stok" class="w-100 mt-1" v-model="stock" v-currency="options" disabled/> 
                        </vs-col>
                        <vs-col vs-lg="1" vs-xs="12" vs-sm="4">  
                            <vs-input label="Satuan" class="w-100 mt-1" v-model="satuan" disabled/> 
                        </vs-col>
                    </vs-row>
                    <vs-row>
                        <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                            <vs-input label="JUMLAH" class="w-100 mt-1" v-model="qty_pesan" v-currency="options"/> 
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
                <br/>
                <div >            
                    <vs-table  :data="formdata.datatransbaku" class="text-nowrap">
                        <template slot="header">
                        <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                            <h5>DATA BAHAN BAKU</h5>
                        </vs-col>
                        

                        </template>
                        <template slot="thead">
                            <vs-th>Kode Barang - Nama Barang</vs-th>
                            <vs-th>Qty</vs-th>
                        </template>
                        <template slot-scope="{data}">
                            <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                            <vs-td vs-align="right">
                                <vs-input v-model="data[indextr].qty" v-currency="options"/>
                            </vs-td>
                        
                            </vs-tr>
                        </template>
                    </vs-table>
                    <vs-table  :data="formdata.datatransprod" class="text-nowrap">
                        <template slot="header">
                        <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                            <h5>DATA HASIL CUTTING</h5>
                        </vs-col>
                        
                        </template>
                        <template slot="thead">
                            <vs-th></vs-th>
                            <vs-th>Kode Barang - Nama Barang</vs-th>
                            <vs-th>Qty</vs-th>
                            <vs-th>FP</vs-th>
                        </template>
                        <template slot-scope="{data}">
                            <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td>
                                <div class="d-flex align-items-center">
                                <vs-button radius color="danger" :id="data[indextr].id" size="small" type="gradient" icon="delete_outline" @click="removeCutting(data[indextr].id)"></vs-button>
                                </div>
                            </vs-td>
                            <vs-td>{{ data[indextr].kode_barang}}&nbsp;&nbsp;&nbsp;{{ data[indextr].nama_barang}}</vs-td>
                            <vs-td vs-align="right">
                                <vs-input v-model="data[indextr].qty" v-currency="options"/>
                            </vs-td>
                            <vs-td vs-align="right">
                                <vs-input v-model="data[indextr].fp" v-currency="options"/>
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


    </div>
    
</template>
<script>
    /* eslint-disable no-unused-vars */
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    import JqxDropDownButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxdropdownbutton.vue";
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import { CurrencyDirective, parseCurrency, setValue } from 'vue-currency-input'
    import _ from 'lodash'
    export default {
    components: {
        JqxGrid,
        JqxDropDownButton,
        DatePicker
    },
    directives: {
        currency: CurrencyDirective
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
        width: "650",
        columns: [
            { text: 'WO NO', datafield: 'wo_no', width: '150', },
            { text: 'PO NO', datafield: 'po_cust', width: '200', },
            { text: 'Tgl', datafield: 'wo_tgl', filtertype: 'date', width: '100', cellsformat: 'dd-MM-yyyy' },
            { text: 'Rekanan', datafield: 'rekanan', width: '450' }
        ],
        selectrow: 0,
        //jenis: 1,
        locale: 'us',
        currency: null,
        datetrans: '',
        formdata: {
            referensi:'',
            tanggak:'',
            jenis:4,
            tanggal:'',
            opr:'',
            datatrans:[],
            datatransprod:[],
            datatransbaku:[],
            dataDebet:[],
            isEdit: false,
            prevReferensi:'',
            uraian_prefix:'PEMBAYARAN DVUD ',
            no_sj:'',
            so_no:'',
            qt_id:'',
            coa_kas:'',
            numSumBayar:0,
            selected:[],
        },
        optWODet:[],
        optperkiraan:[],
        optitem:[],
        optbarang:[],
        optitem_baku:[],
        optbarang_baku:[],
        optperkiraanKas:[],
        optjenis:[],
        optjenis_baku:[],
        kelompok:'',
        kelompok_baku:'',
        jenis:'',
        jenis_baku:'',
        barang:'',
        barang_baku:'',
        satuan:'',
        satuan_baku:'',
        harga:'',
        discount:'0',
        fp:'1',
        stock:'',
        stock_baku:'',
        qty_pesan:'',
        qty_terima:'',
        qty_baku:'',
        nama2:'',
        kode2:'',
        size:'',
        dk:true,
        transval:'',
        errors:[],
        errorBarang:false,
        errorBarangBaku:false,
        errorPerkiraan:false,
        idtrans:0,
        idtransBaku:0,
        dvudUrl : '/api/ju',
        jbkUrl : '/api/jbk',
        bayarUrl : '/api/bayarlist',
        countBayar: 0,
        history_bayar: [],
        popupPdf: false,
        pdfsrc: '',
        
      }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'wo_no', type: 'string'},
          { name: 'po_cust',  map:'quotation>po_cust'},
          { name: 'wo_tgl', type: 'date'},
          { name: 'rekanan', map: 'quotation>qcustomer>nama'}
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/wolist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        },
      }
      this.editrow = -1;
    },
    
    methods: {
        removeCutting: function(id) {
            let index = this.formdata.datatransprod.findIndex(x => x.id == id)
            let kode_perk = this.formdata.datatransprod[index].kode_perk
            let jenis = kode_perk.substr(0,5)
            console.log(jenis)
            let kode_barang = this.formdata.datatransprod[index].kode_barang
            let qty_terima = this.formdata.datatransprod[index].qty_terima
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
            this.formdata.datatransprod.splice(index,1)
        },
        disabledBeforeYear(date) {
            return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
        },
        
        backpage: function(){
            this.$router.push({name : 'cutting'});
        },
        async getWO(referensi){
            try{
                let response = await this.$http.post('/api/quotationdet',{
                    wo_id : referensi
                }).then(response => {
                    this.optWODet = response.data
                })
            }
            catch(e){
                this.$vs.notify({title:'Error',text:'Gagal inisiasi WO, silahkan coba lagi...',color:'danger'})
            }
        },
        addTrans: function(){
            this.errorQty = false
            if(this.barang!='' && this.qty_pesan!=''){
                //cek permintaan terhadap stock
                let minta = Number(this.qty_terima)
                let sisa = Number(this.stock)
                
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
                    //dataBarang.qty_terima = this.qty_terima
                    dataBarang.qty = this.qty_pesan
                    dataBarang.fp = this.fp
                    //dataBarang.qty_sisa = stok_barang
                    dataBarang.id = idjurnal
                    this.formdata.datatransprod.push(dataBarang)
                    this.clearTrans()
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
        addTransBaku: function(){
            this.errorQty = false
            if(this.barang_baku!='' && this.qty_baku!=''){
                //cek permintaan terhadap stock
                let minta = this.numberValue(this.qty_baku)
                let sisa = Number(this.stock_baku)
                console.log(sisa)
                console.log(minta)
                if(sisa - minta >=0){
                    var self = this
                    let idjurnal = ++this.idtransBaku
                    let dataBarang = {}
                    let obj_barang = this.optbarang_baku.find(x => x.kode_barang==self.barang_baku)
                    console.log('--pilih barang--')
                    console.log(obj_barang)
                    let stok_barang = Number(obj_barang.stock.sisa)
                    stok_barang = stok_barang - minta
                    obj_barang.stock.sisa = stok_barang
                    console.log('stok barang')
                    console.log(stok_barang)
                    let nama_barang = obj_barang.nama
                    let parts = nama_barang.split('+')
                    dataBarang.kode_barang = this.barang_baku
                    dataBarang.kode_perk = obj_barang.kode_perk
                    dataBarang.nama_barang = parts[1]
                    dataBarang.item_id = obj_barang.item_id
                    dataBarang.satuan = this.satuan_baku
                    dataBarang.referensi = this.formdata.referensi
                    dataBarang.tanggal = this.formdata.tanggal
                    dataBarang.opr = this.formdata.opr
                    dataBarang.qty = this.qty_baku
                    dataBarang.id = idjurnal
                    this.formdata.datatransbaku.push(dataBarang)
                    this.clearTransBaku()
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
        clearTrans: function(){
            this.qty_pesan = ""
            this.barang = ""
            this.stock = ""
            //this.setFocusOnInput('uraian');
            this.errorBarang = false;
        },
        clearTransBaku: function(){
            this.qty_baku = ""
            this.barang_baku = ""
            this.stock_baku = ""
            //this.setFocusOnInput('uraian');
            this.errorBarangBaku = false;
        },
        cetakJbk: function(referensi){
            this.pdfsrc = this.$urlbackend+'/api/pdf/'+referensi
            this.popupPdf = true
        },
        getAllData: function(){
            this.$http.get('/api/perkiraan').then(response => {
                //console.log(response.data);
                this.optperkiraan = response.data;
                this.optperkiraanKas = this.optperkiraan.filter(function(kode){
                    return kode.kode_perk.substring(0,2) == '11'
                })
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/itemlist').then(response => {
                //console.log(response.data);
                this.optitem = response.data
                this.optitem_baku = response.data
            }).catch(error=>{
                //console.log(error);
            });


            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get("api/cutting/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    var self = this;
                    for(const key of keys){
                        let subkeys = Object.keys(response.data[key])
                        subkeys.forEach(item => {
                            if (this.formdata.hasOwnProperty(item))
                                this.formdata[item] = response.data[key][item]
                            if(item == 'id')
                                this.idtrans = response.data[key][item]
                            if(item == 'ct_tgl'){
                                let tgldata = response.data[key][item]
                                let arrtgl = tgldata.split("-")
                                let d = new Date(arrtgl[0],arrtgl[1]-1,arrtgl[2])
                                let dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
                                let [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
                                //var tgltrans = `${day}-${month}-${year}`
                                this.formdata.tanggal = `${day}-${month}-${year}`
                            }  
                            if(item == 'ct_no'){
                                this.formdata.prevReferensi = response.data[key][item]
                                this.formdata.referensi = response.data[key][item]
                            }
                            if(item == 'cuttd'){
                                let dataitem = response.data[key][item];
                                dataitem.forEach(item => {
                                if(item.qty < 0){
                                    item.qty = Math.abs(item.qty);
                                    item.kode_barang = item.kode_perk+"_"+item.kode_barang;
                                    self.formdata.datatransbaku.push(item)
                                }
                                else{
                                    item.kode_barang = item.kode_perk+"_"+item.kode_barang;
                                    self.formdata.datatransprod.push(item)
                                }
                                
            });
                            }
                                
                        })
                    }
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
        getNamaperk2: function(kode){
            let nama_perk = this.optperkiraan.find(x => x.kode_perk==kode).nama;
            let parts = nama_perk.split('+');
            return kode+"  "+parts[1];
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
            this.errors = [];
            //cek dari client dulu...
            
        
            

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/cutting',{
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
            this.$router.push({name : 'cutting'});
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
        
        itemSelected() {
          return this.formdata.selected.length;
        },
    },
    watch:{
        'formdata.tanggal': function(){
            let tanggal = this.formdata.tanggal
            if(this.formdata.isEdit == false){
                 this.$http.post('/api/referensi',{
                    jenis : 'ct',
                    guna_id: 0,
                    tglJurnal : tanggal
                }).then(response => {
                    this.formdata.referensi = response.data
                }).catch(error=>{
                    console.log(error)
                })
            }
        },
        popupPdf: function(){
            if (this.popupPdf == false)
                this.pdfsrc = 'about:blank'
            else
                this.pdfsrc = this.pdfsrc + "#zoom=100"
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
        kelompok_baku: function(){
            this.jenis_baku = ''
            var self = this
            let arrJenis = this.optitem_baku.filter(item => item.kode_perk == self.kelompok_baku)
            //console.log(arrJenis)
            this.optjenis_baku = arrJenis.map(item => item.subperkiraan).flat()
            
        },
        jenis_baku: function(){
            console.log('jenis berubah')
            this.barang_baku = ''
            var self = this
            let arrBarang = this.optjenis_baku.filter(item => item.kode_perk == self.jenis_baku)
            this.optbarang_baku = arrBarang.map(item => item.itembarang).flat()
            
        },
        barang_baku: function(){
            console.log('barang baku')
            this.satuan_baku = ''
            var self = this
            if (self.barang_baku != ''){
                let objBarang = this.optbarang_baku.find(x => x.kode_barang == self.barang_baku);
                this.satuan_baku = objBarang.satuan
                this.stock_baku = objBarang.stock.sisa
            }
        },
        
        
    },
    mounted: function () {
        this.getAllData();
        
    },
    
}
</script>
<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>