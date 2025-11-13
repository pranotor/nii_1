<template>
    <div>
        <vs-row vs-justify="center">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-card>
                <div class="d-md-flex align-items-center pb-2">
                    <h3 class="card-title mb-0">Penghapusan Aset</h3>
                    <div class="ml-auto">
                    <vs-button @click="backpage" color="danger" type="filled" class="mt-4 mt-md-0" icon="highlight_off" v-focus>
                    Batal
                    </vs-button>
                    </div> 
                </div>
                <h5 class="card-title mb-0">Pilih Aset </h5>
                <JqxDropDownButton ref="myDropDownButton" :width="500" :height="25">
                    <JqxGrid ref="myGrid" @rowselect="myGridOnRowSelect($event)" :ready="ready"
                            :width="width" :source="dataAdapter" :columns="columns"
                            :pageable="true" :columnsresize="true" :autoheight="true" :autorowheight="true" 
                            :sortable="true" :filterable="true" :showfilterrow="true" :pagesize="5"
                            :virtualmode="true" :rendergridrows="rendergridrows"
                    >
                    </JqxGrid>
                </JqxDropDownButton>
                <br/>
                <vs-divider>
                   Input Data Koreksi
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
                    v-model="formdata.tgl_perubahan"
                    format="DD-MM-YYYY"
                    value-type="format"
                    placeholder="Pilih Tgl" input-class="vs-input--input"
                    :disabled-date="disabledBeforeYear">
                    </date-picker>
                    </div>
                    </div>
                </vs-col>
                <vs-col vs-lg="4" vs-xs="4" vs-sm="4">  
                    <vs-input label="NILAI PEROLEHAN" disabled class="w-100 mt-4" v-model="formdata.harga_sebelum"/>  
                </vs-col>
               
                </vs-row>
                <vs-textarea label="KETERANGAN PENGHAPUSAN" ref="uraian" v-model.lazy="formdata.keterangan" class="mt-4 w-100" />
                
                <br/>
                <div class="btn-alignment mt-4">
                    <vs-button color="success" type="filled" @click.stop.prevent="checkForm">Hapus Aset</vs-button>
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
        rendergridrows: (params) => {
          return params.data;
        },
        width: "650",
        columns: [
          { text: 'Kode', datafield: 'kode', width: '15%' },
          { text: 'Kode Asset', datafield: 'kode_asset', width: '10%' },
          { text: 'Kelompok', datafield: 'nama_perk', width: '20%' },
          { text: 'Uraian', datafield: 'uraian', width: '40%' },
          { text: 'Harga Beli', datafield: 'harga_beli', width: '15%', cellsformat: 'd2', cellsalign: 'right' },
        ],
        selectrow: 0,
        jenis: 1,
        locale: 'de',
        currency: null,
        datetrans: '',
        formdata: {
            asset_id:'',
            harga_sebelum:'',
            harga_perubahan:'',
            keterangan:'',
            tgl_perubahan:'',
            opr:'',
            isEdit: false,
            prevReferensi:'',
        },
        errors:[],
      }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'asset_id', type: 'number'},
          { name: 'nama_perk', type: 'string'},
          { name: 'kode_asset', type: 'string'},
          { name: 'kode', type: 'string'},
          { name: 'uraian', type: 'string'},
          { name: 'harga_beli', type: 'number'},
          { name: 'nilai_buku', type: 'number'},

        ],
        datatype: 'json',
        root: "Rows",
        cache: false,
        beforeprocessing: (data) => {
          this.source.totalrecords = data.TotalRows;
        },
        filter: () => {
                    // update the grid and send a request to the server.
                    this.$refs.myGrid.updatebounddata("filter");
        },
        url: this.$urlbackend+'/api/aktivalist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      },
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
            let dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 5px;">' + row['uraian'] + '</div>';
            this.$refs.myDropDownButton.setContent(dropDownContent);
            this.getAktiva(row['asset_id']).then().catch(error=>{
                this.$vs.notify({title:'Error',text:'Gagal inisiasi Data Aktiva, silahkan coba lagi...',color:'danger'})
            })
            
        },
        backpage: function(){
            this.$router.push({name : 'asetlist'});
        },
        async getAktiva(referensi){
            try{
                let response = await this.$http.get('/api/aktiva/'+referensi)
                let datatrans = await response.data
                console.log(datatrans)
                this.formdata.harga_sebelum = datatrans.nilai_buku
            }
            catch(e){
                this.$vs.notify({title:'Error',text:'Gagal inisiasi Data Aktiva, silahkan coba lagi...',color:'danger'})
            }
        },
        
        
        getAllData: function(){
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
           
            if(this.formdata.tgl_perubahan == ''){
                this.errors.push("Tanggal belum terisi...")
            }

            if(this.formdata.harga_perubahan == ''){
                this.errors.push("Harga belum terisi...")
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