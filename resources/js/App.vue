<template>
  <div id="app">
    <nav-bar/>
    <aside-menu :menu="menu"/>
    <router-view/>
    <footer-bar/>
  </div>
</template>

<script>
// @ is an alias to /src
import NavBar from '@/components/NavBar'
import AsideMenu from '@/components/AsideMenu'
import FooterBar from '@/components/FooterBar'

export default {
  name: 'home',
  components: {
    FooterBar,
    AsideMenu,
    NavBar
  },
  computed: {
    menu () {
      return [
        this.$t('sidebar.general'),
        [
          {
            to: '/',
            icon: 'desktop-mac',
            label: this.$t('sidebar.dashboard'),
            role: ['admin', 'user'],
          }
        ],
        this.$t('sidebar.production'),
        [
          {
            to: '/products/list',
            label: this.$t('sidebar.products'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
        ],
        this.$t('sidebar.template'),
        [
          {
            to: '/products/template',
            label: this.$t('sidebar.prodTemplate'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
          {
            to: '/products/section/template',
            label: this.$t('sidebar.prodSection'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },

        ],
        this.$t('sidebar.resource'),
        [
          {
            to: '/users/index',
            label: this.$t('sidebar.users'),
            icon: 'account-multiple',
            updateMark: true,
            role: ['admin'],
          },
          {
            to: '/tokens',
            label: 'Access Tokens',
            icon: 'account-multiple',
            updateMark: true,
            role: ['admin'],
          },
        ]
      ]
    }
  },
  created () {
    axios
      .get('/user')
      .then(r => {
        this.$store.commit('user', r.data.data)
      })
      .catch(err => {
        this.$buefy.toast.open({
          message: `Error: ${err.message}`,
          type: 'is-danger'
        })
      })
  }
}
</script>
