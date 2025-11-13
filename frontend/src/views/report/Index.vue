<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Laporan</h3>

        </div>
        <hr class="custom-hr" />  
        <vs-tabs alignment="fixed" :value="activeTab">
            <vs-tab icon="subject" label="Daftar Laporan" @click="activeTab=0">
                <div>
                    <vs-table
                    v-model="selected"
                    @selected="handleSelected"
                    :data="laporan" notSpacer maxHeight="300px">
                    
                    <template slot-scope="{data}">
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data" >
                        <vs-td :data="data[indextr].id">
                            {{data[indextr].name}}
                        </vs-td>

                       
                        </vs-tr>
                    </template>
                    </vs-table>
                </div>
            </vs-tab>
            
            <vs-tab icon="web" label="Preview">
                <div class="cetak">
                    <iframe width="100%" height="100%" :src="pdfsrc" ref="iframeReport" frameborder="0" />
                </div>
            </vs-tab>
            
        </vs-tabs>
      </vs-card>
      <vs-popup :title="titleLaporan" class="popup-filter" :active.sync="popupFilter">
            <vs-select v-if="this.filterJenis" class="w-100 mb-4" label="JENIS JURNAL" ref="perkiraan" v-model="formdata.jenis">
            <vs-select-item
                :key="index"
                :value="item.jns"
                :text="item.uraian"
                v-for="(item,index) in optjenis"
            />   
            </vs-select> 
            <vs-select multiple v-if="this.filterAktiva" class="w-100 mb-4" label="KELOMPOK AKTIVA" ref="perkiraan" v-model="formdata.jenisAktiva">
            <vs-select-item
                :key="index"
               :value="item.kode_perk"
                :text="parseNama(item.nama)"
                v-for="(item,index) in optjenisAktiva"
            />    
            </vs-select>
            <vs-select v-if="this.filterStatusAktiva" class="w-100 mb-4" label="STATUS AKTIVA" ref="statusaktiva" v-model="formdata.statusAktiva">
            <vs-select-item
                :key="index"
               :value="item.value"
                :text="item.name"
                v-for="(item,index) in optstatusaktiva"
            />    
            </vs-select>
            <vs-row v-if="this.filterDokumen" >
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-input  label="NO DOKUMEN" class="w-100 mb-4" v-model="formdata.dokumen"/>  
                </vs-col>
            </vs-row>
            <vs-row v-if="this.filterNama" >
                <vs-col vs-lg="6" vs-xs="12" vs-sm="4">  
                    <vs-input  label="KODE BARANG" class="w-100 mb-4" v-model="formdata.nama"/>  
                </vs-col>
            </vs-row>
            <vs-select multiple v-if="this.filterPerkiraan" autocomplete class="w-100 mb-4" label="KODE PERKIRAAN" ref="perkiraan" v-model="formdata.perkiraan">
            <vs-select-item
                :key="index"
                :value="item.kode_perk"
                :text="parseNama(item.nama)"
                v-for="(item,index) in optperkiraan"
            />
            </vs-select>
            <vs-select class="w-100 mb-4" v-if="this.filterRekanan" autocomplete label="REKANAN" ref="rekanan" v-model="formdata.rekanan">
            <vs-select-item
                :key="index"
                :value="item.kode"
                :text="item.nama"
                v-for="(item,index) in optrekanan"
            />
            </vs-select>
            <vs-select class="w-100 mb-4" v-if="this.filterStatusVcr" label="STATUS VOUCHER" ref="statusvcr" v-model="formdata.statusvcr">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optstatusvcr"
            />
            </vs-select>
            <vs-select class="w-100 mb-4" v-if="this.filterStatusMutasi" label="STATUS" ref="statusvcr" v-model="formdata.statusmutasi">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optstatusmutasi"
            />
            </vs-select>
            <vs-select class="w-100 mb-4" v-if="this.filterFormat" label="FORMAT" ref="format" v-model="formdata.format">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optformat"
            />
            </vs-select>
            <vs-select class="w-100 mb-4" v-if="this.filterLevel" label="LEVEL" ref="level" v-model="formdata.level">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optlevel"
            />
            </vs-select>
            <vs-select class="w-100" label="PERIODE" ref="periode" v-model="formdata.periode">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optperiode"
            />
            </vs-select>
            <vs-row v-if="formdata.periode=='bl'">
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                    <label class="vs-input--label" >BULAN</label>
                    <div class="vs-con-input">
                    <date-picker  v-model="formdata.bulan" value-type="format" type="month" placeholder="Pilih Bulan"></date-picker>
                    </div>
                    </div>
                </vs-col>
            </vs-row>
            <vs-row v-if="formdata.periode=='tgl'">
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                    <label class="vs-input--label" >TANGGAL</label>
                    <div class="vs-con-input">
                    <date-picker  v-model="formdata.tanggal" value-type="format" format="YYYY-MM-DD" placeholder="Pilih Tanggal"></date-picker>
                    </div>
                    </div>
                </vs-col>
            </vs-row>  
            <vs-row v-if="formdata.periode=='range'">
                <vs-col vs-lg="3" vs-xs="12" vs-sm="4">  
                    <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">  
                    <label class="vs-input--label" >TANGGAL</label>
                    <div class="vs-con-input">
                    <date-picker  v-model="formdata.tanggal" range value-type="format" format="YYYY-MM-DD" placeholder="Pilih Tanggal"></date-picker>
                    </div>
                    </div>
                </vs-col>
            </vs-row> 
            <vs-select class="w-100 mt-4" label="GROUP BY" v-if="this.filterGroupingSales" ref="periode" v-model="formdata.groupsales">
            <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.name"
                v-for="(item,index) in optgroupsales"
            />
            </vs-select>
            <vs-select v-if="this.filterSales" autocomplete class="w-100 mb-4 mt-4" label="NAMA SALES" ref="sales" v-model="formdata.sales">
            <vs-select-item
                :key="index"
                :value="item.nama"
                :text="item.nama"
                v-for="(item,index) in optsales"
            />
            </vs-select>
            <hr class="custom-hr" />
            <vs-button color="success" type="filled" @click.stop.prevent="checkForm(0)">Preview</vs-button>
            &nbsp;<vs-button color="primary" type="filled" @click.stop.prevent="checkForm(1)">Download xls</vs-button>
      </vs-popup>
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
        filterStatusMutasi: false,
        filterDokumen: false,
        filterGroupingSales: false,
        filterSales: false,
        filterNama: false,
        filterFormat: false,
        filterLevel: false,
        showProgress: false,
        activeTab: 0,
        optperkiraan:[],
        optsales:[],
        optjenisAktiva:[],
        optperiode:[
            {"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"},{"value":"tgl","name":"TANGGAL"},
            {"value":"range","name":"KISARAN TANGGAL"}
        ],
        optstatusvcr:[
            {"value":"all","name":"SEMUA"},{"value":"lns","name":"SUDAH LUNAS"},{"value":"blm","name":"BLM LUNAS"}
        ],
        optstatusmutasi:[
            {"value":"all","name":"SEMUA"},{"value":"aktif","name":"AKTIF"}
        ],
        optstatusaktiva:[
            {"value":"all","name":"SEMUA"},{"value":"del","name":"DIHAPUS"}
        ],
        optgroupsales:[
            {"value":"sales","name":"SALES"},{"value":"none","name":"NONE"}
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
            groupsales:'sales',
            sales:'--all--',
            range:'',
            bulan:'',
            thnvaluta:'',
            statusvcr:'all',
            statusmutasi:'all',
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
            if(mode==0){
                this.$http.post(this.reportUrl,{
                        formdata : this.formdata,
                        mode:mode,
                }).then(function(response){
                    console.log(response)
                    self.pdfsrc = self.$urlbackend+response.data
                    //console.log(response.data)
                    self.handleSuccess();
                    //
                }).catch(error=>{
                    console.log(error)
                    self.handleError(error)
                }) 
                //this.pdfsrc = this.reportUrl
                self.popupFilter = false
                self.activeTab = 1
            }
            else{
                console.log('xls')
                this.$http.post(this.reportUrl,{
                        formdata : this.formdata,
                        mode:mode,
                },
                {responseType: 'blob'}).then( response => {
                    FileDownload(response.data, this.namaLaporan+'.xlsx');
                    self.handleSuccess();
                    self.popupFilter = false
                }).catch(error=>{
                    console.log(error)
                    self.handleError(error)
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
            this.$vs.notify({title:'Success',text:'Proses download report selesai..',color:'success'})
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
            this.$http.get('/api/sales').then(response => {
                this.optsales = response.data;
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
            this.filterStatusMutasi = objfilter.filterStatusMutasi
            this.filterDokumen = objfilter.dokumen
            this.filterGroupingSales = objfilter.groupingsales
            this.filterSales = objfilter.sales
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


