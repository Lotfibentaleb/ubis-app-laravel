<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/products/list" class="button">
        {{$gettext('exportsPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <excel-exports />
      </tiles>
      <tiles>
        <q-dis-exports />
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
  import ExcelExports from '@/components/Exports/ExcelExports'
  import QDisExports from '@/components/Exports/QDisExports'

  export default {
    name: 'BulkActions',
    components: {CardComponent, Tiles, HeroBar, TitleBar, ExcelExports, QDisExports},
    computed: {
      titleStack () {
        return [
          this.$gettext('exportsPage.titleBar.main'),
          this.$gettext('exportsPage.titleBar.sub1'),
        ]
      },
      heroTitle () {
        return this.$gettext('exportsPage.heroBar.title')
      }
    }
  }
</script>