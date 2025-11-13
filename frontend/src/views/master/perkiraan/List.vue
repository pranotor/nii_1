<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Perkiraan</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxTreeGrid ref="myGrid"
            :width="width"
            :sortable="true"
            :ready="ready"
            :columns="columns"
            :source="dataAdapter" @contextmenu="myTreeGridOnContextmenu()" 
                     @rowClick="myTreeGridOnRowClick($event)">
        </JqxTreeGrid>
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="58" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li id="edit">Edit</li>
            </ul>
        </JqxMenu>
      </vs-card>
    </vs-col>
   </vs-row>
</template>

<script>
    import JqxTreeGrid from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxtreegrid.vue';
    import JqxMenu from "jqwidgets-scripts/jqwidgets-vue/vue_jqxmenu.vue";
    
  export default {
    components: {
        JqxTreeGrid,
        JqxMenu
    },
    data: function () {
      return {
        // eslint-disable-next-line
        /* eslint-disable no-console */

        dataAdapter: new jqx.dataAdapter(this.source, {
            formatData: function (data) {
                return  JSON.stringify(data);
            },
            beforeSend: function (jqxhr, settings) { 
                settings.xhrFields = {
                    withCredentials: true
                },
                jqxhr.setRequestHeader("X-XSRF-TOKEN",window.$cookies.get('XSRF-TOKEN'))
            }
        }),
        width: "100%",
        columns: [
          { text: 'Kode Perkiraan', datafield: 'kode_perk', width: '20%', },
          { text: 'Uraian', datafield: 'nama_perk', width: '79%', }
        ],
        selectrow: 0,
        routeEdit: 'perkiraanedit'
      }
    },
    methods: {
        ready: function() {
            document.addEventListener('contextmenu', event => event.preventDefault());
        },
        inputForm: function(){
            this.$router.push({name : 'perkiraaninput'});
        },
         myTreeGridOnContextmenu: function () {
                return false;
        },
        myTreeGridOnRowClick: function(event) {
            let args = event.args;
            if (args.originalEvent.button == 2) {
                let scrollTop = window.scrollX;
                let scrollLeft = window.scrollY;
                this.$refs.myMenu.open(parseInt(event.args.originalEvent.clientX) + 5 + scrollLeft, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);
                return false;
            }
        },
        myGridOnCellClick: function (event) {
            if (event.args.column.datafield=='Edit') {
                this.$refs.myGrid.selectrow(event.args.rowindex);
                let scrollTop = window.scrollY;
                const left = this.$refs.myMenu.width + 10;
                   
                this.$refs.myMenu.open(parseInt(event.args.originalEvent.clientX) - left, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);
                return false;
            }
        },
        myMenuOnItemClick: function (event) {
            let args = event.args;
            let rowindex = this.$refs.myGrid.getSelection();
            console.log(rowindex)
            if (args.innerHTML == 'Edit') {
                let referensi = rowindex[0].kode_perk
                this.$router.push({name : this.routeEdit, params: { ref: referensi }});
            }
            else {
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                console.log(data)
                if(confirm("Yakin menghapus data?")){
                    this.$http.delete('/api/ajuan/' + data.ref).then(resp => {
                        console.log(resp)
                        this.$refs.myGrid.deleterow(rowid);
                        this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                    }).catch(error => {
                        console.log(error);
                        this.$vs.notify({title:'GAGAL..',text:'Gagal hapus data..',color:'danger'})
                    })
                }
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'kode_perk', type: 'string'},
          { name: 'nama_perk', type: 'string'},
          { name: 'parent', type: 'string'},
        ],
        datatype: 'json',
        id: 'kode_perk',
        hierarchy:
            {
                keyDataField: { name: 'kode_perk' },
                parentDataField: { name: 'parent' }
            },
        url: this.$urlbackend+'/api/perklist',
        type: 'get',
        deleterow: function (rowid, commit) {
            commit(true);
        },
      }
      this.editrow = -1;
    },
    created: function(){
        var self = this
        let processdata=function (data) {
            data.THN = window.$cookies.get('THN')
            data.jenis = self.jenisJurnal
        }
        this.source.processdata = processdata
    }, 
    mounted: function () {
        document.addEventListener('contextmenu', event => event.preventDefault());
    },
  }
</script>

<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style>
.jqx-grid-cell input[type="button"]{
    width:100%;
    z-index:10;
    opacity:0.01!important;
    z-index:5!important;
    display:block!important;
}
</style>
