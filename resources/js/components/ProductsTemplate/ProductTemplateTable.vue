<template>
  <div>
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

            :data="pdTemplateData">

      <template slot-scope="props">
        <b-table-column label="Artikel-Nr." field="st_article_nr" searchable>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column label="Produktions-Ablauf" field="production_flow">
          <b-tooltip :label="JSON.stringify(props.row.production_flow, null, 2)"  position="is-right" :delay="1000" multilined size="is-large">
            {{ JSON.stringify(props.row.production_flow[0]).substring(0, 40) + ' ...' }}
          </b-tooltip>
        </b-table-column>
        <b-table-column label="Erstellt" field="created_at" sortable>
          {{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss")}}
        </b-table-column>
        <b-table-column label="Geändert" field="updated_at" sortable>
          {{ props.row.updated_at | moment("DD.MM.YYYY / k:mm:ss")}}
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
  </div>
</template>
<script>

  import debounce from 'lodash/debounce'

  export default {
    name: 'ProductTemplate',
    props: {
      reload: {
        type: Boolean,
        default: false
      },
    },
    watch: {
      selectedRow: function () {
        this.rowClickHandler()
      },
      reload: function () {
        this.getData()
      }
    },
    data () {
      return {
        // initial table
        checkedRows: [],
        selectedRow: {},
        isLoading: false,
        total: 0,
        perPage: 10,
        page: 1,
        sortField:'',
        sortOrder:'asc',
        defaultSortOrder:'asc',
        filterValues: '',
        pdTemplateData: [],
        // control table
        isClickedRow: false,
        selectedId: null,
        // selected measurement data
        jsonPdFlow: []
      }
    },
    created () {
      this.getData()
    },
    methods: {
      ///////////////////////////  table  ///////////////////////////
      onPageChange (page) {
        this.page = page
        this.getData ()
      },
      onSort (field, order) {
        this.sortField = field
        this.sortOrder = order
        this.getData()
      },
      onFilterChange: debounce(function (filter) {
        this.filterValues = '';
        this.filterValues = filter.st_article_nr ? filter.st_article_nr : ''
        this.getData()
      }, 250),
      ///////////////////////////////////////////////////////////////

      rowClickHandler () {
        this.selectedId = this.selectedRow.id
        this.jsonPdFlow = this.selectedRow.production_flow
        this.isClickedRow = true
        let data = {
          selectedId: this.selectedId,
          articleNr: this.selectedRow.st_article_nr,
          jsonPdFlow: this.jsonPdFlow
        }
        this.$emit('clickedRow', data)
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
                this.pdTemplateData = r.data.data
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