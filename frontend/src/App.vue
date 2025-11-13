<template>
  <div id="app">
     <vs-alert
    :active.sync="isIdle"
    closable
    close-icon="close" color="danger">
      Sesi anda berakhir karena tidak ada aktifitas selama 30 menit...
    </vs-alert>
    <router-view></router-view>
  </div>
</template>

<script>
import themeConfig from '@/../themeConfig.js'

export default {
  name: 'app',
  components: {
  },
  data:()=>({
  }),
  watch: {
    '$store.state.theme'(val) {
        this.toggleBodyClass(val);
    },
    isIdle: function(){
        if(this.isIdle){
            this.$http.post('/logout').then(() => {
                //vm.$destroy();
                this.$store.commit('IS_LOGGED_IN',false);
                this.$router.push('/login');
            }).catch(error=>{
                console.log(error);
            })
        }
    }
  },
  methods: {
  toggleBodyClass(className) {
            if (className == 'dark') {
                document.body.classList.add('dark-theme');
            } else {
                document.body.classList.remove('dark-theme');
                
            }
        }
    },
  
mounted() {
  this.toggleBodyClass(themeConfig.theme)
},

computed: {
    isIdle() {
        return this.$store.state.idleVue.isIdle;
    }
}
    
  
}
</script>