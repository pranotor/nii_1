<template>
  <div>
    <div class="table-responsive">
      <table class="table v-middle border text-nowrap">
        <thead class="bg-light">
          <tr class>
            <th class="border-top-0">Assigned</th>
            <th class="border-top-0">Name</th>
            <th class="border-top-0">Priority</th>
            <th class="border-top-0">Budget</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="primary" text="S" />
                </div>
                <div class>
                  <h6>Sunil Joshi</h6>
                  <small class="text-muted">Web Designer</small>
                </div>
              </div>
            </td>
            <td>Elite Admin</td>
            <td>
              <label class="rounded-0 badge badge-danger">Low</label>
            </td>
            <td>$3.9K</td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="danger" text="Mrimary" />
                </div>
                <div class>
                  <h6>Andrew</h6>
                  <small class="text-muted">Project Manager</small>
                </div>
              </div>
            </td>
            <td>Real Homes</td>
            <td>
              <label class="rounded-0 badge badge-info text-white">Medium</label>
            </td>
            <td>$23.9K</td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="warning" text="Primary" />
                </div>
                <div class>
                  <h6>Bhavesh patel</h6>
                  <small class="text-muted">Developer</small>
                </div>
              </div>
            </td>
            <td>MedicalPro Theme</td>
            <td>
              <label class="rounded-0 badge badge-success">High</label>
            </td>
            <td>$12.9K</td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="success" text="N" />
                </div>
                <div class>
                  <h6>Nirav Joshi</h6>
                  <small class="text-muted">Frontend Eng</small>
                </div>
              </div>
            </td>
            <td>Elite Admin</td>
            <td>
              <label class="rounded-0 badge badge-warning">Low</label>
            </td>
            <td>$10.9K</td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="dark" text="H" />
                </div>
                <div class>
                  <h6>Micheal Doe</h6>
                  <small class="text-muted">Content Writer</small>
                </div>
              </div>
            </td>
            <td>Helping Hands</td>
            <td>
              <label class="rounded-0 badge badge-warning">High</label>
            </td>
            <td>$12.9K</td>
          </tr>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="mr-2">
                  <vs-avatar size="large" color="danger" text="J" />
                </div>
                <div class>
                  <h6>Johnathan</h6>
                  <small class="text-muted">Graphic</small>
                </div>
              </div>
            </td>
            <td>Digital Agency</td>
            <td>
              <label class="rounded-0 badge badge-warning">High</label>
            </td>
            <td>$2.6K</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
export default {
  name: "ProjectTable",
  data: () => ({
    title: "ProjectTable"
  })
};
</script>