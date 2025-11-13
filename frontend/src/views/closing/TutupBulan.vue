<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>
        <div class="d-md-flex align-items-center pb-2">
          <h3 class="card-title mb-0">Tutup Bulan</h3>
        </div>
        <hr class="custom-hr" />
        <vs-row>
          <vs-col vs-lg="3" vs-xs="12" vs-sm="4">
            <div class="vs-component vs-con-input-label vs-input w-100 mt-4 vs-input-primary">
              <label class="vs-input--label">BULAN</label>
              <div class="vs-con-input">
                <date-picker v-model="formdata.bulan" value-type="format" type="month" placeholder="Pilih Bulan"></date-picker>
              </div>
            </div>
          </vs-col>
        </vs-row>
        <hr class="custom-hr" />
        <vs-button :disabled="loading" color="primary" type="filled" @click.stop.prevent="submitClosing">
          {{ loading ? 'Memproses...' : 'Submit' }}
        </vs-button>
      </vs-card>
    </vs-col>
  </vs-row>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'

export default {
  name: 'TutupBulan',
  components: { DatePicker },
  data () {
    return {
      loading: false,
      formdata: {
        bulan: ''
      },
      errors: []
    }
  },
  methods: {
    async submitClosing () {
      this.errors = []
      if (!this.formdata.bulan) {
        this.errors.push('Bulan wajib dipilih')
        this.$vs.notify({ title: 'Validasi', text: 'Silakan pilih bulan terlebih dahulu.', color: 'warning' })
        return
      }
      try {
        this.loading = true
        this.$vs.loading({ type: 'radius' })
        await this.$http.post('/api/closing_month', {
          bulan: this.formdata.bulan
        })
        this.$vs.loading.close()
        this.loading = false
        this.$vs.notify({ title: 'Success', text: 'Proses tutup bulan berhasil.', color: 'success' })
      } catch (error) {
        this.$vs.loading.close()
        this.loading = false
        // Tampilkan error umum
        this.$vs.notify({ title: 'Gagal', text: 'Terjadi kesalahan saat memproses tutup bulan.', color: 'danger' })
      }
    }
  }
}
</script>

<style scoped>
.custom-hr {
  margin: 16px 0;
}
</style>
