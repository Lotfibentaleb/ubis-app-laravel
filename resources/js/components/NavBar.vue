<template>
  <nav v-show="isNavBarVisible" id="navbar-main" class="navbar is-fixed-top">
    <div class="navbar-brand">
      <a class="navbar-item is-hidden-desktop" @click.prevent="menuToggleMobile">
        <b-icon :icon="menuToggleMobileIcon"/>
      </a>
      <div class="navbar-item">
        <div class="control">
          <input class="input" placeholder="Search everywhere...">
        </div>
      </div>
    </div>
    <div class="navbar-brand is-right">
      <a class="navbar-item navbar-item-menu-toggle is-hidden-desktop" @click.prevent="menuNavBarToggle">
        <b-icon :icon="menuNavBarToggleIcon" custom-size="default"/>
      </a>
    </div>
    <div class="navbar-menu fadeIn animated faster" :class="{'is-active':isMenuNavBarActive}">
      <div class="navbar-end">
        <nav-bar-menu class="has-divider has-user-avatar">
          <div class="is-user-name">
            <span>{{ userName }}</span>
          </div>

          <div slot="dropdown" class="navbar-dropdown">
            <a class="navbar-item" href="/#/profile">
              <b-icon icon="account" custom-size="default"></b-icon>
              <span>{{$gettext('topbar.profile')}}</span>
            </a>
            <a class="navbar-item">
              <b-icon icon="settings" custom-size="default"></b-icon>
              <span>{{$gettext('topbar.settings')}}</span>
            </a>
            <hr class="navbar-divider">
            <a class="navbar-item" @click="logout">
              <b-icon icon="logout" custom-size="default"></b-icon>
              <span>{{$gettext('topbar.logout')}}</span>
            </a>
          </div>
        </nav-bar-menu>
        <div class="navbar-item has-divider">
          <b-dropdown
                  :scrollable="isScrollable"
                  :max-height="maxHeight"
                  v-model="currentLang"
                  aria-role="list"
          >
            <template #trigger>
              <flag :iso="currentLang.flag"/>
            </template>

            <b-dropdown-item
                    v-for="(language, index) in availableLanguages"
                    :key="index"
                    :value="language" aria-role="listitem">
              <div class="media" style="align-items: center;">
                <flag :iso="language.flag"/>
                <div class="media-content" style="margin-left: 10px;">
                  <h2>{{language.title}}</h2>
                </div>
              </div>
            </b-dropdown-item>
          </b-dropdown>
        </div>
        <a href="https://justboil.me/bulma-admin-template/one" class="navbar-item has-divider is-desktop-icon-only" title="About">
          <b-icon icon="help-circle-outline" custom-size="default"/>
          <span>About</span>
        </a>
        <a class="navbar-item is-desktop-icon-only" title="Log out" @click="logout">
          <b-icon icon="logout" custom-size="default"/>
          <span>Log out</span>
        </a>
      </div>
    </div>
  </nav>
</template>

<script>
  import { mapState } from 'vuex'
  import NavBarMenu from '@/components/NavBarMenu'
  import UserAvatar from '@/components/UserAvatar'
  import BField from "buefy/src/components/field/Field";

  export default {
    name: 'NavBar',
    components: {
      BField,
      UserAvatar,
      NavBarMenu,
    },
    watch: {
      currentLang: function() {
        // this.$root.$i18n.locale = this.currentLang.title
        this.$language.current = this.currentLang.title
      }
    },
    data () {
      return {
        isMenuNavBarActive: false,
        initLang: {
          title: "en",
          flag: "gb"
        },
        isScrollable: false,
        maxHeight: 200,
        currentLang: { title: 'en_GB', flag: 'gb' },
        availableLanguages: [
          {
            title: "en_GB",
            flag: "gb"
          },
          {
            title: "de_DE",
            flag: "de"
          }
        ],
      }
    },
    computed: {
      menuNavBarToggleIcon () {
        return (this.isMenuNavBarActive) ? 'close' : 'dots-vertical'
      },
      menuToggleMobileIcon () {
        return this.isAsideMobileExpanded ? 'backburger' : 'forwardburger'
      },
      ...mapState([
        'isNavBarVisible',
        'isAsideMobileExpanded',
        'userName'
      ])
    },
    methods: {
      menuToggleMobile () {
        this.$store.commit('asideMobileStateToggle')
      },
      menuNavBarToggle () {
        this.isMenuNavBarActive = (!this.isMenuNavBarActive)
      },
      logout () {
        document.getElementById('logout-form').submit()
      }
    }
  }
</script>
