<template>
    <div>
        <vs-row vs-justify="center">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-popup fullscreen title="BUKTI BAYAR" :active.sync="popupPdf">
                <iframe width="100%" height="515" :src="pdfsrc" frameborder="0" />
            </vs-popup>
            <vs-card>
                <div class="d-md-flex align-items-center pb-2">
                    <h3 class="card-title mb-0">Pembayaran Kas/Bank {{this.$route.params.ref}}</h3>
                    <div class="ml-auto">
                    <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
                    Batal
                    </vs-button>
                    </div> 
                </div>
                <h5 class="card-title mb-0">Pilih Voucher</h5>
                <JqxDropDownButton ref="myDropDownButton" :width="150" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="myGridOnRowSelect($event)" :ready="ready"
                            :width="width" :source="dataAdapter" :columns="columns"
                            :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true" 
                            :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                    >
                    </JqxGrid>
                </JqxDropDownButton>
                <br/>
                <h5 v-if="history_bayar.length > 0">History Jurnal Bayar : </h5>
                <vs-button @click="cetakJbk(bayar.ref_jurnal)" color="danger" type="border" icon="toc" size="medium" class="mr-2" v-for="bayar in history_bayar" :key="bayar.id"> {{bayar.ref_jurnal}} </vs-button>
                <vs-divider>
                   Input Data Pembayaran 
                </vs-divider>
                <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
                    <b>Terdapat Kesalahan..</b>
                    <ul class="common-list">
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                    </ul>
                </vs-alert>
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
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-input label="NOMOR CEK" class="w-100 mt-4" v-model="formdata.no_cheq"/>  
                </vs-col>
                
                </vs-row>
                <vs-textarea label="URAIAN" ref="uraian" v-model.lazy="formdata.uraian" class="mt-4 w-100" />
                
                <br/>
                <h5 class="card-title">Yang dibayarkan :</h5>
                <vs-row>

                    <vs-col vs-lg="3" vs-xs="12" vs-sm="4" v-for="debet in formdata.dataDebet" :key="debet.id">  
                        <vs-input :label="getNamaperk2(debet.kode)" class="w-100 mt-4" v-model="debet.kredit" v-currency="options"/>  
                    </vs-col>
                
                </vs-row>

                <vs-row>
                    <vs-col vs-lg="12" vs-xs="12" vs-sm="4">  
                    <vs-select  class="w-100 mt-4" :label="sumBayar" ref="perkiraan" v-model="formdata.coa_kas" 
                    danger-text="Kode Perkiraan tidak sesuai">
                    <vs-select-item
                        :key="index"
                        :value="item.kode_perk"
                        :text="parseNama(item.nama)"
                        v-for="(item,index) in optperkiraanKas"
                    />
                    </vs-select>
            </vs-col>
                
                </vs-row>
                <vs-divider>
                    Data DVUD
                </vs-divider>
                <div>            
                    <vs-table  :data="formdata.datatrans" class="text-nowrap">
                        <template slot="thead">
                            <vs-th>Kode Perkiraan</vs-th>
                            <vs-th>Debet</vs-th>
                            <vs-th>Kredit</vs-th>
                            <vs-th></vs-th>
                        </template>
                        <template slot-scope="{data}">
                            <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td>{{ data[indextr].kode }}&nbsp;&nbsp;&nbsp;{{getNamaperk(data[indextr].kode)}}</vs-td>
                            <vs-td vs-align="right">{{ data[indextr].debet }}</vs-td>
                            <vs-td vs-align="right">{{ data[indextr].kredit }}</vs-td>
                            
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
            { text: 'Referensi', datafield: 'no_vcr', width: '100', },
            { text: 'Tgl', datafield: 'tgl_vcr', filtertype: 'date', width: '100', cellsformat: 'dd-MM-yyyy' },
            { text: 'Uraian', datafield: 'uraian', width: '450' }
        ],
        selectrow: 0,
        jenis: 1,
        locale: 'de',
        currency: null,
        datetrans: '',
        formdata: {
            referensi:'',
            voucher:'',
            uraian:'',
            jenis:4,
            tgl_bayar:'',
            opr:'',
            datatrans:[],
            dataDebet:[],
            isEdit: false,
            prevReferensi:'',
            uraian_prefix:'PEMBAYARAN DVUD ',
            no_cheq:'',
            coa_kas:'',
            numSumBayar:0,
        },
        
        optperkiraan:[],
        optperkiraanKas:[],
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
          { name: 'no_vcr', type: 'string'},
          { name: 'tgl_vcr', type: 'date'},
          { name: 'uraian', type: 'string'},
          { name: 'bayar_count', type: 'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/dvudlist',
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
            let dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 5px;">' + row['no_vcr'] + '</div>';
            this.$refs.myDropDownButton.setContent(dropDownContent);
            this.getDvud(row['no_vcr']).then().catch(error=>{
                this.$vs.notify({title:'Error',text:'Gagal inisiasi DVUD, silahkan coba lagi...',color:'danger'})
            })
            
        },
        backpage: function(){
            this.$router.push({name : 'jbklist'});
        },
        async getDvud(referensi){
            try{
                let response = await this.$http.get(this.dvudUrl+"/"+referensi)
                let datatrans = await response.data
                this.formdata.datatrans = datatrans;
                let arrDebet = this.formdata.datatrans.filter( jurnal => jurnal.kredit > 0)
                const keys = Object.keys(response.data)
                for(const key of keys){
                    let subkeys = Object.keys(response.data[key])
                    subkeys.forEach(item => {
                        if(item == 'debet' || item == 'kredit'){
                            this.formdata.datatrans[key][item] =  new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(response.data[key][item])
                        }
                        if(item == 'referensi')
                            this.formdata.voucher = response.data[key][item]
                        if(item == 'uraian')
                            this.formdata.uraian = this.formdata.uraian_prefix+this.formdata.voucher+" :: "+response.data[key][item]    
                    })
                }
                this.formdata.dataDebet=[]
                this.formdata.dataDebet = arrDebet.map(item => ({...item}));

                let responseBayar = await this.$http.get(this.bayarUrl+"/"+referensi)
                let dataBayar = await responseBayar.data
                this.history_bayar = dataBayar 
                let arrBayar = this.history_bayar.map(item => ({...item.jbk}))
                var tempDebet = this.formdata.dataDebet
                
                _.forEach(arrBayar, function(arr){
                    _.forEach(arr, function(bayar){
                        let kode = bayar.kode
                        let jumlah = Number(bayar.debet)
                        let ArrDebet = tempDebet.filter(data => data.kode == kode)
                        _.update(ArrDebet, 'ArrDebet[0].kredit', function(n){ 
                                let kredit = ArrDebet[0].kredit
                                let numKredit = Number(kredit.replace(/\./g,'').replace(/,/g, '.'))
                                let kreditAkhir = numKredit - jumlah
                                ArrDebet[0].kredit = new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(kreditAkhir)
                            return '8.000'
                        });
                        //console.log('hasil filter')
                        //console.log(tempArrDebet)
                    })
                })
            }
            catch(e){
                this.$vs.notify({title:'Error',text:'Gagal inisiasi DVUD, silahkan coba lagi...',color:'danger'})
            }
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
                            if(item == 'tgl_bayar'){
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
            if(this.formdata.numSumBayar == 0){
                this.errors.push("Jumlah Pembayaran tidak boleh nol...")
            }
            if(this.formdata.tgl_bayar == ''){
                this.errors.push("Tanggal belum terisi...")
            }

            if(this.formdata.referensi == ''){
                this.errors.push("Referensi belum terisi...")
            }

            if(this.formdata.coa_kas == ''){
                this.errors.push("Akun pembayaran belum terisi...")
            }

            if(this.formdata.voucher == ''){
                this.errors.push("Voucher (DVUD) belum dipilih...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post(this.jbkUrl,{
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
            this.$router.push({name : 'jbklist'});
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
        sumBayar() {
            let initialValue = 0
            let sum = this.formdata.dataDebet.reduce(
                (accumulator, currentValue) => accumulator + this.numberValue(currentValue.kredit)
                , initialValue)
            this.formdata.numSumBayar = sum       
            let str_ret = new Intl.NumberFormat('de-DE',{notation: 'standard',}).format(sum)
            return "Pembayaran sejumlah Rp "+str_ret+" melalui akun :"
        },
    },
    watch:{
        
        datetrans: function(){
            let d = new Date(this.datetrans);
            const dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: '2-digit', day: '2-digit' }) 
            const [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(d) 
            var tgltrans = `${year }-${month}-${day}`
            this.formdata.tgl_bayar = tgltrans
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
        },
        popupPdf: function(){
            if (this.popupPdf == false)
                this.pdfsrc = 'about:blank'
            else
                this.pdfsrc = this.pdfsrc + "#zoom=100"
        }
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