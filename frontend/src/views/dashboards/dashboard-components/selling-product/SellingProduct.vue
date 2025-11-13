<template>
  <div>
    <div class="table-responsive">
      <table class="table v-middle border text-nowrap">
        <thead class="bg-light">
          <tr class>
            <th class="border-top-0">Products</th>
            <th class="border-top-0">License</th>
            <th class="border-top-0">Support Agent</th>
            <th class="border-top-0">Technology</th>
            <th class="border-top-0">Tickets</th>
            <th class="border-top-0">Sales</th>
            <th class="border-top-0">Earnings</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar color="primary" text="Erimary" />
                </div>
                <div class>
                  <h5 class="m-b-0">Elite Admin</h5>
                </div>
              </div>
            </td>
            <td>Single Use</td>
            <td>John Doe</td>
            <td>
              <label class="rounded-0 badge badge-danger">Angular</label>
            </td>
            <td>46</td>
            <td>356</td>
            <td>
              <h5 class="m-b-0">$2850.06</h5>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar color="danger" text="Mrimary" />
                </div>
                <div class>
                  <h5 class="m-b-0">Monster Admin</h5>
                </div>
              </div>
            </td>
            <td>Single Use</td>
            <td>Venessa Fern</td>
            <td>
              <label class="rounded-0 badge badge-info">Vue Js</label>
            </td>
            <td>46</td>
            <td>356</td>
            <td>
              <h5 class="m-b-0">$2850.06</h5>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar color="warning" text="Primary" />
                </div>
                <div class>
                  <h5 class="m-b-0">Material Pro Admin</h5>
                </div>
              </div>
            </td>
            <td>Single Use</td>
            <td>John Doe</td>
            <td>
              <label class="rounded-0 badge badge-success">Bootstrap</label>
            </td>
            <td>46</td>
            <td>356</td>
            <td>
              <h5 class="m-b-0">$2850.06</h5>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar color="success" text="Arimary" />
                </div>
                <div class>
                  <h5 class="m-b-0">Ample Admin</h5>
                </div>
              </div>
            </td>
            <td>Single Use</td>
            <td>John Doe</td>
            <td>
              <label class="rounded-0 badge badge-warning">React</label>
            </td>
            <td>46</td>
            <td>356</td>
            <td>
              <h5 class="m-b-0">$2850.06</h5>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
export default {
  name: "SellingProduct",
  data: () => ({
    title: "SellingProduct"
  })
};
</script>