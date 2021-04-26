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

        <b-table-column label="st_article_nr" field="st_article_nr">
          {{ props.row.st_article_nr }}
        </b-table-column>
        <b-table-column label="production_flow" field="production_flow">
          {{ props.row.production_flow[0] }}
        </b-table-column>
        <b-table-column label="updated_by" field="updated_by">
          {{ props.row.updated_by }}
        </b-table-column>
        <b-table-column label="created_at" field="created_at" sortable>
          {{ props.row.created_at.split('T')[0] }}
        </b-table-column>
        <b-table-column label="updated_at" field="updated_at" sortable>
          {{ props.row.updated_at.split('T')[0] }}
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
  import ModalTrashBox from '@/components/ModalTrashBox'
  import debounce from 'lodash/debounce'
  import BButton from "buefy/src/components/button/Button";

  export default {
    name: 'ProductTemplateHistory',
    components: {BButton, ModalTrashBox },
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
        sortField:'',
        sortOrder:'asc',
        defaultSortOrder:'asc',
        page: 1,
        total: 0,
        filterValues: '{}',
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
      getData () {
        if (this.dataUrl) {
          this.isLoading = true
          const params = [
            `size=${this.perPage}`,
            `sort_by=${this.sortField}.${this.sortOrder}`,
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
  }
</script>
