<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Customer {{this.$route.params.ref}}</h3>
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
            
            <vs-col vs-lg="8" vs-xs="12" vs-sm="3">  
                <vs-input label="NAMA PERUSAHAAN" class="w-100 mt-4" ref="transval" v-model="formdata.nama" /> 
            </vs-col>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="3">  
                <vs-input label="NICK PERUSAHAAN" class="w-100 mt-4" ref="transval" v-model="formdata.nick" /> 
            </vs-col>
          </vs-row>
          <vs-row>
            <vs-col vs-lg="9" vs-xs="12" vs-sm="3">  
                <vs-input label="ALAMAT" class="w-100 mt-4" ref="transval" v-model="formdata.alamat" /> 
            </vs-col>  
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-select class="w-100 mt-4" label="KEL KOTA" ref="kota" v-model="formdata.kota">
                    <vs-select-item
                        :key="index"
                        :value="item.kel_kode"
                        :text="item.kel_nama"
                        v-for="(item,index) in optkota"
                    />
                </vs-select>
            </vs-col>
            
          </vs-row>
          
          <vs-row>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="TELEPON" class="w-100 mt-4" ref="transval" v-model="formdata.telepon" /> 
            </vs-col>  
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="FAX" class="w-100 mt-4" ref="transval" v-model="formdata.fax" /> 
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="EMAIL" class="w-100 mt-4" ref="transval" v-model="formdata.email" /> 
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="NPWP" class="w-100 mt-4" ref="transval" v-model="formdata.npwp" /> 
            </vs-col>
          </vs-row>
          <vs-row>
            <vs-col vs-lg="9" vs-xs="12" vs-sm="3">  
                <vs-input label="ALAMAT KIRIM" class="w-100 mt-4" ref="transval" v-model="formdata.alamat_kirim" /> 
            </vs-col>  
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
                <vs-input label="TELP KIRIM" class="w-100 mt-4" ref="transval" v-model="formdata.telp_kirim" /> 
            </vs-col>
            
          </vs-row>
          <vs-row>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="KREDIT TERM" class="w-100 mt-4" ref="transval" v-model="formdata.kredit_term" /> 
            </vs-col>  
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="KODE NO PAJAK" class="w-100 mt-4" ref="transval" v-model="formdata.kode_no_pajak" /> 
            </vs-col>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-select class="w-100 mt-4" label="SALES" ref="sales" v-model="formdata.sales" 
                    >
                    <vs-select-item
                        :key="index"
                        :value="item.sales_id"
                        :text="item.nama"
                        v-for="(item,index) in optsales"
                    />
                    </vs-select>
            </vs-col>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-select class="w-100 mt-4" label="MARKET" ref="market" v-model="formdata.market_id" 
                    >
                    <vs-select-item
                        :key="index"
                        :value="item.id"
                        :text="item.market"
                        v-for="(item,index) in optmarket"
                    />
                    </vs-select>
            </vs-col>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="PIC" class="w-100 mt-4" ref="transval" v-model="formdata.pic" /> 
            </vs-col>  
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="HP PIC" class="w-100 mt-4" ref="transval" v-model="formdata.hp_pic" /> 
            </vs-col>  
          </vs-row>
          <vs-row>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="KEUANGAN" class="w-100 mt-4" ref="transval" v-model="formdata.keuangan" /> 
            </vs-col>  
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="HP KEUANGAN" class="w-100 mt-4" ref="transval" v-model="formdata.hp_keuangan" /> 
            </vs-col>  
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="USER" class="w-100 mt-4" ref="transval" v-model="formdata.nama_user" /> 
            </vs-col>  
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="HP USER" class="w-100 mt-4" ref="transval" v-model="formdata.hp_user" /> 
            </vs-col>  
            <vs-col vs-lg="4" vs-xs="12" vs-sm="3">  
                <vs-input label="REKENING" class="w-100 mt-4" ref="transval" v-model="formdata.no_rekening" /> 
            </vs-col>  
          </vs-row>
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
            kode:'',
            nama:'',
            nick:'',
            alamat:'',
            kota:'',
            telepon:'',
            fax:'',
            email:'',
            npwp:'',
            alamat_kirim:'',
            telp_kirim:'',
            kredit_term:'0',
            kode_no_pajak:'',
            sales:'',
            market:'',
            market_id:'',
            pic:'',
            hp_pic:'',
            keuangan:'',
            hp_keuangan:'',
            nama_user:'',
            hp_user:'',
            no_rekening:'',
            isEdit: false,
            prevReferensi:'',
        },
        errors:[],
        optsales:[],
        optmarket:[],
        optkota:[],
        idtrans:0,
        inputUrl: '/api/customer'
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        getAllData: function(){
            var that = this
            this.$http.get('/api/refsales').then(response => that.optsales = response.data)
            this.$http.get('/api/refmarket').then(response => that.optmarket = response.data)
            this.$http.get('/api/refkota').then(response => that.optkota = response.data)
            //edit mode init data
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.inputUrl+"/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    //this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    console.log(keys)
                    var self = this
                    for(const key of keys){
                        //console.log(key)
                        if (this.formdata.hasOwnProperty(key))
                            this.formdata[key] = response.data[key]
                        if(key == 'id'){
                            console.log('kode masuk')
                            this.formdata.prevReferensi = response.data[key]
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
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            
            

            if(this.formdata.nama == ''){
                this.errors.push("Nama belum terisi...")
            }
            if(this.formdata.market_id == ''){
                this.errors.push("Market belum terisi...")
            }
            if(this.formdata.sales == ''){
                this.errors.push("Sales belum terisi...")
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
            this.$router.push({name : 'customerlist'});
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