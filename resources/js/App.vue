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
        this.$gettext('sidebar.general'),
        [
          {
            to: '/',
            icon: 'desktop-mac',
            label: this.$gettext('sidebar.dashboard'),
            role: ['admin', 'user'],
          }
        ],
        this.$gettext('sidebar.production'),
        [
          {
            to: '/products/bulkactions',
            label: this.$gettext('sidebar.bulkActions'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
          {
            to: '/products/list',
            label: this.$gettext('sidebar.products'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
          {
            to: '/measurements/list',
            label: this.$gettext('sidebar.measurements'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
        ],
        this.$gettext('sidebar.setup'),
        [
          {
            to: '/products/template',
            label: this.$gettext('sidebar.prodTemplate'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },
          {
            to: '/products/section/template',
            label: this.$gettext('sidebar.prodSection'),
            icon: 'package-variant-closed',
            role: ['admin', 'user'],
          },

        ],
        this.$gettext('sidebar.resource'),
        [
          {
            to: '/users/index',
            label: this.$gettext('sidebar.users'),
            icon: 'account-multiple',
            updateMark: true,
            role: ['admin'],
          },
          {
            to: '/tokens',
            label: 'Access Tokens',
            icon: 'key',
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
