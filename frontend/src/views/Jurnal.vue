<template>
   <vs-row vs-justify="center">
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <div slot="header">
          <h3 class="card-title">Input groups</h3>
          <h5
            class="card-subtitle font-weight-normal text-muted mb-0"
          >All with vuesax element attributes</h5>
        </div>
        <div>
          <vs-input icon="mode_edit" placeholder="Username" v-model="ig5" class="mt-4 w-100" />
          <vs-input
            icon-after="true"
            placeholder="Recepient's name"
            icon="mode_edit"
            class="mt-4 w-100"
            v-model="ig5_1"
          />
          <vs-input
            icon="attach_money"
            placeholder="Username"
            v-model="ig5_2"
            class="mt-4 w-100 mb-4"
          />
        </div>
        <div class="pt-2 mb-4">
          <hr class="custom-hr" />
          <h4 class="pb-3 pt-3">Group with Label Placeholder</h4>
          <vs-input
            icon-after="true"
            label-placeholder="Label with placeholder"
            icon="mode_edit"
            class="mt-4 w-100"
            v-model="ig5_3"
          />
          <vs-input
            icon="add"
            class="mt-4 w-100"
            label-placeholder="Label with placeholder"
            v-model="ig5_4"
          />
          <vs-input
            icon-no-border
            icon="account_circle"
            class="mt-4 w-100"
            label-placeholder="icon-no-border"
            v-model="ig5_5"
          />
        </div>
        
        <div class="btn-alignment mt-4">
          <vs-button color="success" type="filled">Save</vs-button>
          <vs-button color="dark" type="filled">Cancel</vs-button>
        </div>
      </vs-card>
    </vs-col>
    <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
      <vs-card>
        <div slot="header">
          <h3 class="card-title mb-0">Other Sample Form</h3>
        </div>
 
        <h4 class>Peronal Info</h4>
        <hr class="custom-hr" />
        <vs-row>
          <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
            <vs-input
              label="First Name"
              placeholder="John Smith"
              v-model="fname3"
              class="w-100"
              description-text="This is inline help"
            />
            <vs-select class="w-100 mt-4" label="Gender" v-model="gender3">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in options3"
              />
            </vs-select>
            <vs-select class="w-100 mt-4" label="Category" v-model="category3">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in options3_1"
              />
            </vs-select>
          </vs-col>
          <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
            <vs-input
              label="Last Name"
              placeholder="12n"
              v-model="lname3"
              class="w-100 mt-4 mt-md-0"
              description-text="This field has error."
            />
            <vs-input label="Date of Birth" type="date" v-model="date3" class="w-100 mt-4" />
            <label class="vs-input--label mt-4">Membership</label>
            <div class="d-flex align-items-center mt-1">
              <vs-radio v-model="radios3" class="mb-0" vs-name="radios3" vs-value="Free">Free</vs-radio>
              <vs-radio v-model="radios3" class="ml-2 mb-0" vs-name="radios3" vs-value="Paid">Paid</vs-radio>
            </div>
          </vs-col>
        </vs-row>
        <h4 class="mt-5 mb-4">Address</h4>
        <hr class="custom-hr" />
        <vs-row>
          <vs-col vs-lg="12" vs-xs="12" vs-sm="12">
            <vs-input label="Street" v-model="street3" class="w-100 mt-4" />
          </vs-col>
          <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
            <vs-input label="City" v-model="city3" class="w-100 mt-4" />
            <vs-input label="Post Code" v-model="post3" class="w-100 mt-4" />
          </vs-col>
          <vs-col vs-lg="6" vs-xs="12" vs-sm="6">
            <vs-input label="State" v-model="state3" class="w-100 mt-4" />
            <vs-select class="w-100 mt-4" label="Country" v-model="country3">
              <vs-select-item
                :key="index"
                :value="item.value"
                :text="item.text"
                v-for="(item,index) in countryoptions3"
              />
            </vs-select>
          </vs-col>
        </vs-row>
        <div class="btn-alignment mt-4">
          <vs-button color="success" type="filled">Save</vs-button>
          <vs-button color="dark" type="filled">Cancel</vs-button>
        </div>
      </vs-card>
    </vs-col>
   </vs-row>
</template>

<script>
  export default {
    components: {
      
    },
    data: function () {
      return {
        // eslint-disable-next-line
      }
    }
  }
</script>

<style src='../assets/styles/jqwidgets/jqx.base.css'></style>
<style src='../assets/styles/jqwidgets/jqx.material-green.css'></style>