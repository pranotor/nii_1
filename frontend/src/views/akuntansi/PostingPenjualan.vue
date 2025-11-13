<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
          <h3 class="card-title mb-0">Posting Penjualan</h3>
          <div class="ml-auto d-flex">
            <vs-button type="border" icon="refresh" class="mr-2" @click="fetchData" :disabled="loading">Reload</vs-button>
            <vs-button color="primary" icon="cloud_upload" @click="processPosting" :disabled="loading || items.length === 0">Posting</vs-button>
          </div>
        </div>
        <hr class="custom-hr" />

        <vs-alert v-if="error" color="danger" class="mb-3">
          {{ error }}
        </vs-alert>

        <div>
          <vs-table :data="items" notSpacer maxHeight="420px">
            <template slot="thead">
              <vs-th v-for="(h, idx) in headers" :key="idx">{{ h }}</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td v-for="(key, idx) in headerKeys" :key="idx" :data="safe(tr, key)">
                  {{ safe(tr, key) }}
                </vs-td>
              </vs-tr>
              <vs-tr v-if="!loading && (!data || data.length === 0)">
                <vs-td :colspan="headers.length">
                  Tidak ada transaksi yang perlu diposting.
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </vs-card>
    </vs-col>
  </vs-row>
</template>

<script>
export default {
  name: 'PostingPenjualan',
  data: () => ({
    items: [],
    headers: [],
    headerKeys: [],
    loading: false,
    error: ''
  }),
  methods: {
    safe(obj, key){
      // supports nested keys like 'customer.name'
      if(!obj) return '';
      try{
        return key.split('.').reduce((o,k)=> (o && o[k] !== undefined) ? o[k] : '', obj)
      }catch(e){
        return ''
      }
    },
    normalizeHeaders(arr){
      // Build headers dynamically based on first item keys
      if(!arr || arr.length === 0){
        this.headers = ['Data'];
        this.headerKeys = ['__string']
        return;
      }
      const first = arr[0]
      if(typeof first === 'object' && !Array.isArray(first)){
        const keys = Object.keys(first)
        this.headerKeys = keys
        this.headers = keys.map(k=> k.toUpperCase())
      } else {
        this.headerKeys = ['__string']
        this.headers = ['DATA']
        // map items to object form for consistent rendering
        this.items = arr.map(v=> ({ __string: v }))
      }
    },
    async fetchData(){
      this.error = ''
      this.loading = true
      this.$vs.loading({ type: 'radius' })
      try{
        const res = await this.$http.get('/api/posting')
        const data = res && res.data ? res.data : []
        // ensure array
        this.items = Array.isArray(data) ? data : (data && data.items ? data.items : [])
        this.normalizeHeaders(this.items)
      }catch(err){
        this.error = this.parseHttpError(err)
        this.$vs.notify({ title: 'Gagal Memuat', text: this.error, color: 'danger' })
      }finally{
        this.loading = false
        this.$vs.loading.close()
      }
    },
    async processPosting(){
      if(this.items.length === 0) return
      this.error = ''
      this.loading = true
      this.$vs.loading({ type: 'radius' })
      try{
        await this.$http.post('/api/posting/process')
        this.$vs.notify({ title: 'Sukses', text: 'Proses posting berhasil.', color: 'success' })
        await this.fetchData()
      }catch(err){
        const msg = this.parseHttpError(err)
        this.$vs.notify({ title: 'Gagal Posting', text: msg, color: 'danger' })
      }finally{
        this.loading = false
        this.$vs.loading.close()
      }
    },
    parseHttpError(error){
      try{
        if(error && error.response){
          const e = error.response
          if(e.data){
            if(typeof e.data === 'string') return e.data
            if(e.data.message) return e.data.message
            if(e.data.errors){
              const key = Object.keys(e.data.errors)[0]
              return e.data.errors[key][0]
            }
          }
          return `${e.status} ${e.statusText}`
        }
        return (error && error.message) ? error.message : 'Unknown error'
      }catch(ex){
        return 'Unknown error'
      }
    }
  },
  mounted(){
    this.fetchData()
  }
}
</script>

<style scoped>
.custom-hr{ margin-top: 10px; margin-bottom: 15px; }
.ml-auto{ margin-left: auto; }
.c-pointer{ cursor: pointer; }
</style>
