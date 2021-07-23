<template>
  <div class="products-list-table">
    <modal-trash-box :is-active="isModalActive" :trash-subject="trashObjectName" @confirm="trashConfirm" @cancel="trashCancel"/>
    <div class="level">
      <div class="level-left">
        <b-button type="is-info" disabled>{{$gettext('productsPage.productsTable.productsCounts')}}: {{this.total}}</b-button>
        <a :href="this.filterGeneralUrl"><b-button class="btn excel-export">{{$gettext('productsPage.productsTable.downloadExcel')}}</b-button></a>
        <a :href="this.filterFullExcelUrl"><b-button class="btn excel-export">{{$gettext('productsPage.productsTable.fullExcel')}}</b-button></a>
      </div>
    </div>
    <b-table
            header-class="products-list"
            :checked-rows.sync="checkedRows"
            :checkable="checkable"
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

            :data="products">

      <template slot-scope="props">
        <b-table-column :label="$gettext('productsPage.productsTable.fields.articleNr')" field="st_article_nr" sortable
                        searchable>
          <template #searchable="props">
            <b-autocomplete v-model="filterArticleNr" clearable />
          </template>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.serialNr')" field="st_serial_nr" sortable
                        searchable>
          <template #searchable="props">
            <b-autocomplete v-model="filterSerialNr" clearable />
          </template>
          {{ props.row.st_serial_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.status')" field="lifecycle"
                        searchable>
          <template #searchable="props">
            <b-dropdown
                    :scrollable="isScrollable"
                    :max-height="maxHeight"
                    v-model="filterStatus"
                    aria-role="list"
            >
              <template #trigger>
                <b-button
                        :label="filterStatus.text"
                        icon-right="menu-down" />
              </template>

              <b-dropdown-item
                      v-for="(menu, index) in menus"
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
          {{ props.row.lifecycle }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.productionDataCount')" field="production_data_count">
          {{ props.row.production_data_count }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.componentsCount')" field="components_count">
          {{ props.row.components_count }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.productionOrderNr')" field="production_order_nr" sortable searchable>
          <template #searchable="props">
            <b-autocomplete v-model="filterProdOrderNr" clearable />
          </template>
          {{ props.row.production_order_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.createdAt')" field="created_at" sortable searchable>
          <template #searchable="props">
            <date-range-picker
                    ref="picker"
                    v-model="dateRangeCreatedAt"
                    @update="updateDateRangeCreatedAt"
            >
              <template v-if="isDateRangeCreatedAt" v-slot:input="picker" style="min-width: 350px;">
                {{ picker.startDate | moment("DD.MM.YYYY") }} - {{ picker.endDate | moment("DD.MM.YYYY") }}
              </template>
              <template v-else v-slot:input="picker" style="min-width: 350px;">
                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
              </template>
            </date-range-picker>
            <b-button v-if="isDateRangeCreatedAt" @click="clearCreatedAt">X</b-button>
          </template>
          <small class="has-text-grey is-abbr-like" :title="props.row.created_at">{{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss")}}</small>
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.updatedAt')" field="updated_at" sortable searchable>
          <template #searchable="props">
            <date-range-picker
                    ref="picker"
                    v-model="dateRangeUpdatedAt"
                    @update="updateDateRangeUpdatedAt"
            >
              <template v-if="isDateRangeUpdatedAt" v-slot:input="picker" style="min-width: 350px;">
                {{ picker.startDate | moment("DD.MM.YYYY") }} - {{ picker.endDate | moment("DD.MM.YYYY") }}
              </template>
              <template v-else v-slot:input="picker" style="min-width: 350px;">
                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
              </template>
            </date-range-picker>
            <b-button v-if="isDateRangeUpdatedAt" @click="clearUpdatedAt">X</b-button>
          </template>
          <small class="has-text-grey is-abbr-like" :title="props.row.updated_at">{{ props.row.updated_at | moment("DD.MM.YYYY / k:mm:ss")}}</small>
        </b-table-column>
        <b-table-column custom-key="actions" class="is-actions-cell">
          <div class="buttons is-right">
            <button class="button is-small is-info" type="button" @click.prevent="infoClickHandler(props.row)">
              <font-awesome-icon icon="info-circle" />
            </button>
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
            <option value="10">10 {{$gettext('productsPage.productsTable.perPage')}}</option>
            <option value="20">20 {{$gettext('productsPage.productsTable.perPage')}}</option>
            <option value="50">50 {{$gettext('productsPage.productsTable.perPage')}}</option>
            <option value="100">100 {{$gettext('productsPage.productsTable.perPage')}}</option>
            <option value="1000">1000 {{$gettext('productsPage.productsTable.perPage')}}</option>
          </b-select>
        </div>
      </template>
    </b-table>
  </div>
</template>

<script>
  import ModalTrashBox from '@/components/ModalTrashBox'
  import debounce from 'lodash/debounce'
  import BButton from "buefy/src/components/button/Button"
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
  import BField from "buefy/src/components/field/Field"
  import moment from "moment"
  import BAutocomplete from "buefy/src/components/autocomplete/Autocomplete";

  export default {
    name: 'ProductsTable',
    components: {BAutocomplete, BField, BButton, ModalTrashBox, DateRangePicker },
    props: {
      dataUrl: {
        type: String,
        default: null
      },
      checkable: {
        type: Boolean,
        default: false
      }
    },
    data () {
      const today = new Date()
      return {
        // filter
        filterArticleNr: '',
        filterSerialNr: '',
        filterProdOrderNr: '',
        filterStatus: { text: 'all', value: 0 },
        isScrollable: false,
        maxHeight: 200,
        menus: [
          { text: 'all', value: 0 },
          { text: 'unknown', value: 0 },
          { text: 'in_production', value: 1 },
          { text: 'all_tests_passed', value: 2 },
          { text: 'test_failed', value: 3 },
        ],
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

        isModalActive: false,
        trashObject: null,
        products: [],
        isLoading: false,
        isExcelLoading: false,
        perPage: 10,
        checkedRows: [],
        sortField:'created_at',
        sortOrder:'desc',
        defaultSortOrder:'desc',
        page: 1,
        total: 0,
        filters: {},
        filterValues: '{}',
        selectedRow: {},
        filterGeneralUrl: '',
        filterEnhancedUrl: '',
        filterFullExcelUrl: '',
        excelProducts: [],
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
    watch:{
      perPage:function(){
        this.getData();
      },
      selectedRow: function() {
        this.$emit('clickedRow', this.selectedRow)
      },
      filterArticleNr: function() {
        this.setFilterValues()
        this.getData()
      },
      filterSerialNr: function() {
        this.setFilterValues()
        this.getData()
      },
      filterProdOrderNr: function() {
        this.setFilterValues()
        this.getData()
      },
      filterStatus: function() {
        this.setFilterValues()
        this.getData()
      }
    },
    computed: {
      trashObjectName () {
        if (this.trashObject) {
          return this.trashObject.st_article_nr+"/"+this.trashObject.st_serial_nr
        }
        return null
      }
    },
    created () {
      this.setFilterValues()
      this.getData()
      this.getFilteringURL()
      this.productSearchPageUrl = process.env.MIX_PRODUCTS_SEARCH_PAGE_URL
    },
    methods: {
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
        this.getFilteringURL()
      },
      updateDateRangeUpdatedAt() {
        this.isDateRangeUpdatedAt = true
        this.setFilterValues()
        this.getData()
        this.getFilteringURL()
      },
      onPageChange(page) {
        this.page = page
        this.getData ()
      },
      onSort(field, order) {
        this.sortField = field
        this.sortOrder = order
        this.getData()
      },
      infoClickHandler(row) {
        const gotoUrl = this.productSearchPageUrl + '?products_id=' + row.id
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
        if(this.filterStatus.text != 'all') {
          filter['lifecycle'] = this.filterStatus.value
        }
        if(this.filterProdOrderNr) {
          filter['production_order_nr'] = this.filterProdOrderNr
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
      getData () {
        if (this.dataUrl) {
          this.isLoading = true
          const params = [
            `size=${this.perPage}`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `filter=${this.filterValues}`
          ].join('&')

          axios.create({
            headers: {
              'Content-Type': 'application/json',
            }
          })
              .get(this.dataUrl+'?'+params)
              .then(r => {
                this.isLoading = false
                if (r.data && r.data.data) {
                  this.perPage = r.data.meta.per_page
                  this.total = r.data.meta.total
                  this.page = r.data.meta.current_page
                  this.products = r.data.data
                }
              })
              .catch( err => {
                if (err.response) {
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
        }
      },
      getFilteringURL() {
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

          const paramsFullExcel = [
            `enhanced=2`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `filter=${this.filterValues}`
          ].join('&')

          this.filterGeneralUrl = this.dataUrl + '/excel?' + paramsGeneral
          this.filterEnhancedUrl = this.dataUrl + '/enhancedExcel?' + paramsEnhance
          this.filterFullExcelUrl = this.dataUrl + '/fullExcel?' + paramsFullExcel
        }
      },
      trashModal (trashObject) {
        this.trashObject = trashObject
        this.isModalActive = true
      },
      trashConfirm () {
        this.isModalActive = false
        axios
            .delete(`${this.dataUrl}/product/${this.trashObject.id}`)
            .then( r => {
              this.getData()  // update list
              this.$buefy.snackbar.open({
                message: `Deleted ${r.data.data.st_article_nr}/${r.data.data.st_serial_nr} and ${r.data.data.deleted_device_records} Measurements, ${r.data.data.deleted_sub_components} Components.`,
                queue: false
              })
            })
            .catch( err => {
              this.$buefy.toast.open({
                message: `Error: ${err.message}`,
                type: 'is-danger',
                queue: false
              })
            })
      },
      trashCancel () {
        this.isModalActive = false
      },
      startDownload () {
        console.log("Started Download!")
      },
      finishDownload () {
        console.log("Finished Download!")
      }
    }
  }
</script>