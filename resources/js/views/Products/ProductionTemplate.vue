<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{$t('productionFlowsPage.heroBar.title')}}
      <p class="subtitle">
        {{$t('productionFlowsPage.heroBar.subTitle')}}
      </p>
      <router-link slot="right" to="/products/new-template" class="button">
        {{$t('productionFlowsPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <div class="columns">
        <modal-update-check :is-active="isShowUpdateCheckModal"  @confirm="confirmUpdate" @cancel="cancelUpdate"/>
        <b-modal :active="invalidSecId" has-modal-card>
          <div class="modal-card section-template-modal">
            <header class="modal-card-head">
              <p class="modal-card-title">Produktionskonfiguration für diesen Arbeitsschritt auswählen</p>
            </header>
            <section class="modal-card-body">
              <div class="has-text-right modal-section-selector">
                <b-dropdown v-model="selectedSectionIndex" aria-role="list" class="section-template-drop-down">
                  <template #trigger>
                    <b-button
                            icon-right="menu-down">
                      {{setDropDownLabel(selectedSectionIndex)}}
                    </b-button>
                  </template>
                  <b-dropdown-item v-for="(availableSectionName, index) in availableSectionNames" :value="index" :key="index" aria-role="listitem">
                    <div class="media">
                      <div class="media-content">
                        <h3>{{availableSectionName + ' [' + availableSectionIds[index] + ']'}}</h3>
                        <small>{{'group' + ': ' + availableSectionGroups[index]}}</small>
                      </div>
                    </div>
                  </b-dropdown-item>
                </b-dropdown>
              </div>
            </section>
            <footer class="modal-card-foot custom-foot">
              <b-button class="btn btn-ok"  @click="confirmSecIdModal">Ok</b-button>
            </footer>
          </div>
        </b-modal>
        <div class="column">
          <card-component class="has-table has-mobile-sort-spaced" :title="$t('productionFlowsPage.table.title')" icon="package-variant-closed">
            <product-template-table @clickedRow="clickedRow" :reload="isReload"/>
          </card-component>

          <card-component v-if="isClickedRow" class="has-table has-mobile-sort-spaced history-table" title="Änderungsverlauf" icon="package-variant-closed">
            <card-toolbar />
            <product-template-history data-url="/production_flow/history" :article_nr="selectedArticleNr" :checkable="true" />
          </card-component>
        </div>
        <div v-if="isClickedRow">
          <div  class="column">
            <card-component :title="$t('productionFlowsPage.settingPanel.title')" class="has-mobile-sort-spaced" icon="lead-pencil">
              <div class="level">
                <div class="level-left">
                  <div>
                  </div>
                </div>
                <div class="level-right">
                  <div><b-button class="btn btn-ok" :disabled="!hasUpdatingData" @click="savePdFlowData">Save</b-button></div>
                </div>
              </div>
              <b-field :label="$t('productionFlowsPage.table.fields.articleNr')" :message="$t('productionFlowsPage.settingPanel.fieldMessage')">
                <b-input type="text" placeholder="Artikel-Nr." :value="selectedArticleNr" readonly/>
              </b-field>
              <b-field :label="$t('productionFlowsPage.table.fields.productionFlow')" :message="$t('productionFlowsPage.table.fields.productionFlow')" >
                <v-jsoneditor ref="jeditor" v-model="jsonPdFlow" :options="options"/>
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
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import CardToolbar from '@/components/CardToolbar'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import ProductTemplateHistory from '@/components/ProductsTemplate/ProductTemplateHistory'
  import ProductTemplateTable from '@/components/ProductsTemplate/ProductTemplateTable'
  import ModalUpdateCheck from '@/components/ProductsTemplate/ModalUpdateCheck'
  import ModalBox from '@/components/ModalBox'
  import BField from "buefy/src/components/field/Field"
  import VJsoneditor from 'v-jsoneditor'
  import BInput from "buefy/src/components/input/Input"
  import BButton from "buefy/src/components/button/Button"
  import ProductionFlowSchema from '@/schema/ProductionFlowSchema'

  const ajv = new Ajv()

  export default {
    name: 'products.list',
    components: {
      BButton,
      BInput,
      BField, HeroBar, TitleBar, CardComponent, CardToolbar, VJsoneditor, ModalBox, Notification, ProductTemplateHistory, ProductTemplateTable, ModalUpdateCheck},
    watch:{
      jsonPdFlow: function() {
        const valid = ajv.validate(this.schemaData.schema, this.jsonPdFlow)
        if(!valid) {
          this.isValidSchema = false
          this.schemaErrorData = ajv.errors[0]
        } else {
          this.isValidSchema = true
          this.changedJsonData()
        }
      }
    },
    computed: {
      titleStack() {
        return [
          this.$t('productionFlowsPage.titleBar.main'),
          this.$t('productionFlowsPage.titleBar.sub1')
        ]
      },
    },
    data () {
      return {
        isClickedRow: false,
        tempDataFromPdTemTable: [],
        prevJsonPdFlow: [],
        jsonPdFlow: [],
        selectedId: null,
        selectedArticleNr: null,
        hasUpdatingData: false,
        invalidSecId: false,

        //to show the updating checking modal when loading another row data
        isShowUpdateCheckModal: false,

        //production section template information
        pdSecTemData: [],
        availableSectionIds: [],
        availableSectionNames: [],
        availableSectionGroups: [],
        selectedSectionIndex: 0,

        //to reload products template table data
        isReload: false,

        //json schema
        options: {
          mode: 'tree',
          modes: ['code', 'tree'], // allowed modes
          enableTransform: false,
          enableSort: false,
        },
        isValidSchema: true,
        schemaErrorData: {},
        errorMessage: {
          enumValues: 'must be equal to one of the allowed values'
        },
        schemaData: ProductionFlowSchema,
      }
    },
    created() {
      this.getSectionTemplateData()
    },
    methods: {
      clickedRow(data) {
        if(this.hasUpdatingData) {
          this.isShowUpdateCheckModal = true
          this.tempDataFromPdTemTable = data
        } else {
          this.isClickedRow = true
          this.selectedId = data.selectedId
          this.selectedArticleNr = data.articleNr
          this.jsonPdFlow = data.jsonPdFlow
          this.prevJsonPdFlow = data.jsonPdFlow
          setTimeout(function(){ this.$refs.jeditor.editor.expandAll(); }.bind(this), 50);
        }
      },
      changedJsonData() {
        if(this.jsonPdFlow != this.prevJsonPdFlow) {
          this.hasUpdatingData = true
          if(!this.checkingSectionId()) {
            this.invalidSecId = true
          }
        }
      },
      confirmUpdate() {
        this.updateProductionFlow()
        this.isShowUpdateCheckModal = false

        //to reload table data
        this.isReload ? this.isReload = false : this.isReload = true
      },
      cancelUpdate() {
        this.isShowUpdateCheckModal = false
        this.hasUpdatingData = false
        this.selectedId = this.tempDataFromPdTemTable.selectedId
        this.selectedArticleNr = this.tempDataFromPdTemTable.articleNr
        this.jsonPdFlow = this.tempDataFromPdTemTable.jsonPdFlow
        this.prevJsonPdFlow = this.tempDataFromPdTemTable.jsonPdFlow
      },
      confirmSecIdModal() {
        this.invalidSecId = false
        this.setSelectedSectionId()
      },
      savePdFlowData() {
        if(!this.isValidSchema) {
          this.$buefy.snackbar.open({
            type: 'is-danger',
            message: 'Json schema Validation Error',
            queue: false
          })
          return
        }
        if (!this.checkingSectionId()) {
          this.invalidSecId = true
        } else {
          this.updateProductionFlow()
          //to reload table data
          this.isReload ? this.isReload = false : this.isReload = true
        }
      },
      checkingSectionId() {
        let countTrueSectionId = 0
        this.jsonPdFlow.forEach(pdFlowItem => {
          if (this.availableSectionIds.includes(pdFlowItem.production_section_template)) {
            countTrueSectionId = countTrueSectionId + 1
          }
        })
        if (countTrueSectionId != this.jsonPdFlow.length ) {
          return false
        } else { return true }
      },
      setDropDownLabel(index) {
        return this.availableSectionNames[index] + ' [' + this.availableSectionIds[index] + ']'
      },
      setSelectedSectionId() {
        this.jsonPdFlow.forEach((pdFlowItem, index) => {
          if (!this.availableSectionIds.includes(pdFlowItem.production_section_template)) {
            this.jsonPdFlow[index].production_section_template = this.availableSectionIds[this.selectedSectionIndex]
          }
        })
      },
      updateProductionFlow() {
        let method = 'put'
        let url = `/production_flow/${this.selectedId}`
        let data = {
          st_article_nr: this.selectedArticleNr,
          production_flow: JSON.stringify(this.jsonPdFlow)
        }
        axios({
          method,
          url,
          data
        }).then( r => {
          let infoMessage = `Production_Flow update success`
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
      },
      getSectionTemplateData() {
        const params = [
          `size=100`,
          `page=1`
        ].join('&')
        const fetchUrl = '/production_section'
        axios.create({
          headers: {
            'Content-Type': 'application/json',
          }
        })
            .get(fetchUrl+'?'+params)
            .then(r => {
              this.isLoading = false
              if (r.data && r.data.data) {
                this.pdSecTemData = r.data.data
                this.pdSecTemData.forEach(pdSectionTempItem => {
                  this.availableSectionIds.push(pdSectionTempItem.id)
                  this.availableSectionNames.push(pdSectionTempItem.name)
                  this.availableSectionGroups.push(pdSectionTempItem['group'])
                })
              }
            })
            .catch( err => {
              this.isLoading = false
              this.$buefy.toast.open({
                message: `Error: ${err.message}`,
                type: 'is-danger',
                queue: false
              })
            })
      }
    }
  }
</script>

