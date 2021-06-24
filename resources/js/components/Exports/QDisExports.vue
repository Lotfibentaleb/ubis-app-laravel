<template>
  <card-component :title="$gettext('exportsPage.qdisExports.card.title')" icon="package-variant-closed" class="tile is-child">
    <div class="column is-one-fifths is-offset-two-fifths">
      <p>{{$gettext('exportsPage.qdisExports.explanation')}}</p>
    </div>
    <div class="column is-1 is-offset-11">
      <b-button :label="$gettext('exportsPage.qdisExports.exportBtn')" type="is-primary" @click="qdisExports" />
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  export default {
    name: 'QDisExports',
    components: {CardComponent},
    methods: {
      qdisExports() {

      }
    }
  }
</script>