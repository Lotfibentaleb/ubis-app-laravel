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
            :data="pdSecTemplateData">

      <template slot-scope="props">
        <b-table-column :label="$t('productionSectionPage.table.fields.name')" field="name">
          {{ props.row.name }}
        </b-table-column>
        <b-table-column :label="$t('productionSectionPage.table.fields.group')" field="group" searchable>
          {{ props.row.group }}
        </b-table-column>
        <b-table-column :label="$t('productionSectionPage.table.fields.data')">
          <b-tooltip :label="JSON.stringify(props.row.data, null, 2)"  position="is-right" :delay="1000" multilined size="is-large">
            {{ JSON.stringify(props.row.data[0]).substring(0, 25) + ' ...' }}
          </b-tooltip>
        </b-table-column>
        <b-table-column :label="$t('productionSectionPage.table.fields.createdAt')" field="created_at" sortable>
          {{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss")}}
        </b-table-column>
        <b-table-column :label="$t('productionSectionPage.table.fields.updatedAt')" field="updated_at" sortable>
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
            <option value="10">10 {{$t('productionSectionPage.table.perPage')}}</option>
            <option value="20">20 {{$t('productionSectionPage.table.perPage')}}</option>
            <option value="50">50 {{$t('productionSectionPage.table.perPage')}}</option>
            <option value="100">100 {{$t('productionSectionPage.table.perPage')}}</option>
            <option value="1000">1000 {{$t('productionSectionPage.table.perPage')}}</option>
          </b-select>
        </div>
      </template>

    </b-table>
  </div>
</template>
<script>

  import debounce from 'lodash/debounce'

  export default {
    name: 'ProductionSectionTemplate',
    props: {
      reset: {
        type: Boolean,
        default: false
      },
    },
    watch: {
      selectedRow: function () {
        this.rowClickHandler()
      },
      reset: function () {
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
        pdSecTemplateData: [],
        // control table
        isClickedRow: false,
        selectedId: null,
        // selected measurement data
        jsonMeasurementData: []
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
        this.filterValues = filter.group ? filter.group : ''
        this.getData()
      }, 250),
      ///////////////////////////////////////////////////////////////

      rowClickHandler () {
        this.selectedId = this.selectedRow.id
        this.jsonMeasurementData = this.selectedRow.data
        this.isClickedRow = true
        let data = {
          id: this.selectedId,
          name: this.selectedRow.name,
          data: this.jsonMeasurementData,
          render_hint: this.selectedRow.render_hint,
          group: this.selectedRow.group,
          description: this.selectedRow.description
        }
        this.$emit('clickedRow', data)
      },

      getData () {
        const params = [
          `size=100`,
          `page=1`,
          `group=${this.filterValues}`
        ].join('&')
        const fetchUrl = '/production_section'
        axios.create({
          headers: {
            'Content-Type': 'application/json',
          }
        })
            .get(fetchUrl+'?'+params)
            .then(r => {
              this.isLoading = false
              if (r.data && r.data.data) {
                this.pdSecTemplateData = []
                this.pdSecTemplateData = r.data.data
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