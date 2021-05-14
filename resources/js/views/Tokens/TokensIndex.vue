<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      Tokens
      <router-link slot="right" to="/" class="button">
        Dashboard
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <card-component title="Developer Access" icon="key" class="tile is-child">
          <developer-access-tokens />
        </card-component>
      </tiles>
      <tiles>
        <card-component title="Api Access" icon="key" class="tile is-child">
          <api-access-tokens />
        </card-component>
      </tiles>
    </section>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import CardComponent from '@/components/CardComponent'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import DeveloperAccessTokens from '@/components/Tokens/DeveloperAccessTokens'
  import ApiAccessTokens from '@/components/Tokens/ApiAccessTokens'
  import Tiles from '@/components/Tiles'
  import UserAvatar from '@/components/UserAvatar'
  export default {
    name: 'Profile',
    components: { UserAvatar, Tiles, DeveloperAccessTokens, ApiAccessTokens, HeroBar, TitleBar, CardComponent },
    computed: {
      titleStack () {
        return [
          'Admin',
          'Tokens'
        ]
      },
      ...mapState([
        'userName',
        'userEmail'
      ])
    }
  }
</script>
