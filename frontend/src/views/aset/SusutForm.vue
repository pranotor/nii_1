<template>
    <vs-row vs-justify="center">
        <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12" vs-sm="12">
        <!--
            /////////////////
            Month view only
            /////////////////
        -->
        <vs-card>
            <h3 class="card-title d-flex">Proses Penyusutan</h3>
            <p class="card-subtitle"></p>
            <h3></h3>
            <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
                <b>Terdapat Kesalahan..</b>
                <ul class="common-list">
                <li v-for="error in errors" :key="error">{{ error }}</li>
                </ul>
            </vs-alert>
            <date-picker v-model="formdata.bulan" 
            value-type="format" type="month" placeholder="Pilih Bulan" :disabled-date="disabledBeforeLastTrans">
            </date-picker>

            <div class="btn-alignment mt-4">
                <vs-button color="success" type="filled" @click.stop.prevent="checkForm">Proses</vs-button>
            </div>
        </vs-card>
        </vs-col>
        
    </vs-row>
    
</template>

<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    components: {
        DatePicker
    },
    data:() => ({
       
        formdata: {
           bulan : '',
        },
        jurnalUrl : '/api/aktivasusut',
        bulanTrans:'0',
        errors:[],
       
    }),
    methods: {
        disabledBeforeLastTrans(date) {
            return date.getMonth() < this.bulanTrans
        },
        getAllData: function(){
            this.$http.post('/api/referensi',{
                jenis : 'susut',
                tglJurnal: window.$cookies.get('THN'),
            }).then(response => {
                this.bulanTrans = response.data
            }).catch(error=>{
                console.log(error)
            })
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            if(this.formdata.bulan == ''){
                this.errors.push("Bulan belum terisi...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post(this.jurnalUrl,{
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
                        case 417 :
                            this.errors.push(error.response.data.error)
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
            this.$router.push({name : 'aktivasusut'});
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


<style>

</style>