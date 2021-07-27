<template>
  <div>

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
            backend-sorting
            :default-sort-direction="defaultSortOrder"
            :default-sort="[sortField, sortOrder]"
            @sort="onSort"
            backend-filtering
            @filters-change="onFilterChange"
            :data="productsHistory">
      <template slot-scope="props">
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.productionFlow')" field="production_flow" centered>
          <div class="buttons is-right popup-display-btn">
            <div class="position-btn-detail">
              <div v-if="isShowProdDetailModal && selectedIndex == props.row.index" class="prod-detail-popup">
                <div class="prod-detail-text">
                  <b-input :value="JSON.stringify(JSON.parse(JSON.stringify(selectedRowData.production_flow)))" type="textarea" readonly />
                </div>
                <div class="prod-detail-btn">
                  <b-button class="btn btn-prod-modal"  @click="confirmModal">Ok</b-button>
                  <b-button class="btn btn-prod-modal-cancel"  @click="canCelModal">Cancel</b-button>
                </div>
              </div>
              <button class="button is-small is-success btn-detail" type="button" @click.prevent="showProductDetail(props.row)">
                show detail
              </button>
            </div>
          </div>
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.updatedBy')" field="updated_by">
          {{ props.row.updated_by }}
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.validFrom')" field="valid_since">
          {{ props.row.valid_since | moment("DD.MM.YYYY / k:mm:ss")}}
        </b-table-column>
        <b-table-column :label="$gettext('productionFlowsPage.table.fields.validTill')" field="valid_till">
          {{ props.row.valid_till | moment("DD.MM.YYYY / k:mm:ss")}}
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
  import ModalTrashBox from '@/components/ModalTrashBox'
  import debounce from 'lodash/debounce'
  import BButton from "buefy/src/components/button/Button";
  import BInput from "buefy/src/components/input/Input";

  export default {
    name: 'ProductTemplateHistory',
    components: {BInput, BButton, ModalTrashBox },
    props: {
      dataUrl: {
        type: String,
        default: null
      },
      article_nr: {
        type: String,
        default: null
      },
      checkable: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        isModalActive: false,
        trashObject: null,
        productsHistory: [],
        isLoading: false,
        isExcelLoading: false,
        perPage: 10,
        checkedRows: [],
        sortField:'valid_since',
        sortOrder:'desc',
        defaultSortOrder:'desc',
        page: 1,
        total: 0,
        filterValues: '{}',
        isShowProdDetailModal: false,
        selectedRowData: [],
        selectedIndex: null,
      }
    },
    watch:{
      perPage:function(){
        this.getData();
      },
      article_nr: function(){
        this.getData();
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
      }, 250),
      showProductDetail (rowData) {
        this.selectedIndex = rowData.index
        this.selectedRowData = rowData
        this.isShowProdDetailModal = true
      },
      getData () {
        if (this.dataUrl) {
          this.isLoading = true
          const params = [
            `size=${this.perPage}`,
            `sort_by=${this.sortField}-${this.sortOrder}`,
            `page=${this.page}`,
            `article_nr=${this.article_nr}`
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
                  this.productsHistory = r.data.data
                  this.productsHistory.forEach((productHistoryItem, index) => {
                    productHistoryItem['index'] = index
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
      },
      confirmModal () {
        this.isShowProdDetailModal = false
      },
      canCelModal () {
        this.isShowProdDetailModal = false
      }
    }
  }
</script>
