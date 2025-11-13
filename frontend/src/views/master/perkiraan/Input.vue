<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Perkiraan {{this.$route.params.ref}}</h3>
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
            <h5 class="card-title mb-0">Pilih Kode Perkiraan Induk</h5>
            <JqxDropDownButton ref="myDropDownButton" :width="500" :height="25" >
                <JqxTree ref="myTree" :width="500" :height="220" :source="records" @select="select($event)"></JqxTree>
            </JqxDropDownButton>
          
          <vs-row>
            <vs-col vs-lg="2" vs-xs="12" vs-sm="3">  
                <vs-input label="KODE PERKIRAAN" disabled class="w-100 mt-4" ref="transval" v-model="formdata.kode_depan" /> 
            </vs-col>
            <vs-col vs-lg="1" vs-xs="12" vs-sm="3">  
                <vs-input label="-"  class="w-100 mt-4" ref="transval" v-model="formdata.kode_belakang" /> 
            </vs-col>
            <vs-col vs-lg="4" vs-xs="12" vs-sm="3">  
                <vs-input label="NAMA PERKIRAAN" class="w-100 mt-4" ref="transval" v-model="formdata.nama_perk" /> 
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
  import JqxTree from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxtree.vue';  
  import JqxDropDownButton from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxdropdownbutton.vue';
  export default {
    components: {
        JqxTree,
        JqxDropDownButton
    },
    data: function () {
      return {
        // eslint-disable-next-line
        formdata: {
            kode_depan:'',
            kode_belakang:'',
            nama_perk:'',
            kode_perk:'',
            level:'0',
            isEdit: false,
            prevReferensi:''
        },
        errors:[],
        idtrans:0,
        inputUrl: '/api/perkiraan'
      }
    },
    beforeCreate: function () {
      const source = {
        datafields: [
          { name: 'kode_perk', type: 'string'},
          { name: 'nama_perk', type: 'string'},
          { name: 'parent', type: 'string'},
          { name: 'level', type: 'number'},
        ],
        datatype: 'json',
        id: 'kode_perk',
        url: this.$urlbackend+'/api/perklist',
        type: 'get',
        async: false
      }
      const dataAdapter = new jqx.dataAdapter(source, { autoBind: true });
      this.records = dataAdapter.getRecordsHierarchy('kode_perk', 'parent', 'items', [{ name: 'nama_perk', map: 'label'},{ name: 'kode_perk', map: 'value'},{name: 'level', map: 'level'}]);
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        select: function (event) {
                const item = this.$refs.myTree.getItem(event.args.element);
                const dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 4px;">' + item.label + '</div>';
                this.formdata.kode_depan = item.value
                this.formdata.level = item.level + 1
                this.$refs.myDropDownButton.setContent(dropDownContent);
        },
        parseNama: function(nama){
            var parts = nama.split('+');
            return parts[0].replace(/=/g, '\u00a0') + parts[1];        
        },
        getAllData: function(){
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
                        if(key == 'kode_perk'){
                            this.formdata.prevReferensi = response.data[key]
                        }
                        if(key == 'parent'){
                            this.formdata.kode_depan = response.data[key]
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
            
            if(this.formdata.kode_depan == ''){
                this.errors.push("Kode Perkiraan Induk belum dipilih...")
            }

            if(this.formdata.kode_belakang == ''){
                this.errors.push("Kode Perkiraan belum terisi...")
            }

            if(this.formdata.nama_perk == ''){
                this.errors.push("Nama Perkiraan belum terisi...")
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
            this.$router.push({name : 'perkiraanlist'});
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
        'formdata.kode_belakang': function(){
            let kode = this.formdata.kode_belakang
            this.formdata.kode_perk = this.formdata.kode_depan + '.' +  kode
        },
        'formdata.kode_depan': function(){
            if(this.formdata.kode_perk != ''){
                let kode_depan = this.formdata.kode_depan
                let kode_perk = this.formdata.kode_perk
                let kode_belakang = kode_perk.substring(kode_depan.length+1)
                this.formdata.kode_belakang = kode_belakang
            }
            
        }
    },
    mounted: function () {
        this.getAllData();
        this.$refs.myDropDownButton.setContent('<div style="position: relative; margin-left: 3px; margin-top: 4px;">Kode Perkiraan</div>');
    },
  }
</script>
<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>