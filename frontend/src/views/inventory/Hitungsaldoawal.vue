<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Hitung saldo awal tahun</h3>

        </div>
        <hr class="custom-hr" />  
        <vs-button @click="checkForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Proses...
          </vs-button>
      </vs-card>
    </vs-col>
   </vs-row>
</template>

<script>
  import DatePicker from 'vue2-datepicker'
  import 'vue2-datepicker/index.css'
  import _ from 'lodash'
  const FileDownload = require('js-file-download');

    
  export default {
    components: {
        DatePicker
    },
    data: function () {
      return {
        // eslint-disable-next-line
        notSpacer: true,
        //jenis: this.jenisJurnal,
        popupPdf: false,
        popupFilter: false,
        titleLaporan: '',
        namaLaporan: '',
        reportUrl: '',
        pdfsrc: '',
        filterPerkiraan: false,
        filterStatusVcr: false,
        filterRekanan: false,
        filterJenis: false,
        filterAktiva: false,
        filterStatusAktiva: false,
        filterDokumen: false,
        filterNama: false,
        filterFormat: false,
        filterLevel: false,
        showProgress: false,
        activeTab: 0,
        optperkiraan:[],
        optjenisAktiva:[],
        optperiode:[
            {"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"},{"value":"tgl","name":"TANGGAL"},
            {"value":"range","name":"KISARAN TANGGAL"}
        ],
        optstatusvcr:[
            {"value":"all","name":"SEMUA"},{"value":"lns","name":"SUDAH LUNAS"},{"value":"blm","name":"BLM LUNAS"}
        ],
        optstatusaktiva:[
            {"value":"all","name":"SEMUA"},{"value":"del","name":"DIHAPUS"}
        ],
        optjenis:[],
        optrekanan:[],
        selected:[],
        formdata:{
            perkiraan:'',
            jenisAktiva:'',
            statusAktiva:'all',
            dokumen:'',
            nama:'',
            periode:'',
            tanggal:'',
            range:'',
            bulan:'',
            thnvaluta:'',
            statusvcr:'all',
            format:'',
            level:'',
            rekanan:'',
            jenis:'1',
        },
        errors:[],
        laporan:[]
      }
    },
    methods: {
        checkForm:function(mode) {
            var self = this
            this.$vs.loading({'type':'radius'})
            this.$http.get('/api/hitung_saldo_awal').then(function(response){
                console.log(response)
                self.handleSuccess();
                //
            }).catch(error=>{
                console.log(error)
                self.handleError(error)
            }) 
            //this.pdfsrc = this.reportUrl
            self.popupFilter = false
            self.activeTab = 1
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
            this.$vs.notify({title:'Success',text:'Proses selesai..',color:'success'})
            //this.$router.push('/jurnal/ju');
        },
        notify: function(){
            this.$vs.notify({
                title:'Gagal Proses Data',
                text:'Click untuk melihat',
                color:'danger',
                fixed:false,
                icon: 'warning',
                click:()=>{
                    window.scrollTo(0,0);
                },
            })
        },
        getAllData: function(){
            this.$http.get('/api/perkiraan').then(response => {
                console.log(response.data);
                this.optperkiraan = response.data;
                this.optjenisAktiva = response.data.filter(item =>{
                    if(item.kode_perk.substring(0,2)=='31')
                        return item;
                }) 
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/jenis').then(response => {
                //console.log(response.data);
                this.optjenis = response.data;
            }).catch(error=>{
                //console.log(error);
            });

            this.$http.get('/api/rekanan').then(response => {
                //console.log(response.data);
                this.optrekanan = response.data;
            }).catch(error=>{
                //console.log(error);
            });
            //this.formdata.opr = this.$store.getters.getUserName
            this.$http.get('/api/laporanlist').then(response => {
                let links = response.data
                this.laporan = links
            }).catch(error=>{
                console.log(error)
            });

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
        handleSelected(tr) {
            this.formdata.periode = ''
            this.titleLaporan = "FILTER LAPORAN "+ tr.name
            this.namaLaporan = _.kebabCase(tr.name);
            this.popupFilter = true
            this.reportUrl = this.$urlbackend+'/api/'+ tr.url
            let strfilter = tr.additional
            strfilter = strfilter.replace(/\\/g, '')
            let objfilter = JSON.parse(strfilter)
            this.filterPerkiraan = objfilter.kode_perkiraan
            this.filterStatusVcr = objfilter.status_vcr
            this.filterRekanan = objfilter.rekanan
            this.filterJenis = objfilter.jenisJurnal
            this.filterAktiva = objfilter.jenisAktiva
            this.filterStatusAktiva = objfilter.filterStatusAktiva
            this.filterDokumen = objfilter.dokumen
            this.filterNama = objfilter.nama
            this.filterFormat = objfilter.filterFormat
            this.filterLevel = objfilter.filterLevel
            this.optperiode = objfilter.periode
            this.optformat = objfilter.format
            this.optlevel = objfilter.level
        },
    },
    
    watch: {
        popupPdf: function(){
            if (this.popupPdf == false)
                this.pdfsrc = 'about:blank'
            else
                this.pdfsrc = this.pdfsrc + "#zoom=100"
        },
    },
    created: function(){
        this.formdata.thnvaluta = window.$cookies.get('THN')
    }, 
    mounted: function () {
        this.getAllData();
    },
  }
</script>

<style lang="stylus">

.mx-datepicker-popup
  z-index : 80000

.cetak
  height: 80vh

</style>


