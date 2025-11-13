<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Nomor FP Invoice : {{this.$route.params.ref}}</h3>
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
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-input label="Nama Customer" class="w-100 mt-4" v-model="formdata.customer" disabled/>  
            </vs-col>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-input label="Nomor PO" class="w-100 mt-4" v-model="formdata.po_cust"/>  
            </vs-col>
          </vs-row>
          <vs-row>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="4">  
                <vs-select class="w-100 mt-4" ref="rekanan" label="Akun Bank" v-model="formdata.bank_id">
                <vs-select-item
                    :key="index"
                    :value="item.bank_id"
                    :text="item.bank_name"
                    v-for="(item,index) in optbank"
                />
                </vs-select>
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
            customer:'',
            po_cust:'',
            bank_id:'',
            isEdit: false,
            inv_no:this.$route.params.ref,
            prevFp:'',
        },
        errors:[],
        optbank:[],
        
      }
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        getAllData: function(){
            this.$http.get('/api/masterbank').then(response => {
                //console.log(response.data);
                this.optbank = response.data
            }).catch(error=>{
                //console.log(error);
            });
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get("api/piutang/"+this.$route.params.ref).then(response => {
                    console.log(response.data)
                    //this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    console.log(keys)
                    var self = this
                    for(const key of keys){
                        //console.log(key)
                        if (this.formdata.hasOwnProperty(key))
                            this.formdata[key] = response.data[key]
                        if(key == 'no_fp'){
                            console.log('kode masuk')
                            console.log(response.data[key])
                            this.formdata.prevFp = response.data[key]
                        }
                        if(key == 'sales'){
                            this.formdata.po_cust = response.data[key]['po_cust']
                            this.formdata.customer = response.data[key]['qcustomer']['nama']
                        }
                            
                    }
                }).catch(error=>{
                    console.log(error)
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                })
                this.$http.get('/api/bppjurnal/'+this.$route.params.ref).then(response => {
                    //console.log(response.data);
                    this.bppjurnal = response.data;
                    this.formdata.datajurnal = response.data;
                }).catch(error=>{
                    //console.log(error);
                })
            }
        },
        checkForm:function(e) {
            this.errors = [];
            if(this.formdata.bank_id == ''){
                this.errors.push("Nomor FP belum dipilih...")
            }
           
            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post('/api/invoice_bank',{
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
            this.$router.push({name : 'invoicelist'});
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
        this.getAllData();
    },
  }
</script>
<style lang="stylus">
.cetak
  height: 90vh
</style>