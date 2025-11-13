<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Retur Pembelian (BPB)</h3>
        <div class="ml-auto">
          
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            @rowselect="bpbGridOnRowSelect($event)" 
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="30" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Retur Pembelian</li>
            </ul>
        </JqxMenu>
        <br/>
        <h3>BPB Detail</h3>
        <JqxGrid ref="detailGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columnsDetail">
                  
        </JqxGrid>
      </vs-card>
      <vs-popup fullscreen title="CETAK" :active.sync="popupPdf">
        <div class="cetak">  
            <iframe width="100%" height="100%" :src="pdfsrc" frameborder="0" />
        </div>
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
        width: "100%",
        columns: [
          { text: 'Tgl', datafield: 'bpb_tgl', filtertype: 'range', width: '15%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No BPB', datafield: 'bpb_no', width: '30%' },
          { text: 'No PO', datafield: 'po_no', width: '30%'},
          { text: 'Status', datafield: 'posting', width: '25%'}
        ],
        columnsDetail: [
          { text: 'Kode Barang', datafield: 'kode_barang', width: '40%'},
          { text: 'Qty Pesan', datafield: 'qty_pesan', width: '15%' },        
          { text: 'Qty Terima', datafield: 'qty_terima', width: '15%' },      
          { text: 'Harga', datafield: 'harga', width: '15%', cellsformat: 'D0' },        
          { text: 'PPN', datafield: 'ppn', width: '15%', cellsformat: 'D2' },        
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
        myGridOnContextMenu: function () {
                return false;
        },
        bpbGridOnRowSelect: function(event) {
                let bpb_id = event.args.row.id;
                let dataSource2 = {
                    datafields2: [
                        { name: 'kode_barang', type: 'string'},
                        { name: 'qty_pesan', type: 'number'},
                        { name: 'qty_terima', type: 'number'},
                        { name: 'harga', type: 'number'},
                        { name: 'ppn', type: 'number'}
                    ],
                    datatype: 'json',
                    data:{ bpb_id: bpb_id },
                    id: 'id',
                    url: this.$urlbackend+'/api/bpbdet',
                    type: 'post',
                }
                let adapter2 = new jqx.dataAdapter(dataSource2 , { 
                    formatData: function (data) {
                            return  JSON.stringify(data);
                    },
                    beforeSend: function (jqxhr, settings) { 
                        settings.xhrFields = {
                            withCredentials: true
                        },
                        jqxhr.setRequestHeader("X-XSRF-TOKEN",window.$cookies.get('XSRF-TOKEN'))
                    }
                });
                this.$refs.detailGrid.source = adapter2;
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
            if (args.innerHTML == 'Retur Pembelian') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let bpbId = dataRecord.id
                let bpbNo = dataRecord.bpb_no
                this.$router.push({ name: 'returbeliinput', params: { id: bpbId }, query: { ref: bpbNo } })
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'bpb_no', type: 'string'},
          { name: 'bpb_tgl', type: 'date'},
          { name: 'po_no', type:'string'},
          { name: 'posting', type:'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/bpblist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      }
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
<style lang="stylus">
.cetak
  height: 90vh
</style>
