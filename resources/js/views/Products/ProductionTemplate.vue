<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
        Produkte
      <p class="subtitle">
        Übersicht aller aktuell in der Datenbank befindlichen Produkte
      </p>
      <router-link slot="right" to="/" class="button">
        Dashboard
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <card-component class="has-table has-mobile-sort-spaced" title="Production template" icon="account-multiple">
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

          @dblclick="dbRowClickHandler"

          backend-sorting
          :default-sort-direction="defaultSortOrder"
          :default-sort="[sortField, sortOrder]"
          @sort="onSort"

          backend-filtering
          @filters-change="onFilterChange"

          :data="productionTemplatesData">

          <template slot-scope="props">
            <b-table-column label="id" field="id" sortable>
              {{ props.row.id }}
            </b-table-column>
            <b-table-column label="st_article_nr" field="st_article_nr" sortable>
              {{ props.row.st_article_nr }}
            </b-table-column>
            <b-table-column label="production_flow" field="production_flow" sortable>
              {{ props.row.production_flow[0] }}
            </b-table-column>
            <b-table-column custom-key="actions" class="is-actions-cell">
              <div class="buttons is-right">
                <button class="button is-small is-danger" type="button" @click.prevent="trashModal(props.row)">
                  <b-icon icon="trash-can" size="is-small"/>
                </button>
              </div>
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
        </b-table>
      </card-component>
      <div class="json-editor">
        <vue-json-editor v-model="jsonProductFlow" :show-btns="false" :expandedOnStart="false" @json-change="onJsonChange"/>
      </div>
    </section>
  </div>
</template>

<script>
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import BField from "buefy/src/components/field/Field";
  import vueJsonEditor from 'vue-json-editor'
  import debounce from 'lodash/debounce'

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, Notification, vueJsonEditor},
    computed: {
      titleStack () {
        return [
          'Production',
          'Template'
        ]
      }
    },
    data () {
      return {
        isShow: false,
        tableRowData: {},
        jsonProductFlow: [
          {
            required: 'false',
            step_name: 'visual.test.1',
            production_section_template: 1,
          },
          {
            required: 'false',
            step_name: 'backlight.test.1',
            production_section_template: 2,
          },
          {
            required: 'false',
            step_name: 'touch.test.1',
            production_section_template: 3,
          },
          {
            required: 'false',
            step_name: 'tilt.test.1',
            production_section_template: 4,
          }
        ],
        selectedNum: null,
        isModalActive: false,
        trashObject: null,
        productionTemplatesData: [],
        isLoading: false,
        paginated: false,
        perPage: 10,
        checkedRows: [],
        sortField:'',
        sortOrder:'asc',
        defaultSortOrder:'asc',
        page: 1,
        total: 0,
        filterValues: '{}',
      }
    },
    created () {
      this.getData()
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
        console.warn('filter', Object.entries(filter));
        this.filterValues = '';
        this.filterValues = encodeURIComponent(JSON.stringify(filter));
        this.getData()
        this.getFilteringURL()
      }, 250),
      getFilteringURL () {
        if(this.dataUrl){
          const paramsGeneral = [
            `enhanced=0`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `filter=${this.filterValues}`
          ].join('&')

          const paramsEnhance = [
            `enhanced=1`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `filter=${this.filterValues}`
          ].join('&')

          this.filterGeneralUrl = this.dataUrl + '/excel?' + paramsGeneral
          this.filterEnhancedUrl = this.dataUrl + '/enhancedExcel?' + paramsEnhance
        }
      },
      getData () {
        this.isLoading = true
        const params = [
          `size=${this.perPage}`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `filter=${this.filterValues}`
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
      onJsonChange (value) {
        this.productionTemplatesData[this.selectedNum - 1].production_flow = []
        this.productionTemplatesData[this.selectedNum - 1].production_flow = value
      },
      dbRowClickHandler (rowData) {
        this.selectedNum = rowData.id
        this.jsonProductFlow = []
        this.jsonProductFlow = rowData.production_flow
      },
    }
  }
</script>

