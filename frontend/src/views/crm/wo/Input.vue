<template>
    <div>
        <vs-row vs-justify="center">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-popup fullscreen title="BUKTI BAYAR" :active.sync="popupPdf">
                <iframe width="100%" height="515" :src="pdfsrc" frameborder="0" />
            </vs-popup>
            <vs-card>
                <div class="d-md-flex align-items-center pb-2">
                    <h3 class="card-title mb-0">Work Order {{this.$route.params.ref}}</h3>
                    <div class="ml-auto">
                    <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
                    Batal
                    </vs-button>
                    </div> 
                </div>
                <h5 class="card-title mb-0">Pilih SO</h5>
                <JqxDropDownButton ref="myDropDownButton" :width="150" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="myGridOnRowSelect($event)" :ready="ready"
                            :width="width" :source="dataAdapter" :columns="columns"
                            :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true" 
                            :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                    >
                    </JqxGrid>
                </JqxDropDownButton>
                <br/>
                <h5 v-if="history_bayar.length > 0">History BPB : </h5>
                <vs-button @click="cetakJbk(bayar.ref_jurnal)" color="danger" type="border" icon="toc" size="medium" class="mr-2" v-for="bayar in history_bayar" :key="bayar.id"> {{bayar.ref_jurnal}} </vs-button>
                
                <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
                    <b>Terdapat Kesalahan..</b>
                    <ul class="common-list">
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                    </ul>
                </vs-alert>
                
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
                    Data Barang Produksi
                </vs-divider>
                <div>            
                    <vs-table  :data="formdata.datatrans" class="text-nowrap" multiple v-model="formdata.selected">
                        <template slot="header">
                        <vs-col vs-type="flex" vs-justify="left" vs-align="left" vs-lg="2" vs-sm="4" vs-xs="12">
                            <h5>DATA PERMINTAAN PRODUKSI</h5>
                        </vs-col>
                        

                        </template>
                        <template slot="thead">
                            <vs-th>Kode Barang - Nama Barang</vs-th>
                            <vs-th>Kode2</vs-th>
                            <vs-th>Size</vs-th>
                            <vs-th>Qty Pesan</vs-th>
                            
                        </template>
                        <template slot-scope="{data}">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td vs-align="left">{{ data[indextr].itembarang.kode_barang}}</vs-td>
                            <vs-td>{{ data[indextr].kode2}}</vs-td>
                            <vs-td>{{ data[indextr].size}}</vs-td>
                            <vs-td>{{ data[indextr].qty_pesan}}</vs-td>
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
            { text: 'SO NO', datafield: 'so_no', width: '150', },
            { text: 'PO NO', datafield: 'po_cust', width: '200', },
            { text: 'Tgl', datafield: 'qt_tgl', filtertype: 'date', width: '100', cellsformat: 'dd-MM-yyyy' },
            { text: 'Rekanan', datafield: 'rekanan', width: '450' }
        ],
        selectrow: 0,
        //jenis: 1,
        locale: 'de',
        currency: null,
        datetrans: '',
        formdata: {
            tanggal:'',
            referensi:'',
            voucher:'',
            uraian:'',
            jenis:4,
            tgl:'',
            opr:'',
            datatrans:[],
            datatransprod:[],
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
        podetail:[],
        optperkiraan:[],
        optitem:[],
        optperkiraanKas:[],
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
        kode2:'',
        size:'',
        dk:true,
        transval:'',
        errors:[],
        
        idtrans:0,
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
          { name: 'so_no', type: 'string'},
          { name: 'po_cust', type: 'string'},
          { name: 'qt_tgl', type: 'date'},
          { name: 'rekanan', map: 'nick'}
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/solist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        },
      }
      this.editrow = -1;
    },
    created: function(){
        var self = this
        let processdata=function (data) {
            data.THN = window.$cookies.get('THN')
            data.jenis = self.jenis
        }
        this.source.processdata = processdata
    }, 
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        disabledBeforeYear(date) {
            return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
        },
        myGridOnRowSelect: function (event) {
            this.formdata.datatrans = [];
            let args = event.args;
            let row = this.$refs.myGrid.getrowdata(args.rowindex);
            let dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 5px;">' + row['so_no'] + '</div>';
            this.$refs.myDropDownButton.setContent(dropDownContent);
            this.formdata.so_no = row['so_no']
            this.formdata.qt_id = row['id']
            this.getSO(row['so_no']).then().catch(error=>{
                this.$vs.notify({title:'Error',text:'Gagal inisiasi SO, silahkan coba lagi...',color:'danger'})
            })
            
        },
        backpage: function(){
            this.$router.push({name : 'wo'});
        },
        async getSO(referensi){
            try{
                let response = await this.$http.get("/api/getSO/"+referensi)
                this.formdata.datatrans = response.data
            }
            catch(e){
                this.$vs.notify({title:'Error',text:'Gagal inisiasi DVUD, silahkan coba lagi...',color:'danger'})
            }
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
                    this.formdata.datatransprod.push(dataBarang)
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
        clearTrans: function(){
            this.qty_pesan = ""
            this.barang = ""
            this.stock = ""
            //this.setFocusOnInput('uraian');
            this.errorBarang = false;
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
            }).catch(error=>{
                //console.log(error);
            });


            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.jbkUrl+"/"+this.$route.params.ref).then(response => {
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
                            if(item == 'tgl'){
                                let tgldata = response.data[key][item]
                                let arrtgl = tgldata.split("-")
                                let d = new Date(arrtgl[0],arrtgl[1],arrtgl[2])
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
                this.$http.post('/api/wo',{
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
            this.$router.push({name : 'wo'});
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
        
        datetrans: function(){
            let d = new Date(this.datetrans);
            const dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
            const [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
            var tgltrans = `${year }-${month}-${day}`
            this.formdata.tanggal = tgltrans
            if(this.formdata.isEdit == false){
                this.$http.post('/api/referensi',{
                    jenis : 'bpb',
                    tglJurnal : tgltrans
                }).then(response => {
                    this.formdata.referensi = response.data
                }).catch(error=>{
                    console.log(error)
                })
            }
        },
        'formdata.tanggal': function(){
            let tanggal = this.formdata.tanggal
            if(this.formdata.isEdit == false){
                 this.$http.post('/api/referensi',{
                    jenis : 'wo',
                    guna_id: 0,
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
    },
    mounted: function () {
        this.getAllData();
        this.$refs.myGrid.selectrow(0);
        this.myGridOnRowSelect({ args: { rowindex: 1 } });
    },
    
}
</script>
<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>