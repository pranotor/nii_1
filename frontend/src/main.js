import Vue from 'vue'
import Vuesax from 'vuesax'


import App from './App.vue'

import 'vuesax/dist/vuesax.css' //Vuesax styles
import 'material-icons/iconfont/material-icons.css';
// Vuex Store
import store from './store/store'
// Theme Configurations
import '../themeConfig.js'
// Perfectscrollbar
// import PerfectScrollbar from "vue2-perfect-scrollbar";
// import "vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css";
// Vue.use(PerfectScrollbar);

import Vuebar from 'vuebar';
Vue.use(Vuebar);
// Theme Configurations
import 'prismjs'
import 'prismjs/themes/prism.css'
import VsPrism from './components/prism/VsPrism.vue';
Vue.component(VsPrism.name, VsPrism);
import VsSidebarGroup from './components/vs-sidebar-group/Sidebar-Group.vue';
Vue.component(VsSidebarGroup.name, VsSidebarGroup);
// i18n
import i18n from './i18n/i18n.js'
// Vue Router
import router from './router'
Vue.config.productionTip = false

import axios from 'axios';
const base = axios.create({
  withCredentials : true,
  //baseURL: 'http://saketap.test:9999'
  baseURL: process.env.VUE_APP_BACKEND_URL
  //baseURL: 'https://back.saketap.pass-pdam.com'
  //baseURL: document.location.origin
});

const baseXls = axios.create({
    baseURL: process.env.VUE_APP_BACKEND_URL
});

const basePass = axios.create({
    withCredentials : true,
    //baseURL: 'http://saketap.test:9999'
    baseURL: process.env.VUE_APP_PASS_URL
    //baseURL: 'https://back.saketap.pass-pdam.com'
    //baseURL: document.location.origin
  });

Vue.prototype.$http = base;
Vue.prototype.$httpass = basePass;
Vue.prototype.$httpxls = baseXls;

//Vue.prototype.$urlbackend = 'http://saketap.test:9999';
Vue.prototype.$urlbackend = process.env.VUE_APP_BACKEND_URL
//Vue.prototype.$urlbackend = 'https://back.saketap.pass-pdam.com';
//Vue.prototype.$urlbackend = document.location.origin;
Vue.prototype.$session = store;

Vue.use(Vuesax)

import VueCookies from 'vue-cookies';
Vue.use(VueCookies);

Vue.directive('focus', {
// When the bound element is inserted into the DOM...
inserted: function (el) {
    // Focus the element
    el.focus()
}
})

import IdleVue from "idle-vue";

const eventsHub = new Vue();

Vue.use(IdleVue, {
  eventEmitter: eventsHub,
  store,
  idleTime: 1200000, // 20 mnt
  startAtIdle: false
});


var vm = new Vue({
  store,
  router,
  i18n,
  render: h => h(App),
}).$mount('#app')

global.vm = vm; //Define your app variable globally

import '@/assets/scss/style.scss'
