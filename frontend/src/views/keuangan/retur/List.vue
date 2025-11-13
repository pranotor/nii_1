<template>
   <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Retur SJ </h3>
        <div class="ml-auto">
          
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="myGrid" :autorowheight="true" :autoheight="true"
            @rowselect="sjGridOnRowSelect($event)" 
            :width="width" :theme="'material-green'" :selectionmode="'singlerow'" :ready="ready"
            :pageable="true" :sortable="true" :filterable="true" :showfilterrow="true" :columnsresize="true"
            :source="dataAdapter" :columns="columns" @contextmenu="myGridOnContextMenu()" @rowclick="myGridOnRowClick($event)"
        />
        <JqxMenu ref="myMenu" @itemclick="myMenuOnItemClick($event)"
                 :width="200" :height="30" :mode="'popup'" :autoOpenPopup="false">
            <ul>
                <li>Retur Barang</li>
            </ul>
        </JqxMenu>
        <br/>
        <h3>SJ Detail</h3>
        <JqxGrid ref="detailGrid"
                 :width="width" :height="250"
                 :keyboardnavigation="false" :columns="columns3">
                  
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
    import qz from "qz-tray";
    const FileDownload = require('js-file-download');

    
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
          { text: 'Tgl', datafield: 'sj_tgl', filtertype: 'range', width: '10%', cellsformat: 'dd-MM-yyyy' },
          { text: 'No Invoice', datafield: 'inv_no', width: '15%' },
          { text: 'No SJ', datafield: 'sj_no', width: '15%' },
          { text: 'No PO', datafield: 'po_no', width: '15%' },
          { text: 'Customer', datafield: 'customer', width: '25%'},
          { text: 'Nick', datafield: 'nick', width: '20%'}
        ],
        columns3: [
          { text: 'Kode Barang', datafield: 'kode_barang', width: '10%'},
          { text: 'Kode 2', datafield: 'kode2', width: '20%'},
          { text: 'Size', datafield: 'size', width: '60%' },
          { text: 'Qty', datafield: 'qty_kirim', width: '10%' },        
        ],
        selectrow: 0,
        jenis: 'po',
        popupPdf: false,
        pdfsrc: '',
        dataCetak:[],
      }
    },
    methods: {
        ready: function() {
            this.$refs.myGrid.selectrow(0);
            this.$refs.myGrid.focus();
        },
        convertFp: function(){
            this.$router.push({name : 'convertFp'});
        },
        inputForm: function(){
            this.$router.push({name : 'quotationinput'});
        },
        sjGridOnRowSelect: function(event) {
                let sj_id = event.args.row.id;
                let dataSource2 = {
                    datafields2: [
                        { name: 'kode_barang', type: 'string'},
                        { name: 'kode2', type: 'string'},
                        { name: 'size', type: 'string'},
                        { name: 'qty_kirim', type: 'number'},
                    ],
                    datatype: 'json',
                    data:{ sj_id: sj_id },
                    id: 'id',
                    url: this.$urlbackend+'/api/sjdet',
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
            if (args.innerHTML == 'Retur Barang') {
                this.editrow = rowindex;
                let dataRecord = this.$refs.myGrid.getrowdata(this.editrow)
                //console.log(dataRecord)
                let referensi = dataRecord.sj_no
                //cek apakah sudah posting
                this.$router.push({name : 'returinput', params: { ref: referensi }});
            }
        }
    },
    beforeCreate: function () {
      this.source = {
        datafields: [
          { name: 'id', type: 'number'},
          { name: 'inv_no', type: 'string'},
          { name: 'sj_tgl', type: 'date'},
          { name: 'customer', map: 'sales>qcustomer>nama'},
          { name: 'nick', map: 'sales>qcustomer>nick'},
          { name: 'po_no', map: 'sales>po_cust'},
          { name: 'sj_no', type:'string'},
          { name: 'p', type:'string'},
        ],
        datatype: 'json',
        id: 'referensi',
        url: this.$urlbackend+'/api/invoicinglist',
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

