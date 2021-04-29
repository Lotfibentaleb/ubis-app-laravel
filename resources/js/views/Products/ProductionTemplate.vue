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
      <modal-update-check :is-active="isShowUpdateCheckModal"  @confirm="confirmUpdate" @cancel="cancelUpdate"/>
      <b-modal :active="showSectionModal" has-modal-card>
        <div class="modal-card section-template-modal">
          <header class="modal-card-head">
            <p class="modal-card-title">Produktionskonfiguration für diesen Arbeitsschritt auswählen</p>
          </header>
          <section class="modal-card-body">
            <p>Selected section id: {{availableSectionIds[selectedSectionIndex]}}</p>
            <div class="has-text-right modal-section-selector">
              <div class="section-selector-area">
                <h1 class="section-template-text">Name</h1>
                <b-select class="section-selector-width" v-model="selectedSectionIndex">
                  <option v-for="(availableSectionName, index) in availableSectionNames" :value="index">{{availableSectionName}}</option>
                </b-select>
              </div>
              <div class="section-group-area">
                <h1 class="section-template-text">Group</h1>
                <b-input class="section-group-width" type="text" :value="selectedSectionGroup" readonly />
              </div>
            </div>
          </section>
          <footer class="modal-card-foot custom-foot">
            <b-button class="btn btn-ok"  @click="confirmModal">Ok</b-button>
          </footer>
        </div>
      </b-modal>
      <card-component class="has-table has-mobile-sort-spaced" title="Produktionsablauf je Artikel" icon="package-variant-closed">
        <b-table
                :checked-rows.sync="checkedRows"
                :checkable="true"
                :loading="isLoading"
                paginated
                backend-pagination
                :total="total"
                :per-page="perPage"
                :striped="true"
                :hoverable="true"
                default-sort="name"
                @page-change="onPageChange"
                :selected.sync="selectedRow"
                backend-sorting
                :default-sort-direction="defaultSortOrder"
                :default-sort="[sortField, sortOrder]"
                @sort="onSort"

                backend-filtering
                @filters-change="onFilterChange"

                :data="productionTemplatesData">

          <template slot-scope="props">
            <b-table-column label="Artikel-Nr." field="st_article_nr" searchable>
              {{ props.row.st_article_nr }}
            </b-table-column>
            <b-table-column label="Produktions-Ablauf" field="production_flow">
              {{ JSON.stringify(props.row.production_flow[0]) + ' ...' }}
            </b-table-column>
            <b-table-column label="Erstellt" field="created_at" sortable>
              {{ props.row.created_at | moment("DD.MM.YYYY / h:mm:ss")}}
            </b-table-column>
            <b-table-column label="Geändert" field="updated_at" sortable>
              {{ props.row.updated_at | moment("DD.MM.YYYY / h:mm:ss")}}
            </b-table-column>
          </template>

          <section class="section" slot="empty">
            <div class="content has-text-grey has-text-centered">
              <template v-if="isLoading">
                <p>
                  <b-icon icon="dots-horizontal" size="is-large"/>
                </p>
                <p>Fetching data...</p>
              </template>
              <template v-else>
                <p>
                  <b-icon icon="emoticon-sad" size="is-large"/>
                </p>
                <p>Nothing's here&hellip;</p>
              </template>
            </div>
          </section>

          <template #footer>
            <div class="has-text-right">
              <b-select v-model="perPage">
                <option value="10">10 per page</option>
                <option value="20">20 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
                <option value="1000">1000 per page</option>
              </b-select>
            </div>
          </template>

        </b-table>
      </card-component>
      <div v-if="isClickedRow" class="json-editor">
        <v-jsoneditor v-model="jsonProductFlow" />
      </div>
      <card-component v-if="isClickedRow" class="has-table has-mobile-sort-spaced history-table" title="Änderungsverlauf" icon="package-variant-closed">
        <card-toolbar />
        <product-template-history data-url="/production_flow/history" :article_nr="selectedArticleNr" :checkable="true" />
      </card-component>
    </section>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import CardToolbar from '@/components/CardToolbar'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import ProductTemplateHistory from '@/components/ProductsTemplate/ProductTemplateHistory'
  import ModalUpdateCheck from '@/components/ProductsTemplate/ModalUpdateCheck'
  import ModalBox from '@/components/ModalBox'
  import BField from "buefy/src/components/field/Field"
  import VJsoneditor from 'v-jsoneditor'
  import debounce from 'lodash/debounce'
  import BInput from "buefy/src/components/input/Input"
  import BButton from "buefy/src/components/button/Button"

  export default {
    name: 'products.list',
    components: {
      BButton,
      BInput,
      BField, HeroBar, TitleBar, CardComponent, CardToolbar, VJsoneditor, ModalBox, Notification, ProductTemplateHistory, ModalUpdateCheck},
    watch:{
      perPage: function () {
        this.getData();
      },
      selectedSectionIndex: function () {
        this.selectedSectionGroup = this.productSectionTemplateData[this.selectedSectionIndex]['group']
      },
      selectedRow: function () {
        this.rowClickHandler()
      },
      jsonProductFlow: function () {
        this.onJsonChange()
      },
      checkedRows: function () {
        this.proceedCheckedRows()
      },
      isClickedTemplateSave: function () {
        this.saveJsonData()
      },
      isClickedTemplateCancel: function () {
        this.cancelJsonData()
      }
    },
    computed: {
      titleStack () {
        return [
          'Production',
          'Template'
        ]
      },
      ...mapState([
        'isAsideLeftEditPanel',
        'isClickedTemplateSave',
        'isClickedTemplateCancel',
      ])
    },
    data () {
      return {
        isShowUpdateCheckModal: false,
        isShow: false,
        isClickedRow: false,
        tableRowData: {},
        jsonProductFlow: [],
        selectedRow: {},
        selectedArticleNr: '',
        selectedId: null,
        selectedIndex: null,
        selectedSectionIndex: 0,
        selectedSectionGroup: '',
        isModalActive: false,
        trashObject: null,
        productionTemplatesData: [],
        productSectionTemplateData: [],
        availableSectionIds: [],
        availableSectionNames: [],
        isLoading: false,
        paginated: false,
        perPage: 10,
        checkedRows: [],
        sortField:'',
        sortOrder:'asc',
        defaultSortOrder:'asc',
        page: 1,
        total: 0,
        filterValues: '',
        showSectionModal: false,
        hasUpdatingData: false,
      }
    },
    created () {
      this.getData()
      this.getSectionTemplateData()
    },
    methods: {
      onPageChange(page) {
        this.page = page
        this.getData ()
      },
      onSort(field, order) {
        this.sortField = field
        this.sortOrder = order
        this.getData()
      },
      onFilterChange: debounce(function (filter) {
        this.filterValues = '';
        this.filterValues = filter.st_article_nr ? filter.st_article_nr : ''
        this.getData()
      }, 250),
      confirmUpdate () {
        this.updateProductionFlow()
        this.isShowUpdateCheckModal = false
        this.$store.commit('editPanel', false)
      },
      cancelUpdate () {
        this.isShowUpdateCheckModal = false
        this.$store.commit('editPanel', false)
        this.getData()
      },
      confirmModal () {
        this.showSectionModal = false
        this.setSelectedSectionId()
        this.hasUpdatingData = true
        this.$store.commit('editPanel', true)
      },
      saveJsonData () {
        if (this.isClickedTemplateSave) {
          if (!this.checkSectionId()) {
            this.showSectionModal = true
          } else {
            this.updateProductionFlow()
          }
          this.hasUpdatingData = false
          this.$store.commit('editPanel', false)
          this.$store.commit('prodTemplateSave', false)
        }
      },
      cancelJsonData () {
        if (this.isClickedTemplateCancel) {
          this.hasUpdatingData = false
          this.$store.commit('editPanel', false)
          this.$store.commit('prodTemplateCancel', false)
          this.getData()
        }
      },
      onJsonChange () {
        this.productionTemplatesData.forEach((pdTemplateItem, index) => {
          if (pdTemplateItem.id === this.selectedId) {
            this.selectedIndex = index
          }
        })
        this.productionTemplatesData[this.selectedIndex].production_flow = []
        this.productionTemplatesData[this.selectedIndex].production_flow = this.jsonProductFlow
        if (!this.checkSectionId()) { this.showSectionModal = true }
      },
      checkSectionId () {
        let countTrueSectionId = 0
        this.productionTemplatesData[this.selectedIndex].production_flow.forEach(pdFlowItem => {
          if (this.availableSectionIds.includes(pdFlowItem.production_section_template)) {
            countTrueSectionId = countTrueSectionId + 1
          }
        })
        if (countTrueSectionId != this.productionTemplatesData[this.selectedIndex].production_flow.length ) {
          return false
        } else { return true }
      },
      setSelectedSectionId () {
        this.productionTemplatesData[this.selectedIndex].production_flow.forEach((pdFlowItem, index) => {
          if (!this.availableSectionIds.includes(pdFlowItem.production_section_template)) {
            this.productionTemplatesData[this.selectedIndex].production_flow[index].production_section_template = this.availableSectionIds[this.selectedSectionIndex]
          }
        })
      },
      rowClickHandler () {
        if (this.hasUpdatingData) {
          this.isShowUpdateCheckModal = true
        }
        this.selectedArticleNr = this.selectedRow.st_article_nr
        this.selectedId = this.selectedRow.id
        this.jsonProductFlow = this.selectedRow.production_flow
        this.isClickedRow = true
      },
      proceedCheckedRows () {
        // this.$store.commit('editPanel', true)
      },
      updateProductionFlow () {
        let method = 'put'
        let url = `/production_flow/${this.selectedId}`
        let data = {
          st_article_nr: this.productionTemplatesData[this.selectedIndex].st_article_nr,
          production_flow: JSON.stringify(this.productionTemplatesData[this.selectedIndex].production_flow)
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
      getSectionTemplateData () {
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
                this.productSectionTemplateData = r.data.data
                this.selectedSectionGroup = this.productSectionTemplateData[this.selectedSectionIndex]['group']
                this.productSectionTemplateData.forEach(pdSectionTempItem => {
                  this.availableSectionIds.push(pdSectionTempItem.id)
                  this.availableSectionNames.push(pdSectionTempItem.name)
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
      },
      getData () {
        this.isLoading = true
        this.isClickedRow = false
        this.hasUpdatingData = false
        const params = [
          `size=${this.perPage}`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `article_nr=${this.filterValues}`
        ].join('&')

        const fetchUrl = '/production_flow'

        axios.create({
          headers: {
            'Content-Type': 'application/json',
          }
        })
            .get(fetchUrl+'?'+params)
            .then(r => {
              this.isLoading = false
              if (r.data && r.data.data) {
                this.perPage = r.data.meta.per_page
                this.total = r.data.meta.total
                this.page = r.data.meta.current_page
                this.productionTemplatesData = r.data.data
                // this.selectedRow = this.productionTemplatesData[0]
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

