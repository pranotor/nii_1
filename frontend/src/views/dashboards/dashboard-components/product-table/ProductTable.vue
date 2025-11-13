<template>
  <div>
    <div v-bar class="vs-scrollable" style="height: 393px;">
      <div>
        <vs-table :data="producttable" class="text-nowrap">
          <template slot="thead">
            <vs-th>Products</vs-th>
            <vs-th>Customers</vs-th>
            <vs-th>Status</vs-th>
            <vs-th>Invoice</vs-th>
            <vs-th>Amount</vs-th>
          </template>

          <template slot-scope="{data}">
            <vs-tr class="border-top" :data="tr" :key="indextr" v-for="(tr, indextr) in data">
              <vs-td>
                <div class="d-flex no-block align-items-center">
                  <div class="mr-2">
                    <img
                      :src="require('@/assets/images/product'+ data[indextr].img)"
                      alt="user"
                      class="rounded-circle"
                      width="45"
                    />
                  </div>
                  <div class>
                    <h5 class="mb-0 font-16 font-medium">{{ data[indextr].productname }}</h5>
                  </div>
                </div>
              </vs-td>
              <vs-td>{{ data[indextr].producttext }}</vs-td>
              <vs-td>
                <div class="d-flex align-items-center">
                  <vs-tooltip :text="data[indextr].statustext" :color="data[indextr].statuscolor">
                    <i class="mdi mdi-checkbox-blank-circle" :class="[data[indextr].status]"></i>
                  </vs-tooltip>
                </div>
              </vs-td>
              <vs-td>{{ data[indextr].invoice }}</vs-td>
              <vs-td>
                <span class="font-medium">{{ data[indextr].amount }}</span>
              </vs-td>
            </vs-tr>
          </template>
        </vs-table>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "ProductTable",
  data: () => ({
    title: "ProductTable",
    // Data for Product Table
    producttable: [
      {
        img: "/chair.png",
        productname: "Orange Shoes",
        producttext: "Rotating Chair",
        statustext: "In Progress",
        statuscolor: "warning",
        status: "text-warning",
        invoice: "35",
        amount: "$96K"
      },
      {
        img: "/chair2.png",
        productname: "Red Sandle",
        producttext: "Dummy Product",
        statustext: "Active",
        statuscolor: "success",
        status: "text-success",
        invoice: "35",
        amount: "$96K"
      },
      {
        img: "/chair3.png",
        productname: "Gourgeous Purse",
        producttext: "Comfortable Chair",
        statustext: "Active",
        statuscolor: "success",
        status: "text-success",
        invoice: "35",
        amount: "$96K"
      },
      {
        img: "/chair4.png",
        productname: "Puma T-shirt",
        producttext: "Wooden Chair",
        statustext: "In Progress",
        statuscolor: "warning",
        status: "text-warning",
        invoice: "35",
        amount: "$96K"
      }
    ]
  })
};
</script>