// Axios & Echo global
require('./bootstrap');

/* Core */
import Vue from 'vue'
import Buefy from 'buefy'

/* Router & Store */
import router from './landingapprouter'

/* Vue. Main component */
import App from './LandingApp.vue'

//import 'buefy/dist/buefy.css';

Vue.config.productionTip = false

/* Main component */
Vue.component('LandingApp', App)
/* Buefy */
Vue.use(Buefy)

/* This is main entry point */

new Vue({
  router,
  render: h => h(App),
  mounted() {
    document.documentElement.classList.remove('has-spinner-active')
  }
}).$mount('#app')
