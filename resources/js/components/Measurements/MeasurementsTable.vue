<template>
  <div>
    <div class="level">
      <div class="level-right"></div>
      <div class="level-left">
        <b-icon
                icon="calendar-today">
        </b-icon>
        <date-range-picker
                ref="picker"
                :timePicker="timePicker"
                :timePicker24Hour="timePicker24Hour"
                v-model="dateRange"
                @update="updateDateRange"
        >
          <template v-slot:input="picker" style="min-width: 350px;">
            <!--{{ getParamDate(picker.startDate) | date }} - {{ getParamDate(picker.endDate) | date }}-->
            {{ picker.startDate | moment("DD.MM.YYYY / k:mm:ss") }} - {{ picker.endDate | moment("DD.MM.YYYY / k:mm:ss") }}
          </template>
        </date-range-picker>
      </div>
    </div>
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
            @page-change="onPageChange"
            :selected.sync="selectedRow"
            backend-sorting
            :default-sort-direction="defaultSortOrder"
            :default-sort="[sortField, sortOrder]"
            @sort="onSort"

            backend-filtering
            @filters-change="onFilterChange"

            :data="measurementsData">

      <template slot-scope="props">
        <b-table-column :label="$gettext('measurementsPage.table.fields.articleNr')" field="st_article_nr" searchable sortable>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.serialNr')" field="st_serial_nr" searchable sortable>
          {{ props.row.st_serial_nr  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.state')" field="state">
          {{ props.row.state  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.name')" field="name">
          {{ props.row.name  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.createdBy')" field="created_by" searchable sortable>
          {{ props.row.created_by  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.createdAt')" field="created_at" searchable sortable>
          {{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss")}}
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
            <option value="10">10 {{$gettext('measurementsPage.table.perPage')}}</option>
            <option value="20">20 {{$gettext('measurementsPage.table.perPage')}}</option>
            <option value="50">50 {{$gettext('measurementsPage.table.perPage')}}</option>
            <option value="100">100 {{$gettext('measurementsPage.table.perPage')}}</option>
            <option value="1000">1000 {{$gettext('measurementsPage.table.perPage')}}</option>
          </b-select>
        </div>
      </template>

    </b-table>
  </div>
</template>
<script>

  import debounce from 'lodash/debounce'
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

  export default {
    name: 'ProductTemplate',
    components: {DateRangePicker},
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
      const today = new Date()
      return {
        dateRange: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        dateRangeValues: '{}',
        timePicker: true,
        timePicker24Hour: true,
        // initial table
        checkedRows: [],
        selectedRow: {},
        isLoading: false,
        total: 0,
        perPage: 10,
        page: 1,
        sortField:'created_at',
        sortOrder:'desc',
        defaultSortOrder:'desc',
        filterValues: '',
        measurementsData: [],
        // control table
        isClickedRow: false,
        selectedId: null,
        // selected measurement data
        jsonPdFlow: [],
        // productsearch page url
        productSearchPageUrl: ''
      }
    },
    filters: {
      dateCell (value) {
        let dt = new Date(value)

        return dt.getDate()
      },
      date (val) {
        return val ? val.toLocaleString() : ''
      }
    },
    created () {
      this.getData()
      this.productSearchPageUrl = process.env.MIX_PRODUCTS_SEARCH_PAGE_URL
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
        console.warn('filter', Object.entries(filter));
        this.filterValues = '';
        this.filterValues = encodeURIComponent(JSON.stringify(filter));
        this.getData()
      }, 250),
      ///////////////////////////////////////////////////////////////

      rowClickHandler () {
        this.selectedId = this.selectedRow.id
        this.jsonPdFlow = this.selectedRow.production_flow
        this.isClickedRow = true
        let data = {
          selectedId: this.selectedId,
          articleNr: this.selectedRow.st_article_nr
        }
        // this.$emit('clickedRow', data)
        const gotoUrl = this.productSearchPageUrl + '?products_id=' + this.selectedRow.products_id
        window.open(gotoUrl, '_blank');
      },

      getData () {
        this.isLoading = true
        this.isClickedRow = false
        const params = [
          `size=${this.perPage}`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `date_range=${this.dateRangeValues}`,
          `filter=${this.filterValues}`
        ].join('&')

        const fetchUrl = '/device_records'

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
                this.measurementsData = r.data.data
              }
            })
            .catch( err => {
              if (err.response){
                if (err.response.status == 401) {
                  document.getElementById('logout-form').submit()
                } else {
                  this.isLoading = false
                  this.$buefy.toast.open({
                    message: `Error: ${err.message}`,
                    type: 'is-danger',
                    queue: false
                  })
                }
              } else if(err.message) {
                this.isLoading = false
                this.$buefy.toast.open({
                  message: `Error: ${err.message}`,
                  type: 'is-danger',
                  queue: false
                })
              }
            })
      },
      updateDateRange() {
        this.dateRangeValues = '';
        this.dateRangeValues = encodeURIComponent(JSON.stringify(this.dateRange));
        this.getData()
      }
    }
  }
</script>
