<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Daftar Aset</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :virtualmode="true" :rendergridrows="rendergridrows"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Edit</li>
            </ul>
        </JqxMenu>
      </vs-card>
      <vs-popup fullscreen title="CETAK JURNAL" :active.sync="popupPdf">
        <iframe width="100%" height="515" :src="pdfsrc" frameborder="0" />
      </vs-popup>
    </vs-col>
    
   </vs-row>

</template>

<script>
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    import JqxWindow from "jqwidgets-scripts/jqwidgets-vue/vue_jqxwindow.vue";
    import JqxInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxinput.vue";
    import JqxNumberInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxnumberinput.vue";
    import JqxButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxbuttons.vue";
    import JqxMenu from "jqwidgets-scripts/jqwidgets-vue/vue_jqxmenu.vue";
    
  export default {
    components: {
        JqxGrid,
        JqxWindow,
        JqxInput,
        JqxNumberInput,
        JqxButton,
        JqxMenu
    },
    data: function () {
      return {
        // eslint-disable-next-line
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
        rendergridrows: (params) => {
          return params.data;
        },
        width: "100%",
        columns: [
          { text: 'Kode', datafield: 'kode', width: '8%' },
          { text: 'Kode Asset', datafield: 'kode_asset', width: '8%' },
          { text: 'Kelompok', datafield: 'nama_perk', width: '20%' },
          { text: 'Uraian', datafield: 'uraian', width: '40%' },
          { text: 'Harga Beli', datafield: 'harga_beli', width: '12%', cellsformat: 'd2', cellsalign: 'right' },
          { text: 'Nilai Buku', datafield: 'nilai_buku', width: '12%', cellsformat: 'd2', cellsalign: 'right'  }
        ],
        selectrow: 0,
        popupPdf: false,
        pdfsrc: ''
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        inputForm: function(){
            this.$router.push({name : 'aktivainput'});
        },
        myGridOnContextMenu: function () {
                return false;
        },
        myGridOnRowClick: function (event) {
            if (event.args.rightclick) {
                this.$refs.myGrid.selectrow(event.args.rowindex);
                let scrollTop = window.scrollY;
                let scrollLeft = window.scrollX;
                this.$refs.myMenu.open(parseInt(event.args.originalEvent.clientX) + 5 + scrollLeft, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);
                return false;
            }
        },
        myMenuOnItemClick: function (event) {
            let args = event.args;
            let rowindex = this.$refs.myGrid.getselectedrowindex();
            if (args.innerHTML == 'Edit') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                //console.log(dataRecord)
                let referensi = dataRecord.asset_id
                this.$router.push({name : 'aktivaedit', params: { ref: referensi }});
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'asset_id', type: 'number'},
          { name: 'nama_perk', type: 'string'},
          { name: 'kode_asset', type: 'string'},
          { name: 'kode', type: 'string'},
          { name: 'uraian', type: 'string'},
          { name: 'harga_beli', type: 'number'},
          { name: 'nilai_buku', type: 'number'},

        ],
        datatype: 'json',
        root: "Rows",
        cache: false,
        beforeprocessing: (data) => {
          console.log('total record')
          console.log(data)
          this.source.totalrecords = data.TotalRows;
        },
        filter: () => {
                    // update the grid and send a request to the server.
                    this.$refs.myGrid.updatebounddata("filter");
        },
        url: this.$urlbackend+'/api/aktivalist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      },
      this.editrow = -1;
    },
    created: function(){
        var self = this
        let processdata=function (data) {
            data.THN = window.$cookies.get('THN')
            data.jenis = self.jenisJurnal
            data.bpbmode = self.bpbmode
        }
        this.source.processdata = processdata
    }, 
    mounted: function () {
        document.addEventListener('contextmenu', event => event.preventDefault());
    },
    watch: {
        popupPdf: function(){
            if (this.popupPdf == false)
                this.pdfsrc = 'about:blank'
            else
                this.pdfsrc = this.pdfsrc + "#zoom=100"
        }
    }
  }
</script>

<style src='@/assets/styles/jqwidgets/jqx.base.css'></style>
<style src='@/assets/styles/jqwidgets/jqx.material-green.css'></style>


