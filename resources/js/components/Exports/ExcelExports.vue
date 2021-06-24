<template>
  <card-component :title="$gettext('exportsPage.excelExports.card.title')" icon="package-variant-closed" class="tile is-child">
    <div class="column is-one-fifths is-offset-two-fifths">
      <p>{{$gettext('exportsPage.excelExports.explanation')}}</p>
    </div>
    <div class="column is-1 is-offset-11">
      <a :href="hrefUrl"><b-button :label="$gettext('exportsPage.excelExports.exportBtn')" type="is-primary" /></a>
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  import BButton from "buefy/src/components/button/Button";
  export default {
    name: 'ExcelExports',
    components: {BButton, CardComponent},
    data() {
      return {
        hrefUrl: '',
        dataUrl: '/productlist',
        sortField:'created_at',
        sortOrder:'desc',
        filterValues: '{}',
      }
    },
    created () {
      this.getHrefURL()
    },
    methods: {
      getHrefURL() {
        const paramsFullExcel = [
          `enhanced=2`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `filter=${this.filterValues}`
        ].join('&')
        this.hrefUrl = this.dataUrl + '/fullExcel?' + paramsFullExcel
      }
    }
  }
</script>