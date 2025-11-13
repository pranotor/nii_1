<template>
  <div>
    <div class="table-responsive">
      <table class="table browser mb-0 no-border">
        <tbody>
          <tr>
            <td class="border-0" style="width:40px">
              <img src="@/assets/images/browser/chrome-logo.png" alt="logo" />
            </td>
            <td class="border-0">Google Chrome</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-info text-white">23%</span>
            </td>
          </tr>
          <tr>
            <td class="border-0">
              <img src="@/assets/images/browser/firefox-logo.png" alt="logo" />
            </td>
            <td class="border-0">Mozila Firefox</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-success">15%</span>
            </td>
          </tr>
          <tr>
            <td class="border-0">
              <img src="@/assets/images/browser/safari-logo.png" alt="logo" />
            </td>
            <td class="border-0">Apple Safari</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-primary">07%</span>
            </td>
          </tr>
          <tr>
            <td class="border-0">
              <img src="@/assets/images/browser/internet-logo.png" alt="logo" />
            </td>
            <td class="border-0">Internet Explorer</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-warning">09%</span>
            </td>
          </tr>
          <tr>
            <td class="border-0">
              <img src="@/assets/images/browser/opera-logo.png" alt="logo" />
            </td>
            <td class="border-0">Opera mini</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-danger">23%</span>
            </td>
          </tr>
          <tr>
            <td class="border-0">
              <img src="@/assets/images/browser/internet-logo.png" alt="logo" />
            </td>
            <td class="border-0">Microsoft edge</td>
            <td class="border-0" align="right">
              <span class="badge-pill badge badge-primary">09%</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
export default {
  name: "BrowseStats",
  data: () => ({
    title: "BrowseStats"
  })
};
</script>