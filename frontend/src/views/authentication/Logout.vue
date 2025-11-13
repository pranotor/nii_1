<script>
export default {
  name: 'Login',
  data:()=>({
    msg: "Login SAKETAP",
    rememberme:false,
    email:'',
    pwd:'',
    errors:[],
  }),
  methods:{
    checkForm:function(e) {
        this.$http.get('/sanctum/csrf-cookie').then(response => {
            this.$http.post('/login',{
                email : this.email,
                password : this.pwd
            }).then(response2 => {
                //console.log(response2);
                this.handleSuccessLogin();
                //
            }).catch(error=>{
                this.handleErrorLogin(error);
            })
        });
    },
    handleErrorLogin: function(error){
        const key = Object.keys(error.response.data.errors)[0];
        console.log(error.response.data.errors[key][0]);
        this.errors.push(error.response.data.errors[key][0]);
    },
    handleSuccessLogin: function(){
        this.$store.commit('IS_LOGGED_IN',true);
        this.$router.push('/dashboards/classic-dashboard');
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

