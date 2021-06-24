<template>
  <card-component :title="$gettext('exportsPage.excelExports.card.title')" icon="package-variant-closed" class="tile is-child">
    <div class="column is-one-fifths is-offset-two-fifths">
      <p>You can use full excel exports feature</p>
    </div>
    <div class="column is-1 is-offset-11">
      <b-button label="Export" type="is-primary" @click="excelExports" />
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  import BButton from "buefy/src/components/button/Button";
  export default {
    name: 'ExcelExports',
    components: {BButton, CardComponent},
    methods: {
      excelExports() {

      }
    }
  }
</script>