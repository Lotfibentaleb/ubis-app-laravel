// Axios & Echo global
require('./bootstrap');

/* Core */
import Vue from 'vue'
import Buefy from 'buefy'

import VueApexCharts from 'vue-apexcharts'
import VueCarousel from 'vue-carousel'

/* Router & Store */
import router from './router'
import store from './store'

import FlagIcon from 'vue-flag-icon'
import GetTextPlugin from 'vue-gettext'
import translations from './translations.json'

import { library } from '@fortawesome/fontawesome-svg-core';
// internal icons
import { faCheck, faCheckCircle, faInfoCircle, faExclamationTriangle, faExclamationCircle,
  faArrowUp, faAngleRight, faAngleLeft, faAngleDown,
  faEye, faEyeSlash, faCaretDown, faCaretUp, faUpload } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faCheck, faCheckCircle, faInfoCircle, faExclamationTriangle, faExclamationCircle,
    faArrowUp, faAngleRight, faAngleLeft, faAngleDown,
    faEye, faEyeSlash, faCaretDown, faCaretUp, faUpload);

Vue.use(FlagIcon);

Vue.use(VueApexCharts)
Vue.use(VueCarousel)
Vue.component('apexchart', VueApexCharts)

const lang = document.documentElement.lang.substr(0, 2);
// or however you determine your current app locale


/* Vue. Main component */
import App from './App.vue'

/* Vue. Component in recursion */
import AsideMenuList from '@/components/AsideMenuList'

/* Collapse mobile aside menu on route change */
router.afterEach(() => {
  store.commit('asideMobileStateToggle', false)
})

Vue.config.productionTip = false

/* These components are used in recursion algorithm */
Vue.component('AsideMenuList', AsideMenuList)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.use(require('vue-moment'));
/* Main component */
Vue.component('App', App)

/* Buefy */
Vue.use(Buefy)

/* This is main entry point */

Vue.use(GetTextPlugin, {
  availableLanguages: {
    en_GB: 'British English',
    de_DE: 'Germany'
  },
  defaultLanguage: 'en_GB',
  languageVmMixin: {
    computed: {
      currentKebabCase: function () {
        return this.current.toLowerCase().replace('_', '-')
      },
    },
  },
  translations: translations,
  silent: true,
})

new Vue({
  router,
  store,
  render: h => h(App),
  mounted() {
    document.documentElement.classList.remove('has-spinner-active')
  }
}).$mount('#app')
