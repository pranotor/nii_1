<template>
  <vs-row vs-justify="center" vs-align="center" class="full-height login-register-bg">
    <vs-col type="flex"  vs-justify="center" vs-align="center" class="login-register-box" vs-lg="12" vs-xs="12">
    <vs-card class=" mb-0">
      <div slot="header">
        <h3 class="mb-1">{{msg}}</h3>
        <p class="mb-0">SALES & PRODUCTION SYSTEM</p>
      </div>
      <vs-alert v-if="errors.length" class="mb-3 mt-2"  color="danger">
        <b>Please correct the following error(s):</b>
        <ul class="common-list">
          <li v-for="error in errors" :key="error">{{ error }}</li>
        </ul>
      </vs-alert>
      <form @submit="checkForm" novalidate="true">

      <vs-input icon-before="true" size="large" class="w-100 mb-4 mt-2 vs-input-large" icon="person" placeholder="Email ID" v-model="email" type="email"/>
      <vs-input icon-before="true" size="large" class="w-100 mb-4 mt-2 vs-input-large" type="password" icon="lock" placeholder="Password" v-model="pwd"/>
      <vs-input icon-before="true" size="large" class="w-100 mb-4 vs-input-large" type="text" icon="calendar_today" placeholder="Tahun" v-model="thn"/>
      
      <input class="btn-block btn btn-primary submit-button" type="submit" @click.stop.prevent="checkForm" value="Login"/>
    </form>
    </vs-card>
    </vs-col>
  </vs-row>   
  
</template>

<script>
export default {
  name: 'Login',
  data:()=>({
    msg: "Login System",
    rememberme:false,
    email:'',
    pwd:'',
    thn:'',
    errors:[],
  }),
  methods:{
    checkForm:function(e) {
        this.errors = [];
        this.$vs.loading({'type':'radius'});
        this.$http.get('/sanctum/csrf-cookie').then(response => {
            this.$http.post('/login',{
                email : this.email,
                password : this.pwd,
                thnValuta : this.thn
            }).then(response2 => {
                //console.log(response2);
                this.handleSuccessLogin();
                //
            }).catch(error=>{
                this.handleErrorLogin(error);
            })
        }).catch(error=>this.handleErrorLogin(error,'csrf'));
    },
    handleErrorLogin: function(error,type='login'){
        if(type=='login'){
            const key = Object.keys(error.response.data.errors)[0];
            //console.log(error.response.data.errors[key][0]);
            this.errors.push(error.response.data.errors[key][0]);
        }
        else{
            this.errors.push(error);
        }
        this.$vs.loading.close()
    },
    handleSuccessLogin: function(){
        this.$http.get('/api/user').then(response => {
            //console.log(response.data)
            this.$store.commit('SET_USERNAME',response.data.name)
        })
        this.$vs.loading.close()
        this.$store.commit('IS_LOGGED_IN',true)
        this.$store.commit('SET_THN_VALUTA',this.thn)
        //window.$cookies.set('THN',this.thn)
        this.$cookies.set("THN",this.thn,null, null, null,false,'lax');
        this.$router.push('/dashboards/classic-dashboard')

    },
    currentYear() {
        var d = new Date();
        var n = d.getFullYear();
        this.thn = n;
    }
  },  
  computed: {
    inputValid(){
      if(this.validEmail(this.email) && this.pwd){
        return false
      } else {
        return true
      }
    }
  },
  mounted: function(){
      this.currentYear();
  }
}
</script>
<style>
.login-register-bg{
  margin:0 auto;
  background:url(../../assets/images/background/auth-bg.jpg) no-repeat center center;
}
.login-register-box{
  max-width:400px;
}

</style>

