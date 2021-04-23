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
      <card-component class="has-table has-mobile-sort-spaced" title="Production template" icon="account-multiple">
        <b-table
          :checked-rows.sync="checkedRows"
          :checkable="true"
          :loading="isLoading"
          :paginated="paginated"
          :per-page="perPage"
          :striped="true"
          :hoverable="true"
          default-sort="name"
          :data="productionTemplatesData"
          @dblclick="dbRowClickHandler">
          <template slot-scope="props">
            <b-table-column label="id" field="id" sortable>
              {{ props.row.id }}
            </b-table-column>
            <b-table-column label="st_article_nr" field="st_article_nr" sortable>
              {{ props.row.st_article_nr }}
            </b-table-column>
            <b-table-column label="production_flow" field="production_flow" sortable>
              {{ props.row.production_flow[0] }}
            </b-table-column>
            <b-table-column custom-key="actions" class="is-actions-cell">
              <div class="buttons is-right">
                <router-link :to="{name:'users.edit', params: {id: props.row.id}}" class="button is-small is-primary">
                  <b-icon icon="account-edit" size="is-small"/>
                </router-link>
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
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import BField from "buefy/src/components/field/Field";
  import vueJsonEditor from 'vue-json-editor'

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, Notification, vueJsonEditor},
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
        selectedNum: null,
        isModalActive: false,
        trashObject: null,
        productionTemplatesData: [],
        isLoading: false,
        paginated: false,
        perPage: 10,
        checkedRows: []
      }
    },
    created () {
      this.getData()
    },
    methods: {
      onJsonChange (value) {
        this.productionTemplatesData[this.selectedNum - 1].production_flow = []
        this.productionTemplatesData[this.selectedNum - 1].production_flow = value
      },
      dbRowClickHandler (rowData) {
        this.selectedNum = rowData.id
        this.jsonProductFlow = []
        this.jsonProductFlow = rowData.production_flow
      },
      getData () {
        this.isLoading = true
        axios
            .get('/product_templates')
            .then(r => {
              this.isLoading = false
              if (r.data) {
                if (r.data.length > this.perPage) {
                  this.paginated = true
                }
                this.productionTemplatesData = r.data
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
    }
  }
</script>

