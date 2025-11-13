<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Tanda Tangan </h3>
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
            <vs-col vs-lg="12" vs-xs="12" vs-sm="3">  
                <vs-select class="w-100 mt-4" ref="unit" label="MODULE" v-model="formdata.module_id">
              <vs-select-item
                :key="index"
                :value="item.id"
                :text="item.name"
                v-for="(item,index) in optmodule"
              />
            </vs-select> 
            </vs-col>
          </vs-row>
          <vs-row v-if="formdata.module_id==1">
            <vs-col vs-lg="12" vs-xs="12" vs-sm="3">  
                <vs-select class="w-100 mt-4" ref="unit" label="JENIS JURNAL" v-model="formdata.jenis">
                <vs-select-item
                    :key="index"
                    :value="item.jns"
                    :text="item.uraian"
                    v-for="(item,index) in optjenis"
                />
                </vs-select> 
            </vs-col>
          </vs-row>

          <vs-row v-if="formdata.module_id > 0">
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="FUNGSI" class="w-100 mt-4" ref="transval" v-model="transFungsi" />  
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="JABATAN" class="w-100 mt-4" ref="transval" v-model="transJabatan" />  
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="NAMA" class="w-100 mt-4" ref="transval" v-model="transNama" />  
            </vs-col>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="NIP" class="w-100 mt-4" ref="transval" v-model="transNip" />  
            </vs-col>
            <vs-col vs-lg="1" vs-xs="12" vs-sm="1">  
                <div class="vs-component vs-con-input-label vs-input w-100 mt-3 vs-input-primary">  
                <label class="vs-input--label" >&nbsp;</label>
                <div class="vs-con-input">
                <vs-button color="success" type="filled" @click="addTrans">OK</vs-button>
                </div>
                </div>
            </vs-col>
          </vs-row>
        </div>
        <hr class="custom-hr" />
        <div>            
            <vs-table  :data="formdata.datatrans" class="text-nowrap">
                <template slot="thead">
                    <vs-th>FUNGSI</vs-th>
                    <vs-th>JABATAN</vs-th>
                    <vs-th>NAMA</vs-th>
                    <vs-th>NIP</vs-th>
                    <vs-th></vs-th>
                </template>
                <template slot-scope="{data}">
                    <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td ><vs-input v-model="data[indextr].fungsi" /></vs-td>
                    <vs-td ><vs-input v-model="data[indextr].jabatan" /></vs-td>
                    <vs-td ><vs-input v-model="data[indextr].nama" /></vs-td>
                    <vs-td ><vs-input v-model="data[indextr].nip" /></vs-td>
                    <vs-td>
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
  /* eslint-disable no-console */
  /* eslint-disable no-case-declarations */

  

  export default {
    data: function () {
      return {
        // eslint-disable-next-line
        formdata: {
            datatrans:[],
            mod_name:'',
            module_id:'',
            jenis:'0',
            tipe:'',
            keterangan:'',
            isEdit: false,
            prevReferensi:''
        },
        transFungsi:'',
        transJabatan:'',
        transNama:'',
        transNip:'',
        optmodule:[],
        optjenis:[],
        errors:[],
        idtrans:0,
        inputUrl: '/api/tandatangan'
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        getAllData: function(){
            this.$http.get('/api/module').then(response => {
                //console.log(response.data);
                this.optmodule = response.data;
            }).catch(error=>{
                //console.log(error);
            });
            this.$http.get('/api/jenis').then(response => {
                //console.log(response.data);
                this.optjenis = response.data;
            }).catch(error=>{
                //console.log(error);
            });
            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.inputUrl+"/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    //this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data[0])
                    console.log(keys)
                    var self = this
                    for(const key of keys){
                        //console.log(key)
                        if (this.formdata.hasOwnProperty(key))
                            this.formdata[key] = response.data[0][key]
                        if(key == 'id')
                            this.formdata.prevReferensi = response.data[0][key]
                        if(key == 'mod_name'){
                            let mod_name = response.data[0][key]
                            //console.log(mod_name)
                            let mod_id = this.optmodule.filter(item => item.controller == mod_name).map(item => item.id)
                            //console.log(mod_id);
                            this.formdata.module_id = mod_id
                        }
                        if(key == 'pejabat'){
                            let pejabat = response.data[0][key]
                            console.log(pejabat)
                            pejabat.forEach(item => {
                                let data = {};
                                data.fungsi = item.fungsi
                                data.jabatan = item.jabatan
                                data.nama = item.nama
                                data.nip = item.nip
                                //data.opr = this.formdata.opr
                                data.id = item.id
                                self.formdata.datatrans.push(data)
                            })
                            
                        }
                    }
                }).catch(error=>{
                    console.log(error)
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                });
            }
            //edit mode init data

            this.formdata.pemohon_id = this.$store.getters.getUserId
        },
        addTrans: function(){
            let idjurnal = ++this.idtrans
            let data = {};
            data.fungsi = this.transFungsi
            data.jabatan = this.transJabatan
            data.nama = this.transNama
            data.nip = this.transNip
            data.opr = this.formdata.opr
            data.id = idjurnal
            this.formdata.datatrans.push(data)
            this.clearTrans()    
        },
        removeTrans: function(id) {
            let index = this.formdata.datatrans.findIndex(x => x.id == id)
            this.formdata.datatrans.splice(index,1)
        },
        clearTrans: function(){
            //this.transval = "";
            //this.errorPerkiraan = false;
        },
        numberValue (value) {
            //alert(value)
            return parseCurrency(value.toString(), this.options)
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            
            if(this.formdata.module_id == ''){
                this.errors.push("Module belum terisi...")
            }

            if(this.formdata.module_id == '1' && this.formdata.jenis == '0'){
                this.errors.push("Jenis Jurnal belum terisi...")
            }

            if(this.formdata.datatrans.length == 0){
                this.errors.push("Data pejabat penandatangan masih kosong...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post(this.inputUrl,{
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
            this.$router.push({name : 'ttdlist'});
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
    watch:{
        'formdata.module_id': function(){
            let mod_id = this.formdata.module_id
            if(mod_id != 1)
                this.formdata.jenis = 0
            let mod_name = this.optmodule.filter(item => item.id == mod_id).map(item => item.controller).join('');
            this.formdata.mod_name = mod_name
        },
    },
    mounted: function () {
        this.getAllData();
    },
  }
</script>