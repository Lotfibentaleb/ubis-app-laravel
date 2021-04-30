<template>
  <div class="aside-right-edit-panel" v-bind:class="{'aside-right-edit-panel-transition': isAsideLeftEditPanel}">
    <div class="edit-panel-close">
      <b-button class="close-btn" @click="closeEditPanel"></b-button>
    </div>

    <div class="edit-panel-layout">
      <h1 v-bind:class="{'section-product-disabled': !isFromProd}">Products</h1>
      <div class="section-product" v-bind:class="{'section-product-disabled': !isFromProd}">
        <div class="section-layout">
          <div class="section-header-label">
            <h3>Produktionsauftrag</h3>
          </div>
          <div class="section-content">
            <b-input :disabled="!isFromProd" v-model="prodOrderNr" />
          </div>
          <div class="section-footer">
            <b-button class="btn-confirm" :disabled="!isFromProd" @click="clickedProdNrSave">Save</b-button>
            <b-button class="btn-cancel" @click="clickedProdNrCancel" :disabled="!isFromProd">Close</b-button>
          </div>
        </div>
      </div>

      <h1 v-bind:class="{'section-product-disabled': !isFromTemp}">Produktionsabläufe</h1>
      <div class="section-product" v-bind:class="{'section-product-disabled': !isFromTemp}">
        <div class="section-layout">
          <div class="section-header-label">
            <h3>You must save the changed </h3>
          </div>
          <div class="section-header-label">
            <h3>production flow data.</h3>
          </div>
          <div class="section-footer section-footer-template">
            <b-button class="btn-confirm" @click="clickedTemplateSave" :disabled='!isFromTemp'>Save</b-button>
            <b-button class="btn-cancel" @click="clickedTemplateCancel" :disabled='!isFromTemp'>Cancel</b-button>
          </div>
        </div>
      </div>

      <h1 v-bind:class="{'section-product-disabled': !isFromSecTemp}">Production Section Template</h1>
      <div class="section-product" v-bind:class="{'section-product-disabled': !isFromSecTemp}">
        <div class="section-layout">
          <div class="section-header-label">
            <h3>You must save the changed production </h3>
          </div>
          <div class="section-header-label">
            <h3>section template data.</h3>
          </div>
          <div class="section-footer section-footer-template">
            <b-button class="btn-confirm" @click="clickedSecTemplateSave" :disabled='!isFromSecTemp'>Save</b-button>
            <b-button class="btn-cancel" @click="clickedSecTemplateCancel" :disabled='!isFromSecTemp'>Cancel</b-button>
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
        'isClickedTemplateCancel',
        'isFromTemp',
        'isFromSecTemp',
        'isFromProd',
        'g_production_order_nr',
        'isClickedProdNrSave',
        'isClickedProdNrCancel'
      ])
    },
    watch: {
      prodOrderNr: function() {
        this.$store.commit('setProdOrderNr', this.prodOrderNr)
      },
      g_production_order_nr: function () {
        this.prodOrderNr = this.g_production_order_nr
      }
    },
    data () {
      return {
        prodOrderNr: ''
      }
    },
    created() {
      this.prodOrderNr = this.g_production_order_nr
    },
    methods: {
      closeEditPanel () {
        this.$store.commit('editPanel', false)
      },
      clickedProdNrSave () {
        this.$store.commit('prodOrderNrSave', true)
      },
      clickedProdNrCancel () {
        this.$store.commit('prodOrderNrCancel', true)
      },

      clickedTemplateSave () {
        this.$store.commit('prodTemplateSave', true)
      },
      clickedTemplateCancel () {
        this.$store.commit('prodTemplateCancel', true)
      },
      clickedSecTemplateSave () {
        this.$store.commit('prodSecTemplateSave', true)
      },
      clickedSecTemplateCancel () {
        this.$store.commit('prodSecTemplateCancel', true)
      }
    }
  }
</script>