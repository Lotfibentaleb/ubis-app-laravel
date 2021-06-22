<template>
  <card-component :title="$gettext('bulkActionsPage.bulkReVerify.card.title')" icon="account-edit" class="tile is-child">
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  export default {
    name: 'BulkReVerify',
    components: {CardComponent}
  }
</script>