<template>
    <div>
        <vs-row vs-justify="center">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-popup fullscreen title="BUKTI BAYAR" :active.sync="popupPdf">
                <iframe width="100%" height="515" :src="pdfsrc" frameborder="0" />
            </vs-popup>
            <vs-card>
                <div class="d-md-flex align-items-center pb-2">
                    <h3 class="card-title mb-0">Kirim Invoice {{this.$route.params.ref}}</h3>
                    <div class="ml-auto">
                    <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
                    Batal
                    </vs-button>
                    </div> 
                </div>
                <div>
                    <vs-row>
                    <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                        <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                        <label class="vs-input--label" >Tanggal</label>
                        <div class="vs-con-input">
                        <date-picker
                        v-model="formdata.tt_tgl"
                        format="DD-MM-YYYY"
                        value-type="format"
                        placeholder="Pilih Tgl" input-class="vs-input--input"
                        :disabled-date="disabledBeforeYear">
                        </date-picker>
                        </div>
                        </div>
                    </vs-col>
                    </vs-row>
                </div>
                <br/>
                <h5 class="card-title mb-0">Pilih Invoice</h5>
                <JqxDropDownButton ref="myDropDownButton" :width="150" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="myGridOnRowSelect($event)"  @rowunselect="myGridOnRowSelect($event)" 
                            :width="width" :source="dataAdapter" :columns="columns" :selectionmode="'checkbox'"
                            :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true" 
                            :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                    >
                    </JqxGrid>
                </JqxDropDownButton>
                <br/>
                <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
                    <b>Terdapat Kesalahan..</b>
                    <ul class="common-list">
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                    </ul>
                </vs-alert>
                <vs-table  :data="formdata.invoices" class="text-nowrap">
                    <template slot="header">
                    <vs-col vs-type="flex" vs-justify="left" vs-align="center" vs-lg="2" vs-sm="4" vs-xs="12">
                        <h5>Data Invoice</h5>
                    </vs-col>
                    </template>
                    <template slot="thead">
                        <vs-th>No Invoice</vs-th>
                        <vs-th>Subtotal</vs-th>
                        <vs-th>Ppn</vs-th>
                        
                    </template>
                    <template slot-scope="{data}">
                        <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                        <vs-td>{{ data[indextr].inv_no}}</vs-td>
                        <vs-td vs-align="right">
                            <vs-input v-model="data[indextr].subtotal" v-currency="options" disabled style="width:150px"/>
                        </vs-td>
                        <vs-td vs-align="right">
                            <vs-input v-model="data[indextr].ppn" v-currency="options" disabled style="width:150px"/>
                        </vs-td>
                       
                        </vs-tr>
                    </template>
                </vs-table>
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
        width: "750",
        columns: [
          { text: 'Tgl', datafield: 'inv_tgl', filtertype: 'range', width: '15%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Customer', datafield: 'nama', width: '30%'},
          { text: 'No Invoice', datafield: 'inv_no', width: '15%' },
          { text: 'PO Customer', datafield: 'po_cust', width: '20%' },
          { text: 'Total', datafield: 'total', width: '20%',cellsformat: 'D' },
          
        ],
        selectrow: 0,
        //jenis: 1,
        locale: 'us',
        currency: null,
        datetrans: '',
        formdata: {
            tt_tgl:'',
            cust_id:'',
            inv_no:'',
            p:'',
            invoices:[],
            opr:'',
            isEdit: false,
            prevReferensi:'',
        },
        
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
          { name: 'inv_no', type: 'string'},
          { name: 'inv_tgl', type: 'date'},
          { name: 'nama', type:'string'},
          { name: 'po_cust',type:'string'},
          { name: 'subtotal', type:'number'},
          { name: 'ppn', type:'number'},
          { name: 'total', type:'number'},
          { name: 'cust_id', type:'number'},
          { name: 'p', type:'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/invoiceblmkirim',
        type: 'post',
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
            data.jenis = self.jenis
        }
        this.source.processdata = processdata
    }, 
    methods: {
        disabledBeforeYear(date) {
            return date.getFullYear() < window.$cookies.get('THN') || date.getFullYear() > window.$cookies.get('THN');
        },
        myGridOnRowSelect: function (event) {
            var self = this;
            let rows = this.$refs.myGrid.getselectedrowindexes();
            let sumInvoice = 0;
            self.formdata.inv_id = [];
            self.formdata.inv_no = [];
            self.formdata.invoices = [];
            rows.forEach(item => {
                let dataRecord = this.$refs.myGrid.getrowdata(item)
                sumInvoice = sumInvoice + Number(dataRecord.total)
                self.formdata.invoices.push(dataRecord)
                self.formdata.inv_no.push(dataRecord.inv_no)
                self.formdata.cust_id = dataRecord.cust_id
                self.formdata.p = dataRecord.p
            });
        },
        
        backpage: function(){
            this.$router.push({name : 'ttinvoicelist'});
        },
        async getInv(data){
            console.log(data);
            this.formdata.subtotal = new Intl.NumberFormat('us-US',{style: 'decimal',maximumFractionDigits: 2}).format(data['subtotal']);
            this.formdata.ppn = new Intl.NumberFormat('us-US',{style: 'decimal',maximumFractionDigits: 2}).format(data['ppn']);
            this.formdata.p = data.p
            this.formdata.inv_tgl = data.inv_tgl
            this.formdata.inv_no = data.inv_no
            this.formdata.cust_id = data.cust_id
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
                this.optitem_hasil = response.data
            }).catch(error=>{
                //console.log(error);
            });


            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get("/api/produksi/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    for(const key of keys){
                        let subkeys = Object.keys(response.data[key])
                        subkeys.forEach(item => {
                            if (this.formdata.hasOwnProperty(item))
                                this.formdata[item] = response.data[key][item]
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
            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/invoicekirim',{
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
            this.$router.push({name : 'ttinvoicelist'});
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
                    jenis : 'pd',
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
        kelompok_hasil: function(){
            console.log('dor')
            this.jenis = ''
            var self = this
            let arrJenis = this.optitem_hasil.filter(item => item.kode_perk == self.kelompok)
            //console.log(arrJenis)
            this.optjenis_hasil = arrJenis.map(item => item.subperkiraan).flat()
            
        },
        jenis_hasil: function(){
            this.barang = ''
            var self = this
            let arrBarang = this.optjenis_hasil.filter(item => item.kode_perk == self.jenis)
            this.optbarang_hasil = arrBarang.map(item => item.itembarang).flat()
            
        },
        
        
    },
    mounted: function () {
        this.getAllData();
    },
    
}
</script>
<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>