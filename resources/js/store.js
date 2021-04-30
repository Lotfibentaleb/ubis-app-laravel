import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    /* User */
    userName: null,
    userEmail: null,
    userAvatar: null,
    userRole: null,

    /* NavBar */
    isNavBarVisible: true,

    /* FooterBar */
    isFooterBarVisible: true,

    /* Aside */
    isAsideVisible: true,
    isAsideMobileExpanded: false,

    /* Edit Panel */
    isFromProd: false,

    isAsideLeftEditPanel: false,
    isFromTemp: false,
    isClickedTemplateSave: false,
    isClickedTemplateCancel: false,

    isFromSecTemp: false,
    isClickedSecTemplateSave: false,
    isClickedSecTemplateCancel: false,

  },
  mutations: {
    /* A fit-them-all commit */
    basic (state, payload) {
      state[payload.key] = payload.value
    },

    /* User */
    user (state, payload) {
      if (payload.name) {
        state.userName = payload.name
      }
      if (payload.email) {
        state.userEmail = payload.email
      }
      if (payload.avatar) {
        state.userAvatar = payload.avatar
      }
      if (payload.role != null) {
          state.userRole = payload.role == 0 ? 'admin' : 'user'
      }
    },

    /* Aside Left Edit Panel */
    editPanel (state, payload) {
      state.isAsideLeftEditPanel = payload
    },

    fromTemp (state, payload) {
      state.isFromTemp = payload
    },

    fromSecTemp (state, payload) {
      state.isFromSecTemp = payload
    },

    prodTemplateSave (state, payload) {
      state.isClickedTemplateSave = payload
    },

    prodTemplateCancel (state, payload) {
      state.isClickedTemplateCancel = payload
    },

    prodSecTemplateSave (state, payload) {
      state.isClickedSecTemplateSave = payload
    },

    prodSecTemplateCancel (state, payload) {
      state.isClickedSecTemplateCancel = payload
    },

    /* Aside Mobile */
    asideMobileStateToggle (state, payload = null) {
      const htmlClassName = 'has-aside-mobile-expanded'

      let isShow

      if (payload !== null) {
        isShow = payload
      } else {
        isShow = !state.isAsideMobileExpanded
      }

      if (isShow) {
        document.documentElement.classList.add(htmlClassName)
      } else {
        document.documentElement.classList.remove(htmlClassName)
      }

      state.isAsideMobileExpanded = isShow
    }
  },
  actions: {

  }
})
