<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Proses Produksi</h3>
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
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
            :rowdetails="true" :initrowdetails="initRowDetails" :rowdetailstemplate="rowdetailstemplate"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="88" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Edit</li>
                <li>Batal</li>
            </ul>
        </JqxMenu>
        <br/>
        
        
        
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
          { text: 'Tgl Produksi', datafield: 'prod_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Tgl SO', datafield: 'so_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No Dok', datafield: 'prod_no', width: '20%' },
          { text: 'No PO', datafield: 'po_no', width: '20%' },
          { text: 'Customer', datafield: 'customer', width: '40%'}
        ],
        columns2: [
          { text: 'Kode Barang', datafield: 'kode_barang',  width: '30%', cellsformat: 'dd-MM-yyyy' },
          { text: 'Qty', datafield: 'qty', width: '70%' }
        ],
        
        rowdetailstemplate: {
            rowdetails: '<div id="nestedGrid" style="margin: 10px;"></div>', rowdetailsheight: 420, rowdetailshidden: true
        },
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: ''
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        quotationGridOnRowSelect: function(event) {
                let wo_no = event.args.row.id;
                let dataSource = {
                    datafields: [
                        { name: 'item_id', type: 'string' },
                        { name: 'kode_barang', type: 'string' },
                        { name: 'qty', type: 'number' },
                        { name: 'wo_id', type: 'number' }
                    ],
                    datatype: 'json',
                    data:{ wo_id: wo_no },
                    id: 'id',
                    url: this.$urlbackend+'/api/woitem',
                    type: 'post',
                }
                let adapter = new jqx.dataAdapter(dataSource , { 
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
                this.$refs.itemGrid.source = adapter;
                //this.$refs.historyGrid.updatebounddata()
        },
        initRowDetails: function (index, parentElement, gridElement, record) {
            let nestedGridContainer = parentElement.children[0];
            this.nestedGrids[index] = nestedGridContainer;
            let ordersSource = {
                datafields: [
                    { name: 'item_id', type: 'string' },
                    { name: 'kode_barang', map: 'itembarang>kode_barang' },
                    { name: 'size', map: 'detailquot>size' },
                    { name: 'qty', type: 'number'},
                    
                ],
                datatype: 'json',
                id: 'id',
                data:{ prod_id: record.id},
                url: this.$urlbackend+'/api/produksidet',
                type: 'post',
            }
            let nestedGridAdapter = new jqx.dataAdapter(ordersSource,{ 
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
            if (nestedGridContainer != null) {
                let settings = {
                    width: 800,
                    height: 300,
                    source: nestedGridAdapter,
                    columns: [
                        { text: 'Kode Barang', datafield: 'kode_barang', width: '60%' },
                        { text: 'Size', datafield: 'size', width: '35%' },
                        { text: 'Qty', datafield: 'qty', width: '15%' },
                    ]
                };
                jqwidgets.createInstance(`#${nestedGridContainer.id}`, 'jqxGrid', settings);
            }
        },
        
        inputForm: function(){
            this.$router.push({name : 'produksiinput'});
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
            if (args.innerHTML == 'Batal') {
                this.editrow = rowindex;
                let rowid = this.$refs.myGrid.getrowid(rowindex)
                let data = this.$refs.myGrid.getrowdatabyid(rowid)
                if(confirm("Yakin menghapus data?")){
                        this.$http.delete('/api/produksi/' + data.id).then(resp => {
                            console.log(resp)
                            this.$refs.myGrid.deleterow(rowid);
                            this.$vs.notify({title:'BERHASIL..',text:'Data berhasil dihapus..',color:'danger'})
                        }).catch(error => {
                            console.log(error);
                            this.$vs.notify({title:'GAGAL..',text:'Gagal hapus data..',color:'danger'})
                        })
                    }
            }
            else if (args.innerHTML == 'Edit') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                let referensi = dataRecord.prod_no;
                this.$router.push({name : 'produksiedit', params: { ref: referensi }});
            }
            
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'prod_no', type: 'string'},
          { name: 'wo_no', type: 'string'},
          { name: 'id', type: 'number'},
          { name: 'prod_tgl', type: 'date'},
          { name: 'customer', map: 'work>quotation>qcustomer>nick'},
          { name: 'po_no', map: 'work>quotation>po_cust'},
          { name: 'so_tgl', map: 'work>quotation>so_tgl', type:'date'},
          { name: 'id', type: 'number'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/produksilist',
        type: 'post',
        deleterow: function (rowid, commit) {
            commit(true);
        }
      }
      this.nestedGrids = [];
      this.nestedGrids2 = [];
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

