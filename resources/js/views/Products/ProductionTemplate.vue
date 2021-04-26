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
      <card-component class="has-table has-mobile-sort-spaced" title="Production flow template for Articles" icon="package-variant-closed">
        <card-toolbar />
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
            <b-table-column label="id" field="id">
              {{ props.row.id }}
            </b-table-column>
            <b-table-column label="st_article_nr" field="st_article_nr" searchable>
              {{ props.row.st_article_nr }}
            </b-table-column>
            <b-table-column label="production_flow" field="production_flow">
              {{ props.row.production_flow[0] }}
            </b-table-column>
            <b-table-column label="created_at" field="created_at" sortable>
              {{ props.row.created_at.split('T')[0] }}
            </b-table-column>
            <b-table-column label="updated_at" field="updated_at" sortable>
              {{ props.row.updated_at.split('T')[0] }}
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
      <div class="json-editor">
        <vue-json-editor v-model="jsonProductFlow" :show-btns="false" :expandedOnStart="false" @json-change="onJsonChange"/>
      </div>
    </section>
  </div>
</template>

<script>
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import CardToolbar from '@/components/CardToolbar'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import BField from "buefy/src/components/field/Field";
  import vueJsonEditor from 'vue-json-editor'
  import debounce from 'lodash/debounce'

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, CardToolbar, Notification, vueJsonEditor},
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
        selectedId: null,
        selectedIndex: null,
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
        filterValues: '',
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
        this.filterValues = '';
        this.filterValues = filter.st_article_nr ? filter.st_article_nr : ''
        this.getData()
      }, 250),
      getData () {
        this.isLoading = true
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
        this.productionTemplatesData.forEach((pdTemplateItem, index) => {
          if (pdTemplateItem.id === this.selectedId) {
            this.selectedIndex = index
          }
        })
        this.productionTemplatesData[this.selectedIndex].production_flow = []
        this.productionTemplatesData[this.selectedIndex].production_flow = value

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
      dbRowClickHandler (rowData) {
        this.selectedId = rowData.id
        this.jsonProductFlow = []
        this.jsonProductFlow = rowData.production_flow
      },
    }
  }
</script>

