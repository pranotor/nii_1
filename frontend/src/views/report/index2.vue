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
                    :data="users" notSpacer maxHeight="300px">
                    
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
                <div>
                    <iframe width="100%" height="300" :src="pdfsrc" name="iframeReport" frameborder="0" />
                </div>
            </vs-tab>
            
        </vs-tabs>
      </vs-card>
      <vs-popup :title="titleLaporan" class="popup-filter" :active.sync="popupFilter">
        <form ref="formFilter" target="iframeReport" method="post">
            <vs-select multiple v-if="this.filterPerkiraan" autocomplete class="w-100" label="KODE PERKIRAAN" ref="perkiraan" v-model="formdata.perkiraan">
            <vs-select-item
                :key="index"
                :value="item.kode_perk"
                :text="parseNama(item.nama)"
                v-for="(item,index) in optperkiraan"
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
            <hr class="custom-hr" />
            <input class="btn-block btn btn-primary submit-button" type="submit" @click.stop.prevent="checkForm" value="Proses"/>
        </form>  
      </vs-popup>
    </vs-col>
   
   </vs-row>
</template>

<script>
  import DatePicker from 'vue2-datepicker'
  import 'vue2-datepicker/index.css'

    
  export default {
    components: {
        DatePicker
    },
    data: function () {
      return {
        // eslint-disable-next-line
        notSpacer: true,
        jenis: this.jenisJurnal,
        popupPdf: false,
        popupFilter: false,
        titleLaporan: '',
        reportUrl: '',
        pdfsrc: '',
        filterPerkiraan: false,
        showProgress: false,
        activeTab: 0,
        optperkiraan:[],
        optperiode:[
            {"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"},{"value":"tgl","name":"TANGGAL"},
            {"value":"range","name":"KISARAN TANGGAL"}
        ],
        selected:[],
        formdata:{
            perkiraan:'',
            periode:'',
            tanggal:'',
            range:'',
            bulan:'',
            thnvaluta:'',
        },
        users:[
            {
                "id": 1,
                "url" : "neracasaldo",
                "name": "NERACA SALDO",
                "value":"neracasaldo",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 2,
                "name": "WORKSHEET",
                "value":"worksheet",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 3,
                "name": "NERACA",
                "value":"neraca",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 4,
                "name": "LABA RUGI",
                "value":"lr",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 5,
                "name": "ARUS KAS",
                "value":"aruskas",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 6,
                "name": "BUKU BESAR",
                "value":"ledger",
                "objFilter": {"kode_perkiraan":true,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"},{"value":"range","name":"KISARAN TANGGAL"}]}
            },
            {
                "id": 7,
                "name": "BIAYA",
                "value":"rekapbiaya",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 8,
                "name": "DAFTAR KEWAJIBAN UTANG",
                "value":"rekaputang",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 9,
                "name": "REKAP STOK PERSEDIAAN",
                "value":"stock",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 10,
                "name": "REKAP NILAI PERSEDIAAN",
                "value":"nilaipersediaan",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 11,
                "name": "REKAP VOUCHER",
                "value":"rekapvoucher",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 12,
                "name": "REKAP ASSET",
                "value":"rekapasset",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 13,
                "name": "REKAP JURNAL",
                "value":"rekapjurnal",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            },
            {
                "id": 13,
                "name": "JURNAL BAYAR KAS BANK",
                "value":"rekapbayar",
                "objFilter": {"kode_perkiraan":false,"periode":[{"value":"bl","name":"BULANAN"},{"value":"th","name":"TAHUNAN"}]}
            }
        ]
      }
    },
    methods: {
        checkForm:function(e) {
            var formfilter= this.$refs.formFilter
            formfilter.action = this.reportUrl
            formfilter.submit();
            //this.pdfsrc = this.reportUrl
            this.popupFilter = false
            this.activeTab = 1
        },
        getAllData: function(){
            this.$http.get('/api/perkiraan').then(response => {
                //console.log(response.data);
                this.optperkiraan = response.data;
            }).catch(error=>{
                //console.log(error);
            });
            //this.formdata.opr = this.$store.getters.getUserName
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
            this.popupFilter = true
            this.reportUrl = this.$urlbackend+'/api/'+ tr.url
            this.popupPdf = true
            let objfilter = tr.objFilter
            this.filterPerkiraan = objfilter.kode_perkiraan
            this.optperiode = objfilter.periode
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
</style>


