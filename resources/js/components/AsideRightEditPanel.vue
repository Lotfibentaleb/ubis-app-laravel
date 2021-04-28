<template>
  <div class="aside-right-edit-panel" v-bind:class="{'aside-right-edit-panel-transition': isAsideLeftEditPanel}">
    <b-button class="btn excel-export" @click="closeEditPanel">Close</b-button>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BButton from "buefy/src/components/button/Button";
  export default {
    name: 'AsideLeftEditPanel',
    components: {BButton},
    computed: {
      ...mapState([
        'isAsideLeftEditPanel'
      ])
    },
    methods: {
      closeEditPanel () {
        this.$store.commit('editPanel', false)
      }
    }
  }
</script>