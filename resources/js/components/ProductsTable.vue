<template>
  <div>
    <modal-trash-box :is-active="isModalActive" :trash-subject="trashObjectName" @confirm="trashConfirm" @cancel="trashCancel"/>
    <div class="level">
      <div class="level-left">
        <b-button type="is-info" disabled>{{$gettext('productsPage.productsTable.productsCounts')}}: {{this.total}}</b-button>
        <a :href="this.filterGeneralUrl"><b-button class="btn excel-export">{{$gettext('productsPage.productsTable.downloadExcel')}}</b-button></a>
        <a :href="this.filterFullExcelUrl"><b-button class="btn excel-export">{{$gettext('productsPage.productsTable.fullExcel')}}</b-button></a>
      </div>
      <div class="level-right">
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
            @filters-change="onFilterChange"

            :data="products">

      <template slot-scope="props">

        <b-table-column class="has-no-head-mobile is-image-cell">
          <div v-if="props.row.avatar" class="image">
            <img :src="props.row.avatar" class="is-rounded">
          </div>
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.articleNr')" field="st_article_nr" sortable searchable>
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.serialNr')" field="st_serial_nr" sortable searchable>
          {{ props.row.st_serial_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.status')" field="lifecycle" sortable>
          {{ props.row.lifecycle }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.productionDataCount')" field="production_data_count">
          {{ props.row.production_data_count }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.componentsCount')" field="components_count">
          {{ props.row.components_count }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.productionOrderNr')" field="production_order_nr" sortable searchable>
          {{ props.row.production_order_nr }}
        </b-table-column>
        <b-table-column :label="$gettext('productsPage.productsTable.fields.createdAt')" field="created_at" sortable>
          <small class="has-text-grey is-abbr-like" :title="props.row.created_at">{{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss")}}</small>
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
  import downloadexcel from "vue-json-excel";
  import BButton from "buefy/src/components/button/Button";
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
  import BField from "buefy/src/components/field/Field";

  export default {
    name: 'ProductsTable',
    components: {BField, BButton, ModalTrashBox, downloadexcel, DateRangePicker },
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
        dateRange: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        dateRangeValues: '{}',
        timePicker: true,
        timePicker24Hour: true,
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
        filterValues: '{}',
        selectedRow: {},
        filterGeneralUrl: '',
        filterEnhancedUrl: '',
        filterFullExcelUrl: '',
        excelProducts: [],
        jsonFields: {
          'Artikel-Nr.': 'st_article_nr',
          'Serial-Nr.': 'st_serial_nr',
          'Status': 'lifecycle',
          'Produktionsdaten': 'production_data_count',
          'Komponenten': 'components_count',
          'Produktionsauftrag': 'production_order_nr',
          'Erstellt': 'created_at'
        },
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
      this.getData()
      this.getFilteringURL ()
    },
    methods: {
      updateDateRange() {
        this.dateRangeValues = '';
        this.dateRangeValues = encodeURIComponent(JSON.stringify(this.dateRange));
        this.getFilteringURL ()
        this.getData()
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
      onFilterChange: debounce(function (filter) {
        console.log(filter);
        console.warn('filter', Object.entries(filter));
        this.filterValues = '';
        this.filterValues = encodeURIComponent(JSON.stringify(filter));
        this.getData()
        this.getFilteringURL()
      }, 250),
      getData () {
        if (this.dataUrl) {
          this.isLoading = true
          const params = [
            `size=${this.perPage}`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `date_range=${this.dateRangeValues}`,
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
      getFilteringURL () {
        if(this.dataUrl){
          const paramsGeneral = [
            `enhanced=0`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `date_range=${this.dateRangeValues}`,
            `filter=${this.filterValues}`
          ].join('&')

          const paramsEnhance = [
            `enhanced=1`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `date_range=${this.dateRangeValues}`,
            `filter=${this.filterValues}`
          ].join('&')

          const paramsFullExcel = [
            `enhanced=2`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
            `page=${this.page}`,
            `date_range=${this.dateRangeValues}`,
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
      },
      getParamDate(strDate) {
        let d = new Date(strDate);
        let yyyy = d.getFullYear();
        let mm = d.getMonth() + 1;
        let dd = d.getDate()
        let hr = d.getHours();
        let min = d.getMinutes();
        let second = d.getSeconds();
        mm = mm < 10 ? ('0' + mm) : mm;
        dd = dd < 10 ? ('0' + dd) : dd;
        hr = hr < 10 ? ('0' + hr) : hr;
        min = min < 10 ? ('0' + min) : min;
        second = second < 10 ? ('0' + second) : second;
        // return yyyy + '-' + mm + '-' + dd
        return dd + '.' + mm + '.' + yyyy
      }
    }
  }
</script>