<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Nomor Faktur Pajak</h3>
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
          <vs-col vs-lg="3" vs-xs="12" vs-sm="3">  
            <vs-input label="Kode Depan (ex : 005.22)" class="w-100 mt-4" v-model="formdata.kode_depan"/>  
          </vs-col>
          <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
            <vs-input label="No Seri Awal" class="w-100 mt-4" v-model="formdata.no_awal"/>  
          </vs-col>
          <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
            <vs-input label="No Seri Akhir" class="w-100 mt-4" v-model="formdata.no_akhir"/>  
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
  export default { 
    data: function () {
      return {
        // eslint-disable-next-line
        locale: 'de',
        currency: null,
        formdata: {
            kode_depan:'',
            no_awal:'',
            no_akhir:'',
            isEdit: false,
            prevReferensi:this.$route.params.ref,
        },
        errors:[],
        
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            if(this.formdata.kode_depan == ''){
                this.errors.push("Kode Depan belum terisi...")
            }
            if(this.formdata.no_awal == ''){
                this.errors.push("Nomor awal belum terisi...")
            }
            if(this.formdata.no_akhir == ''){
                this.errors.push("Nomor akhir belum terisi...")
            }
            if(this.formdata.no_akhir <= this.formdata.no_awal){
                this.errors.push("Nomor akhir harus lebih besar dari nomor awal...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/nomorfp',{
                    formdata : this.formdata
                }).then(response => {
                    this.handleSuccess();
                    //
                }).catch(error=>{
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
            this.$router.push({name : 'nofplist'});
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
    mounted: function () {
        //this.getAllData();
    },
  }
</script>
<style lang="stylus">
.cetak
  height: 90vh
</style>