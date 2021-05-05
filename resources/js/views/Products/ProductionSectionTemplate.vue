<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      Produktionsabläufe
      <p class="subtitle">
        Übersicht der aktuell aktiven Produktionsabläufe je Artikel
      </p>
      <router-link slot="right" to="/products/section/new-template" class="button">
        New template
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <div class="columns">
        <modal-update-check :is-active="isShowUpdateCheckModal"  @confirm="confirmUpdate" @cancel="cancelUpdate"/>
        <div class="column">
          <card-component class="has-table has-mobile-sort-spaced" title="Production Section Template" icon="package-variant-closed">
            <production-section-template-table  @clickedRow="clickedRow" :reset="isResetSecTempTable"/>
          </card-component>
          <card-component v-if="isClickedRow" class="has-table has-mobile-sort-spaced history-table" title="Änderungsverlauf" icon="package-variant-closed">
            <card-toolbar />
            <production-section-template-history-table  data-url="/production_section/history" :name="selectedName" :checkable="true"/>
          </card-component>
        </div>
        <div v-if="isClickedRow">
          <div class="column">
            <card-component title="Produkt Komponenten" class="has-mobile-sort-spaced" icon="lead-pencil">
              <div class="level">
                <div class="level-left">
                  <div>
                  </div>
                </div>
                <div class="level-right">
                  <div><b-button class="btn btn-ok" @click="savePdSecTemData" :disabled="!hasUpdatingData">Save</b-button></div>
                </div>
              </div>
              <b-field label="Darstellung">
                <b-select placeholder="default" v-model="selectedRenderHint" required expanded>
                  <option v-for="(render_hints, index) in formHelper.render_hints" :key="index" :value="render_hints">
                    {{ render_hints }}
                  </option>
                </b-select>
              </b-field>
              <b-field label="Gruppe">
                <b-select placeholder="default" v-model="selectedGroup" required expanded>
                  <option v-for="(groups, index) in this.formHelper.groups" :key="index" :value="groups">
                    {{ groups }}
                  </option>
                </b-select>
              </b-field>
              <b-field label="Beschreibung" message="Description with using up to 255 characters" expanded>
                <b-input type="textarea" placeholder="Explain how we can help you" v-model="selectedDescription" maxlength="255" required/>
              </b-field>
              <b-field label="Konfiguration" message="Messplatz Konfiguration" >
                <v-jsoneditor v-model="selectedJsonData" />
              </b-field>
              <b-field>
                <b-message v-if="!isValidSchema" title="Schema Error!" type="is-danger" aria-close-label="Close message" has-icon>
                  <p>path: {{schemaErrorData.instancePath}}</p>
                  <p>message: {{schemaErrorData.message}}</p>
                  <p v-if="schemaErrorData.message == errorMessage.enumValues">allowed values: {{JSON.stringify(schemaErrorData.params.allowedValues)}}</p>
                  <b-tooltip :label="JSON.stringify(schemaData.schema, null, 2)" position="is-left" size="is-large" multilined>
                    <a href="#"><p>See schema</p></a>
                  </b-tooltip>
                </b-message>
              </b-field>
              <b-field>
                <b-message v-if="isValidSchema" auto-close title="Success" type="is-success" aria-close-label="Close message" has-icon>
                  <p>JSON Schema Validation Success</p>
                </b-message>
              </b-field>
            </card-component>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import Ajv from 'ajv'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import CardComponent from "../../components/CardComponent";
  import ProductionSectionTemplateTable from '@/components/ProductionSectionTemplate/ProductionSectionTemplateTable'
  import CardToolbar from '@/components/CardToolbar'
  import VJsoneditor from 'v-jsoneditor'
  import ProductionSectionTemplateHistoryTable from '@/components/ProductionSectionTemplate/ProductionSectionTemplateHistoryTable'
  import ModalUpdateCheck from '@/components/ProductsTemplate/ModalUpdateCheck'
  import ProductionSectionSchema from '@/schema/ProductionSectionSchema'

  const ajv = new Ajv()

  export default {
    name: 'products.list',
    components: {TitleBar, HeroBar, CardComponent, ProductionSectionTemplateHistoryTable, ModalUpdateCheck, CardToolbar, VJsoneditor, ProductionSectionTemplateTable},
    watch: {
      selectedJsonData: function() {
        const valid = ajv.validate(this.schemaData.schema, this.selectedJsonData)
        if(!valid) {
          this.isValidSchema = false
          this.schemaErrorData = ajv.errors[0]
        } else {
          this.isValidSchema = true
          this.changedJsonData()
        }
      },
      selectedGroup: function() {
        this.changedJsonData()
      },
      selectedRenderHint: function() {
        this.changedJsonData()
      },
      selectedDescription: function() {
        this.changedJsonData()
      }
    },
    computed: {
      titleStack () {
        return [
          'Production',
          'Section Template'
        ]
      },
    },
    data () {
      return {
        // section template table component
        selectedName: '',
        selectedSecTempId: null,
        tempDataFromPdSecTemTable: [],
        isShowUpdateCheckModal: false,

        // json edit component
        isClickedRow: false,
        jsonSecTempData: [],
        prevJsonSecTempData: [],

        selectedJsonData: [],
        selectedGroup: null,
        selectedRenderHint: null,
        selectedDescription: '',

        hasUpdatingData: false,
        isResetSecTempTable: false,
        formHelper: null,

        //json schema
        isValidSchema: true,
        schemaErrorData: {},
        errorMessage: {
          enumValues: 'must be equal to one of the allowed values'
        },
        schemaData: ProductionSectionSchema,
      }
    },
    mounted() {
      this.updateFormHelper()
    },
    methods: {
      clickedRow(data) {
        if(this.hasUpdatingData) {
          this.isShowUpdateCheckModal = true
          this.tempDataFromPdSecTemTable = data
        } else {
          this.isClickedRow = true
          this.selectedSecTempId = data.id
          this.selectedName = data.name
          this.jsonSecTempData = data
          this.prevJsonSecTempData =this.jsonSecTempData

          this.selectedRenderHint = this.jsonSecTempData.render_hint
          this.selectedGroup = this.jsonSecTempData.group
          this.selectedDescription = this.jsonSecTempData.description

          this.selectedJsonData = this.jsonSecTempData.data
        }
      },
      changedJsonData() {
        if(this.selectedRenderHint != this.jsonSecTempData.render_hint) {
          this.hasUpdatingData = true
        } else if(this.selectedGroup != this.jsonSecTempData.group) {
          this.hasUpdatingData = true
        } else if(this.selectedJsonData != this.jsonSecTempData.data) {
          this.hasUpdatingData = true
        } else if(this.selectedDescription != this.jsonSecTempData.description) {
          this.hasUpdatingData = true
        } else {
          this.hasUpdatingData = false
        }
      },
      savePdSecTemData() {
        if(!this.isValidSchema) {
          this.$buefy.snackbar.open({
            type: 'is-danger',
            message: 'Json schema Validation Error',
            queue: false
          })
          return
        }
        this.updateMeasurementData()
        this.isResetSecTempTable ? this.isResetSecTempTable = false : this.isResetSecTempTable = true
      } ,
      confirmUpdate() {
        this.updateMeasurementData()
        this.isShowUpdateCheckModal = false
        this.isResetSecTempTable ? this.isResetSecTempTable = false : this.isResetSecTempTable = true
      },
      cancelUpdate() {
        this.isShowUpdateCheckModal = false
        this.hasUpdatingData = false
        this.selectedSecTempId = this.tempDataFromPdSecTemTable.id
        this.selectedName = this.tempDataFromPdSecTemTable.name
        this.jsonSecTempData = this.tempDataFromPdSecTemTable
        this.prevJsonSecTempData =this.jsonSecTempData

        this.selectedRenderHint = this.tempDataFromPdSecTemTable.render_hint
        this.selectedGroup = this.tempDataFromPdSecTemTable.group
        this.selectedDescription = this.tempDataFromPdSecTemTable.description
        this.selectedJsonData = this.tempDataFromPdSecTemTable.data
      },
      updateFormHelper() {
        axios.get(`/production_section/form_support`, {
          headers: {
            "Content-Type": "application/json"
          }
        })
            .then( result => {
              this.formHelper = result.data;
            })
            .catch( error => {
              console.log('Error', error.message);
            })
            .finally(() => {
            })
      },

      // update measurement data
      updateMeasurementData() {
        let method = 'put'
        let url = `/production_section/${this.selectedSecTempId}`
        let data = {
          description: this.selectedDescription,
          group: this.selectedGroup,
          data: JSON.stringify(this.selectedJsonData),
          render_hint: this.selectedRenderHint
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