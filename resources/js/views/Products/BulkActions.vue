<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/products/list" class="button">
        {{$gettext('bulkActionsPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <bulk-register />
      </tiles>
      <tiles>
        <bulk-re-verify />
      </tiles>
    </section>
  </div>
</template>

<script>
  import clone from 'lodash/clone'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import Tiles from '@/components/Tiles'
  import CardComponent from '@/components/CardComponent'
  import BulkRegister from '@/components/BulkActions/BulkRegister'
  import BulkReVerify from '@/components/BulkActions/BulkReVerify'

  export default {
    name: 'BulkActions',
    components: {CardComponent, Tiles, HeroBar, TitleBar, BulkRegister, BulkReVerify},
    computed: {
      titleStack () {
        return [
          this.$gettext('bulkActionsPage.titleBar.main'),
          this.$gettext('bulkActionsPage.titleBar.sub1'),
        ]
      },
      heroTitle () {
        return this.$gettext('bulkActionsPage.heroBar.title')
      }
    }
  }
</script>
