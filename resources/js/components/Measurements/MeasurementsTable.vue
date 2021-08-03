<template>
  <div class="measurements-list-table">
    <modal-box :is-active="isModalActive" @confirm="reloadConfirm" @cancel="reloadCancel" confirmLabel="Reload">
      <p>This will modify the RESULT of measurement data. Do you want to continue?</p>
    </modal-box>
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

            :data="measurementsData">

      <template slot-scope="props">
        <b-table-column :label="$gettext('measurementsPage.table.fields.articleNr')" field="st_article_nr" searchable
                        sortable>
          <template #searchable="props">
            <b-autocomplete v-model="filterArticleNr" clearable />
          </template>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.serialNr')" field="st_serial_nr" searchable
                        sortable>
          <template #searchable="props">
            <b-autocomplete v-model="filterSerialNr" clearable />
          </template>
          {{ props.row.st_serial_nr  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.state')" field="state" searchable
                        sortable>
          <template #searchable="props">
            <b-dropdown
                    :scrollable="isScrollable"
                    :max-height="maxHeight"
                    v-model="filterState"
                    aria-role="list"
            >
              <template #trigger>
                <b-button
                        :label="filterState.text"
                        icon-right="menu-down" />
              </template>

              <b-dropdown-item
                      v-for="(menu, index) in stateOptions"
                      :key="index"
                      :value="menu" aria-role="listitem">
                <div class="media">
                  <div class="media-content">
                    <h3>{{menu.text}}</h3>
                  </div>
                </div>
              </b-dropdown-item>
            </b-dropdown>
          </template>
          {{ props.row.state  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.name')" field="name" searchable sortable>
          <template #searchable="props">
            <b-dropdown
                    :scrollable="isScrollable"
                    :max-height="maxHeight"
                    v-model="filterSection"
                    aria-role="list"
            >
              <template #trigger>
                <b-button
                        :label="filterSection.text"
                        icon-right="menu-down" />
              </template>

              <b-dropdown-item
                      v-for="(menu, index) in sectionOptions"
                      :key="index"
                      :value="menu" aria-role="listitem">
                <div class="media">
                  <div class="media-content">
                    <h3>{{menu.text}}</h3>
                  </div>
                </div>
              </b-dropdown-item>
            </b-dropdown>
          </template>
          {{ props.row.name  }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.createdBy')" field="device_records.created_by" searchable
                        sortable>
          <template #searchable="props">
            <b-autocomplete v-model="filterCreatedBy" clearable />
          </template>
          {{ props.row['device_records.created_by'] }}
        </b-table-column>
        <b-table-column :label="$gettext('measurementsPage.table.fields.createdAt')" field="device_records.created_at"  sortable searchable>
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
          {{ getLocalTime(props.row['device_records.created_at']) }}
        </b-table-column>
        <b-table-column>
          <div class="buttons is-right">
            <button class="button is-small is-info icon-btn" type="button" @click.prevent="rowReloadHandler(props.row)">
              <font-awesome-icon icon="sync" />
            </button>
            <b-tooltip :label="JSON.stringify(props.row.note, null, 2)" position="is-left">
              <button class="button is-small is-info icon-btn" type="button" @click.prevent="rowClickHandler(props.row)">
                <font-awesome-icon icon="info-circle" />
              </button>
            </b-tooltip>
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

  import { mapState } from 'vuex'
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
  import moment from "moment"
  import BTableColumn from "buefy/src/components/table/TableColumn"
  import BAutocomplete from "buefy/src/components/autocomplete/Autocomplete"
  import ModalBox from '@/components/ModalBox'

  export default {
    name: 'ProductTemplate',
    components: {BAutocomplete, BTableColumn, DateRangePicker, ModalBox},
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
      reload: function () {
        this.getData()
      },
      filterArticleNr: function() {
        this.setFilterValues()
        this.getData()
      },
      filterSerialNr: function() {
        this.setFilterValues()
        this.getData()
      },
      filterState: function() {
        this.setFilterValues()
        this.getData()
      },
      filterSection: function() {
        this.setFilterValues()
        this.getData()
      },
      filterCreatedBy: function() {
        this.setFilterValues()
        this.getData()
      }
    },
    data () {
      const today = new Date()
      // for date picker range
      const rangeDataToday = new Date()
      let rangeDataYesterday = new Date()
      rangeDataYesterday.setDate(rangeDataToday.getDate() - 1)

      let dateLastWeek = new Date()
      let rangeDataLastWeekEnd = new Date()
      let rangeDataLastWeekStart = new Date()
      rangeDataLastWeekEnd.setTime(dateLastWeek.setTime(dateLastWeek.getTime()-(dateLastWeek.getDay()?dateLastWeek.getDay():7)*24*60*60*1000))
      rangeDataLastWeekStart.setTime(dateLastWeek.setTime(rangeDataLastWeekEnd.getTime()-6*24*60*60*1000))

      let dateLast2Week = new Date()
      let rangeDataLast2WeekEnd = new Date()
      let rangeDataLast2WeekStart = new Date()
      rangeDataLast2WeekEnd.setTime(dateLast2Week.setTime(dateLast2Week.getTime()-(dateLast2Week.getDay()?dateLast2Week.getDay():7)*24*60*60*1000))
      rangeDataLast2WeekStart.setTime(dateLast2Week.setTime(rangeDataLastWeekEnd.getTime()-13*24*60*60*1000))
      return {
        // filter
        filterArticleNr: '',
        filterSerialNr: '',
        filterState: { text: 'all', value: 0 },
        isScrollable: false,
        maxHeight: 200,
        stateOptions: [],
        // filterSection: '',
        filterSection: { text: 'all', value: 0 },
        sectionOptions: [],
        filterCreatedBy: '',
        isDateRangeCreatedAt: false,
        dateRangeCreatedAt: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        ranges: {
          'Today': [rangeDataToday, rangeDataToday],
          'Yesterday': [rangeDataYesterday, rangeDataYesterday],
          'This month': [new Date(today.getFullYear(), today.getMonth(), 1), new Date(today.getFullYear(), today.getMonth() + 1, 0)],
          'This year': [new Date(today.getFullYear(), 0, 1), new Date(today.getFullYear(), 11, 31)],
          'Last month': [new Date(today.getFullYear(), today.getMonth() - 1, 1), new Date(today.getFullYear(), today.getMonth(), 0)],
          'Last week': [rangeDataLastWeekStart, rangeDataLastWeekEnd],
          'Last 2 weeks': [rangeDataLast2WeekStart, rangeDataLast2WeekEnd],
        },
        // initial table
        checkedRows: [],
        selectedRow: {},
        isLoading: false,
        total: 0,
        perPage: 10,
        page: 1,
        sortField:'device_records.created_at',
        sortOrder:'desc',
        defaultSortOrder:'desc',
        filters: {},
        filterValues: '',
        measurementsData: [],
        // control table
        selectedId: null,
        // selected measurement data
        jsonPdFlow: [],
        // productsearch page url
        productSearchPageUrl: '',
        // refresh
        isModalActive: false,
        selectedRefreshRow: ''
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
      this.getFormSupport()
      this.getFormSupportSectionTemplate()
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
        if(field == 'name') {
          this.sortField = 'device_records.production_section_template_id'
        }
        this.getData()
      },
      ///////////////////////////////////////////////////////////////
      rowReloadHandler(row) {
        this.selectedRefreshRow = row
        this.isModalActive = true
      },
      reloadConfirm() {
        this.isModalActive = false
        const data = {
          id: this.selectedRefreshRow.products_id,
          sectionId: this.selectedRefreshRow.name
        }
        axios.put('device_records/reloadMeasurements', data)
            .then( r => {
              if(r.data.status == false) {
                let message = `Device record not found.`
                this.$buefy.snackbar.open({
                  message: message,
                  type: 'is-danger',
                  queue: false
                })
              } else {
                //update entry
                let message = `Updated successfully!`
                this.$buefy.snackbar.open({
                  message: message,
                  type: 'is-success',
                  queue: false
                })
              }
            }).catch( err => {
          let message
          if( err.response.status == 404){
            message = `Products FormSupport Error`
          }
          this.$buefy.toast.open({
            message: message,
            type: 'is-danger',
            queue: false
          })
        }).finally(() => {

        })
      },
      reloadCancel() {
        this.isModalActive = false
      },
      rowClickHandler(row) {
        const gotoUrl = this.productSearchPageUrl + '?products_id=' + row.products_id
        window.open(gotoUrl, '_blank');
      },
      setFilterValues() {
        let filter = {}
        if(this.filterArticleNr) {
          filter['st_article_nr'] = this.filterArticleNr
        }
        if(this.filterSerialNr) {
          filter['st_serial_nr'] = this.filterSerialNr
        }
        if(this.filterState.text != 'all') {
          filter['device_records.state'] = this.filterState.value
        }
        if(this.filterSection.text != 'all') {
          filter['device_records.production_section_template_id'] = this.filterSection.value
        }
        if(this.filterCreatedBy) {
          filter['device_records.created_by'] = this.filterCreatedBy
        }
        if(this.isDateRangeCreatedAt) {
          filter['device_records.created_at-gt'] = moment.utc(this.dateRangeCreatedAt.startDate).format()
          filter['device_records.created_at-lt'] = moment.utc(this.dateRangeCreatedAt.endDate).format()
        }
        this.filterValues = ''
        this.filterValues = encodeURIComponent(JSON.stringify(filter))
      },
      getLocalTime(date) {
        return moment.utc(date).utcOffset(this.utcOffset).format("DD.MM.YYYY / k:mm:ss")
      },
      getFormSupport() {
        axios.get('/device_records/form_support')
            .then( r => {
              const options = r.data.state
              this.stateOptions = []
              const item = {text: 'all', value: 0}
              this.stateOptions.push(item)
              for (const property in options) {
                const item = {text: `${options[property]}`, value: `${property}`}
                this.stateOptions.push(item)
              }
            }).catch( err => {
          let message
          if( err.response.status == 404){
            message = `Products FormSupport Error`
          }
          this.$buefy.toast.open({
            message: message,
            type: 'is-danger',
            queue: false
          })
        }).finally(() => {

        })
      },
      getFormSupportSectionTemplate() {
        axios.get('/production_section/form_support')
            .then( r => {
              const options = r.data.section
              this.sectionOptions = []
              const item = {text: 'all', value: 0}
              this.sectionOptions.push(item)
              for (const property in options) {
                const item = {text: `${options[property]['name']}`, value: `${options[property]['id']}`}
                this.sectionOptions.push(item)
              }
            }).catch( err => {
          let message
          if( err.response.status == 404){
            message = `Production section template FormSupport Error`
          }
          this.$buefy.toast.open({
            message: message,
            type: 'is-danger',
            queue: false
          })
        }).finally(() => {

        })
      },
      getData() {
        this.isLoading = true
        const params = [
          `size=${this.perPage}`,
          `sort_by=${this.sortField}-${this.sortOrder}`,
          `page=${this.page}`,
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
      clearCreatedAt() {
        const today = new Date()
        this.dateRangeCreatedAt.startDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate())
        this.dateRangeCreatedAt.endDate = today
        this.isDateRangeCreatedAt = false
        this.setFilterValues()
        this.getData()
      },
      updateDateRangeCreatedAt() {
        this.isDateRangeCreatedAt = true
        this.setFilterValues()
        this.getData()
      },
    }
  }
</script>
