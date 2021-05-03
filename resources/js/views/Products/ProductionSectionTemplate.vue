<template>
  <div class="no-aside-right" v-bind:class="{'has-aside-right-edit-panel': isAsideLeftEditPanel}">
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      Produktionsabläufe
      <p class="subtitle">
        Übersicht der aktuell aktiven Produktionsabläufe je Artikel
      </p>
      <router-link slot="right" to="/" class="button">
        Dashboard
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <card-component class="has-table has-mobile-sort-spaced" title="Production Section Template" icon="package-variant-closed">
        <card-toolbar />
        <production-section-template-table  @clickedRow="clickedRow"/>
      </card-component>
      <div v-if="isClickedRow" class="json-editor">
        <v-jsoneditor v-model="jsonSecTempData" />
      </div>
      <card-component v-if="isClickedRow" class="has-table has-mobile-sort-spaced history-table" title="Änderungsverlauf" icon="package-variant-closed">
        <card-toolbar />
        <production-section-template-history-table  data-url="/production_section/history" :name="selectedName" :checkable="true"/>
      </card-component>
    </section>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import CardComponent from "../../components/CardComponent";
  import ProductionSectionTemplateTable from '@/components/ProductionSectionTemplate/ProductionSectionTemplateTable'
  import CardToolbar from '@/components/CardToolbar'
  import VJsoneditor from 'v-jsoneditor'
  import ProductionSectionTemplateHistoryTable from '@/components/ProductionSectionTemplate/ProductionSectionTemplateHistoryTable'


  export default {
    name: 'products.list',
    components: {TitleBar, HeroBar, CardComponent, ProductionSectionTemplateHistoryTable, CardToolbar, VJsoneditor, ProductionSectionTemplateTable},
    watch: {
      jsonSecTempData: function () {
        this.onJsonChange()
      },
      isClickedSecTemplateSave: function () {
        this.saveJsonData()
      },
      isClickedSecTemplateCancel: function () {
        this.cancelJsonData()
      }
    },
    computed: {
      titleStack () {
        return [
          'Production',
          'Section Template'
        ]
      },
      ...mapState([
        'isAsideLeftEditPanel',
        'isClickedSecTemplateSave',
        'isClickedSecTemplateCancel'
      ])
    },
    data () {
      return {
        // section template table component
        selectedName: '',
        selectedSecTempId: null,

        // json edit component
        isClickedRow: false,
        jsonSecTempData: [],
        prevJsonSecTempData: [],
        isChangedJson: false,
      }
    },
    created () {
      this.$store.commit('editPanel',false)
      this.$store.commit('fromTemp',false)
      this.$store.commit('fromSecTemp',false)
      this.$store.commit('fromProd', false)
    },
    methods: {
      clickedRow (data) {
        this.isChangedJson = false
        this.isClickedRow = true
        this.selectedSecTempId = data.id
        this.selectedName = data.name
        this.jsonSecTempData = data
        this.prevJsonSecTempData =this.jsonSecTempData
      },

      // control json editor component
      onJsonChange () {
        if (this.jsonSecTempData != this.prevJsonSecTempData) {
          this.isChangedJson = true
        }
        if (this.isChangedJson) {
          this.$store.commit('editPanel', true)
          this.$store.commit('fromSecTemp', true)
        }
      },
      saveJsonData () {
        if (this.isClickedSecTemplateSave) {
          this.$store.commit('editPanel', false)
          this.$store.commit('fromSecTemp', false)
          this.$store.commit('prodSecTemplateSave', false)
          this.updateMeasurementData()
        }
      },
      cancelJsonData () {
        if (this.isClickedSecTemplateCancel) {
          this.$store.commit('editPanel', false)
          this.$store.commit('fromSecTemp', false)
          this.$store.commit('prodSecTemplateCancel', false)
        }
      },

      // update measurement data
      updateMeasurementData () {
        let method = 'put'
        let url = `/production_section/${this.selectedSecTempId}`
        let data = {
          description: this.jsonSecTempData.description,
          group: this.jsonSecTempData.group,
          data: JSON.stringify(this.jsonSecTempData.data),
          render_hint: this.jsonSecTempData.render_hint
        }
        axios({
          method,
          url,
          data
        }).then( r => {
          let infoMessage = `Production Section Template update success`
          this.$buefy.snackbar.open({
            message: infoMessage,
            queue: false
          })
          this.hasUpdatingData = false
        }).catch( err => {
          let message = `Fehler: ${err.message}`
          if( err.response.status == 404){
            message = `Product update failed`
          }
          this.$buefy.toast.open({
            message: message,
            type: 'is-danger',
            queue: false
          })
        }).finally(() => {

        })
      }

    }
  }
</script>