<template>
    <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>  
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Customer Relation Activity</h3>
        <div class="ml-auto">
          <vs-button @click="inputForm"  color="success" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Input Data
          </vs-button>
        </div> 
        </div>
        <hr class="custom-hr" />  
        <JqxGrid ref="customersGrid" @rowselect="customersGridOnRowSelect($event)"
                 :width="getWidth" :height="250" :source="dataAdapter"
                 :keyboardnavigation="false" :columns="columns">
        </JqxGrid>
        <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Activity</h3>
        </div>
        <JqxGrid ref="ordersGrid"
                 :width="getWidth" :height="250"
                 :keyboardnavigation="false" :columns="columns2">
        </JqxGrid>
      </vs-card>
    </vs-col>
   </vs-row>
</template>
<script>
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    export default {
        components: {
            JqxGrid
        },
        data: function () {
            return {
                getWidth: "100%",
                dataAdapter: new jqx.dataAdapter(this.source),
                columns: [
                    { text: 'Company Name', datafield: 'CompanyName', width: 250 },
                    { text: 'Contact Name', datafield: 'ContactName', width: 150 },
                    { text: 'Contact Title', datafield: 'ContactTitle', width: 180 },
                    { text: 'City', datafield: 'City', width: 120 },
                    { text: 'Country', datafield: 'Country' }
                ],
                columns2: [
                    { text: 'Act Date', datafield: 'ActDate', cellsformat: 'd', width: 150 },
                    { text: 'Type', datafield: 'ActType', cellsformat: 'd', width: 150 },
                    { text: 'Explication', datafield: 'explication' }
                ]
            }
        },
        beforeCreate: function () {
            this.source = {
                datafields: [
                    { name: 'CustomerID' },
                    { name: 'CompanyName' },
                    { name: 'ContactName' },
                    { name: 'ContactTitle' },
                    { name: 'Address' },
                    { name: 'City' },
                    { name: 'Country' }
                ],
                localdata: [
                    { 'CustomerID': 'AAA', 'CompanyName': 'PT MAJU TERUS', 'ContactName': 'Siti Maimunah', 'ContactTitle': 'Sales Representative', 'City': 'Bandung', 'Country': 'Indonesia' },
                    { 'CustomerID': 'BBB', 'CompanyName': 'PT HEBAT', 'ContactName': 'Asmidi', 'ContactTitle': 'Owner', 'City': 'Jakarta', 'Country': 'Indonesia' },
                    { 'CustomerID': 'CCC', 'CompanyName': 'Moreno Taquera', 'ContactName': 'Antonio Moreno', 'ContactTitle': 'Manager', 'City': 'Mxico D.F.', 'Country': 'Mexico' }
                ]
            };
            this.dataFields2 = [
                { name: 'CustomerID' },
                { name: 'ActDate', type: 'date'  },
                { name: 'ActType' },
                { name: 'explication' }
            ];
            const source2 = {
                datafields: this.dataFields2,
                localdata: [
                    { 'OrderID': 10248, 'CustomerID': 'AAA', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Phone Call' },
                    { 'OrderID': 10248, 'CustomerID': 'AAA', 'ActDate': '2021-10-3 13:00:00', 'ActType': 'Phone Call' },
                    { 'OrderID': 10248, 'CustomerID': 'AAA', 'ActDate': '2021-10-12 13:00:00', 'ActType': 'Quotation' },
                    { 'OrderID': 10248, 'CustomerID': 'BBB', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Phone Call' },
                    { 'OrderID': 10248, 'CustomerID': 'BBB', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Visit' },
                    { 'OrderID': 10248, 'CustomerID': 'BBB', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Phone Call' },
                    { 'OrderID': 10248, 'CustomerID': 'CCC', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Phone Call' },
                    { 'OrderID': 10248, 'CustomerID': 'CCC', 'ActDate': '2021-10-1 13:00:00', 'ActType': 'Sales Order' },
                    
                ]
            }
            this.dataAdapter2 = new jqx.dataAdapter(source2, { autoBind: true });
        },
        methods: {
            inputForm: function(){
                this.$router.push({name : 'activityinput'});
            },
            customersGridOnRowSelect: function(event) {
                let customerID = event.args.row.CustomerID;
                let records = new Array();
                let dataAdapter = this.dataAdapter2;
                let length = dataAdapter.records.length;
                for (let i = 0; i < length; i++) {
                    let record = dataAdapter.records[i];
                    if (record.CustomerID == customerID) {
                        records[records.length] = record;
                    }
                }
                let dataSource = {
                    datafields: this.dataFields2,
                    localdata: records
                }
                let adapter = new jqx.dataAdapter(dataSource);
                this.$refs.ordersGrid.source = adapter;
            }
        }
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