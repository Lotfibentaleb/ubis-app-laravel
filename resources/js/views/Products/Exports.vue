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
        <excel-exports-by-serial />
      </tiles>
      <tiles>
        <excel-exports-by-sub-serial />
      </tiles>
      <tiles>
        <q-das-exports />
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
  import QDasExports from '@/components/Exports/QDasExports'
  import ExcelExportsBySerial from '@/components/Exports/ExcelExportsBySerial'
  import ExcelExportsBySubSerial from '@/components/Exports/ExcelExportsBySubSerial'

  export default {
    name: 'BulkActions',
    components: {CardComponent, Tiles, HeroBar, TitleBar, ExcelExports, QDasExports, ExcelExportsBySerial, ExcelExportsBySubSerial},
    computed: {
      titleStack ()
        {
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
