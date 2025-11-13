<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Role {{this.$route.params.ref}}</h3>
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
                <vs-input label="NAMA ROLE" class="w-100 mt-4" ref="transval" v-model="formdata.role_name" /> 
            </vs-col>
          </vs-row>
          <hr/>
          <vs-row>
            <vs-col vs-lg="6" vs-xs="12" vs-sm="3">
            <h5 class="card-title mb-0">Pilih Hak Akses Menu</h5>    
            <JqxTree ref="myTree" :width="500" :height="220" :source="records" :hasThreeStates="true" :checkboxes="true" @checkChange="onCheckChange($event)"></JqxTree>
            </vs-col>
            <vs-col vs-lg="3" vs-xs="12" vs-sm="3">
                <div height="500px">
                    &nbsp;
                </div>
                <vs-button @click="syncmenu" color="primary" type="filled" class="mt-100" icon="highlight" v-focus>  
                    Sync
                </vs-button>
            </vs-col>
          </vs-row>
        </div>
        <hr/>
        <div>            
            <table width="90%" class="p-10" border="1">
                <tr>
                    <th width="50%">Nama Menu</th>
                    <th width="15%" align="center">Tambah</th>
                    <th width="15%">Edit</th>
                    <th width="15%">Hapus</th>
                </tr>
                <tr :key="item.value" v-for="item in formdata.datatrans">
                    <td height="30px">{{item.label}}</td>
                    <td align="center"><vs-switch v-model="item.create_auth" ref="dk" class="p-10" v-if="item.parentId!=0">
                            <span slot="on">Yes</span>
                            <span slot="off">No</span>
                        </vs-switch>
                    </td>
                    <td align="center"><vs-switch v-model="item.edit_auth" ref="dk" class="p-10" v-if="item.parentId!=0">
                            <span slot="on">Yes</span>
                            <span slot="off">No</span>
                        </vs-switch>
                    </td>
                    <td align="center"><vs-switch v-model="item.delete_auth" ref="dk" class="p-10" v-if="item.parentId!=0">
                            <span slot="on">Yes</span>
                            <span slot="off">No</span>
                        </vs-switch>
                    </td>
                </tr>
            </table>
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
            datatrans:[],
            role_name:'',
            isEdit: false,
            prevReferensi:''
        },
        menuitems:[],
        dk: true,
        errors:[],
        idtrans:0,
        inputUrl: '/api/role'
      }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'name', type: 'string'},
          { name: 'parent', type: 'number'},
          { name: 'checked', type: 'boolean', map:'checked_menu>checked'},
        ],
        datatype: 'json',
        id: 'kode_perk',
        url: this.$urlbackend+'/api/menulist',
        type: 'post',
        async: false
      }
      let self = this
      let processdata=function (data) {
            data.role_id = self.$route.params.ref
      }
      this.source.processdata = processdata
      const dataAdapter = new jqx.dataAdapter(this.source, { autoBind: true,formatData: function (data) {
                return  JSON.stringify(data);
            },
            beforeSend: function (jqxhr, settings) { 
                settings.xhrFields = {
                    withCredentials: true
                },
                jqxhr.setRequestHeader("X-XSRF-TOKEN",window.$cookies.get('XSRF-TOKEN'))
            } });
      this.records = dataAdapter.getRecordsHierarchy('id', 'parent', 'items', [{ name: 'name', map: 'label'},{ name: 'wewenang', map: 'value'}]);
    },
    methods:{
        backpage: function(){
            this.$router.go(-1);
        },
        onCheckChange: function (event) {
            let items = this.$refs.myTree.getCheckedItems();
            this.menuitems = items
        },
        syncmenu: function(){
            this.formdata.datatrans = this.menuitems.map(({ id, parentId, label }) => ({id, parentId, label,create_auth:true,edit_auth:true,delete_auth:true}));
        },
        getAllData: function(){
            //edit mode init data
            let items = this.$refs.myTree.getCheckedItems();
            console.log('test items')
            console.log(items)
            this.menuitems = items
            if(this.$route.params.ref != null){
                this.formdata.isEdit = true
                this.$http.get(this.inputUrl+"/"+this.$route.params.ref).then(response => {
                    //console.log(response.data)
                    //this.formdata.datatrans = response.data;
                    const keys = Object.keys(response.data)
                    //console.log(keys)
                    var self = this
                    for(const key of keys){
                        //console.log(key)
                        if (this.formdata.hasOwnProperty(key))
                            this.formdata[key] = response.data[key]
                        if(key == 'id'){
                            this.formdata.prevReferensi = response.data[key]
                        }
                        if(key == 'wewenang'){
                            let dataWewenang = response.data[key]
                            dataWewenang.forEach(item => {
                                let data = {}
                                data.id = item.menu_id
                                data.parentId = item.menudetail.parent
                                data.label = item.menudetail.name
                                data.create_auth = item.create_auth
                                data.delete_auth = item.delete_auth
                                data.edit_auth = item.edit_auth
                                this.formdata.datatrans.push(data)
                            })
                        }
                            
                    }
                }).catch(error=>{
                    //console.log(error)
                    this.$vs.notify({title:'Error',text:'Gagal inisiasi data, silahkan coba lagi...',color:'danger'})
                });
            }
            //edit mode init data
            this.formdata.pemohon_id = this.$store.getters.getUserId
            
        },
        checkForm:function(e) {
            this.errors = [];
            //cek dari client dulu...
            
            if(this.formdata.role_name == ''){
                this.errors.push("Nama Role belum diisi...")
            }

            if(this.errors.length > 0){
                this.handleError(this.error,'local')
            }
            else{
                this.$vs.loading({'type':'radius'})
                this.$http.post(this.inputUrl,{
                    formdata : this.formdata
                }).then(response => {
                    //console.log(response);
                    this.handleSuccess();
                    //
                }).catch(error=>{
                    //console.log(error.response)
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
            this.$router.push({name : 'rolelist'});
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
    },
  }
</script>
<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>