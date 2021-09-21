// Axios & Echo global
require('./bootstrap');

/* Core */
import Vue from 'vue'
import Buefy from 'buefy'

/* Router & Store */
import router from './landingapprouter'

/* Vue. Main component */
import App from './LandingApp.vue'

import { library } from '@fortawesome/fontawesome-svg-core';
// internal icons
import { faPhoneAlt } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faPhoneAlt);

//import 'buefy/dist/buefy.css';

Vue.config.productionTip = false

/* Main component */
Vue.component('LandingApp', App)

Vue.component('font-awesome-icon', FontAwesomeIcon)

/* Buefy */
Vue.use(Buefy, {
  defaultIconComponent: 'vue-fontawesome',
  defaultIconPack: 'fas',
});
/* This is main entry point */

new Vue({
  router,
  render: h => h(App),
  mounted() {
    document.documentElement.classList.remove('has-spinner-active')
  }
}).$mount('#app')
