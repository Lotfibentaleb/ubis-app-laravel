<template>
  <div class="production-template-table">
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

            :data="pdTemplateData">

      <template slot-scope="props">
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.articleNr')" field="st_article_nr" searchable>
          <template #searchable="props">
            <b-autocomplete v-model="filterArticleNr" clearable />
          </template>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.productionFlow')" field="production_flow">
          {{ JSON.stringify(props.row.production_flow[0]).substring(0, 40) + ' ...' }}
          <font-awesome-icon :title="JSON.stringify(props.row.production_flow, null, 2)" icon="comment-medical" >
          </font-awesome-icon>
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.createdAt')" field="created_at" sortable searchable>
          <template #searchable="props">
            <date-range-picker
                    ref="picker"
                    v-model="dateRangeCreatedAt"
                    opens="left"
                    :ranges="ranges"
                    @update="updateDateRangeCreatedAt"
            >
              <template v-if="isDateRangeCreatedAt" v-slot:input="picker" style="min-width: 350px;">
                {{ picker.startDate | moment("DD.MM.YYYY") }} - {{ picker.endDate | moment("DD.MM.YYYY") }}
                <span v-if="isDateRangeCreatedAt" class="icon is-right is-clickable" style="opacity: 20%">
                  <i class="mdi mdi-close-circle mdi-24px" @click="clearCreatedAt" />
                </span>
              </template>
              <template v-else v-slot:input="picker" style="min-width: 350px;">
                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
              </template>
            </date-range-picker>
          </template>
          {{ getLocalTime(props.row.created_at) }}
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.updatedAt')" field="updated_at" sortable searchable>
          <template #searchable="props">
            <date-range-picker
                    ref="picker"
                    v-model="dateRangeUpdatedAt"
                    opens="left"
                    :ranges="ranges"
                    @update="updateDateRangeUpdatedAt"
            >
              <template v-if="isDateRangeUpdatedAt" v-slot:input="picker" style="min-width: 350px;">
                {{ picker.startDate | moment("DD.MM.YYYY") }} - {{ picker.endDate | moment("DD.MM.YYYY") }}
                <span v-if="isDateRangeUpdatedAt" class="icon is-right is-clickable" style="opacity: 20%">
                  <i class="mdi mdi-close-circle mdi-24px" @click="clearUpdatedAt" />
                </span>
              </template>
              <template v-else v-slot:input="picker" style="min-width: 350px;">
                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
              </template>
            </date-range-picker>
          </template>
          {{ getLocalTime(props.row.updated_at) }}
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
            <option value="10">10 {{$gettext('productionFlowsPage.table.perPage')}}</option>
            <option value="20">20 {{$gettext('productionFlowsPage.table.perPage')}}</option>
            <option value="50">50 {{$gettext('productionFlowsPage.table.perPage')}}</option>
            <option value="100">100 {{$gettext('productionFlowsPage.table.perPage')}}</option>
            <option value="1000">1000 {{$gettext('productionFlowsPage.table.perPage')}}</option>
          </b-select>
        </div>
      </template>

    </b-table>
  </div>
</template>
<script>

  import { mapState } from 'vuex'
  import debounce from 'lodash/debounce'
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
  import moment from "moment"
  import BAutocomplete from "buefy/src/components/autocomplete/Autocomplete";
  import ranges from "@/constants/datePickerRange"

  export default {
    name: 'ProductTemplate',
    components: {BAutocomplete, DateRangePicker},
    props: {
      reload: {
        type: Boolean,
        default: false
      },
    },
    watch: {
      perPage:function(){
        this.getData();
      },
      selectedRow: function () {
        this.rowClickHandler()
      },
      reload: function () {
        this.getData()
      },
      filterArticleNr: function() {
        this.setFilterValues()
        this.getData()
      }
    },
    data () {
      const today = new Date()
      return {
        //filter
        filterArticleNr: '',

        isDateRangeCreatedAt: false,
        dateRangeCreatedAt: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        isDateRangeUpdatedAt: false,
        dateRangeUpdatedAt: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        ranges: ranges,
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
        filters: {},
        filterValues: '',
        pdTemplateData: [],
        // control table
        isClickedRow: false,
        selectedId: null,
        // selected measurement data
        jsonPdFlow: []
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
    computed: {
      ...mapState([
        'utcOffset'
      ])
    },
    created () {
      this.setFilterValues()
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
      onFilterChange: debounce(function (params) {
        this.filters = {}
        this.filters = params
        this.setFilterValues()
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
      setFilterValues() {
        let filter = {}
        if(this.filterArticleNr) {
          filter['st_article_nr'] = this.filterArticleNr
        }
        if(this.isDateRangeCreatedAt) {
          filter['created_at-gt'] = moment.utc(this.dateRangeCreatedAt.startDate).format()
          filter['created_at-lt'] = moment.utc(this.dateRangeCreatedAt.endDate).format()
        }
        if(this.isDateRangeUpdatedAt) {
          filter['updated_at-gt'] = moment.utc(this.dateRangeUpdatedAt.startDate).format()
          filter['updated_at-lt'] = moment.utc(this.dateRangeUpdatedAt.endDate).format()
        }
        this.filterValues = ''
        this.filterValues = encodeURIComponent(JSON.stringify(filter))
      },
      getLocalTime(date) {
        return moment.utc(date).utcOffset(this.utcOffset).format("DD.MM.YYYY / k:mm:ss")
      },
      getData () {
        this.isLoading = true
        this.isClickedRow = false
        this.hasUpdatingData = false
        const params = [
          `size=${this.perPage}`,
          `sort_by=${this.sortField}-${this.sortOrder}`,
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
                this.pdTemplateData = r.data.data
                // this.selectedRow = this.productionTemplatesData[0]
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
      clearCreatedAt() {
        const today = new Date()
        this.dateRangeCreatedAt.startDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate())
        this.dateRangeCreatedAt.endDate = today
        this.isDateRangeCreatedAt = false
        this.setFilterValues()
        this.getData()
      },
      clearUpdatedAt() {
        const today = new Date()
        this.dateRangeUpdatedAt.startDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate())
        this.dateRangeUpdatedAt.endDate = today
        this.isDateRangeUpdatedAt = false
        this.setFilterValues()
        this.getData()
      },
      updateDateRangeCreatedAt() {
        this.isDateRangeCreatedAt = true
        this.setFilterValues()
        this.getData()
      },
      updateDateRangeUpdatedAt() {
        this.isDateRangeUpdatedAt = true
        this.setFilterValues()
        this.getData()
      }
    }
  }
</script>