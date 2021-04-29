<template>
  <div class="aside-right-edit-panel" v-bind:class="{'aside-right-edit-panel-transition': isAsideLeftEditPanel}">
    <div class="edit-panel-close">
      <b-button class="close-btn" @click="closeEditPanel"></b-button>
    </div>

    <div class="edit-panel-layout" disabled>
      <h1 class="section-product-disabled">Products</h1>
      <div class="section-product section-product-disabled">
        <div class="section-layout">
          <div class="section-header-label">
            <h3>Produktionsauftrag</h3>
          </div>
          <div class="section-content">
            <b-input />
          </div>
          <div class="section-footer">
            <b-button class="btn-confirm">Save</b-button>
            <b-button class="btn-cancel" @click="closeEditPanel">Close</b-button>
          </div>
        </div>
      </div>
      <h1>Produktionsabläufe</h1>
      <div class="section-product">
        <div class="section-layout">
          <div class="section-header-label">
            <h3>You must save the changed </h3>
          </div>
          <div class="section-header-label">
            <h3>production flow data.</h3>
          </div>
          <div class="section-footer section-footer-template">
            <b-button class="btn-confirm" @click="clickedTemplateSave">Save</b-button>
            <b-button class="btn-cancel" @click="clickedTemplateCancel">Cancel</b-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BButton from "buefy/src/components/button/Button";
  import BInput from "buefy/src/components/input/Input";
  export default {
    name: 'AsideLeftEditPanel',
    components: {BInput, BButton},
    computed: {
      ...mapState([
        'isAsideLeftEditPanel',
        'isClickedTemplateSave',
        'isClickedTemplateCancel'
      ])
    },
    methods: {
      closeEditPanel () {
        this.$store.commit('editPanel', false)
      },

      clickedTemplateSave () {
        this.$store.commit('prodTemplateSave', true)
      },
      clickedTemplateCancel () {
        this.$store.commit('prodTemplateCancel', true)
      },
    }
  }
</script>