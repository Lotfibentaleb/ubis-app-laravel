<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{$t('measurementsPage.heroBar.title')}}
      <p class="subtitle">
        {{$t('measurementsPage.heroBar.subTitle')}}
      </p>
      <router-link slot="right" to="/products/new-template" class="button">
        {{$t('measurementsPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <div class="columns">
        <div class="column">
          <card-component class="has-table has-mobile-sort-spaced" :title="$t('measurementsPage.table.title')" icon="package-variant-closed">
            <measurements-table @clickedRow="clickedRow" :reload="isReload"/>
          </card-component>
        </div>
        <div v-if="isClickedRow">
          <div  class="column">
            <card-component :title="$t('measurementsPage.settingPanel.title')" class="has-mobile-sort-spaced" icon="lead-pencil">
              <div class="level">
                <div class="level-left">
                  <div>
                  </div>
                </div>
                <div class="level-right">
                  <!--<div><b-button class="btn btn-ok" :disabled="!hasUpdatingData" @click="savePdFlowData">Save</b-button></div>-->
                </div>
              </div>
              <b-field :label="$t('measurementsPage.table.fields.articleNr')" :message="$t('productionFlowsPage.settingPanel.fieldMessage')">
                <b-input type="text" placeholder="Artikel-Nr." :value="selectedArticleNr" readonly/>
              </b-field>
              <b-field :label="$t('measurementsPage.table.fields.measurementDetail')" :message="$t('measurementsPage.table.fields.measurementDetail')" >
                <v-jsoneditor ref="jeditor" v-model="jsonMeasurementDetail" :options="options" style="max-width: 500px;"/>
              </b-field>
            </card-component>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import Ajv from 'ajv'
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import CardToolbar from '@/components/CardToolbar'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import MeasurementsTable from '@/components/Measurements/MeasurementsTable'
  import ModalUpdateCheck from '@/components/ProductsTemplate/ModalUpdateCheck'
  import ModalBox from '@/components/ModalBox'
  import BField from "buefy/src/components/field/Field"
  import VJsoneditor from 'v-jsoneditor'
  import BInput from "buefy/src/components/input/Input"
  import BButton from "buefy/src/components/button/Button"
  import ProductionFlowSchema from '@/schema/ProductionFlowSchema'

  const ajv = new Ajv()

  export default {
    name: 'measurements.list',
    components: {
      BButton,
      BInput,
      BField, HeroBar, TitleBar, CardComponent, CardToolbar, VJsoneditor, ModalBox, Notification, MeasurementsTable, ModalUpdateCheck},
    computed: {
      titleStack() {
        return [
          this.$t('measurementsPage.titleBar.main'),
          this.$t('measurementsPage.titleBar.sub1')
        ]
      },
    },
    data () {
      return {
        isClickedRow: false,
        prevjsonMeasurementDetail: [],
        jsonMeasurementDetail: [],
        selectedId: null,
        selectedArticleNr: null,
        hasUpdatingData: false,
        invalidSecId: false,

        measurementsDetail: [],

        //to reload products template table data
        isReload: false,

        //json schema
        options: {
          mode: 'tree',
          modes: ['code', 'tree'], // allowed modes
          enableTransform: false,
          enableSort: false,
        }
      }
    },
    methods: {
      clickedRow(data) {
        this.isClickedRow = true
        this.getData(data.selectedId)
      },
      getData (id) {
        const fetchUrl = '/device_records/' + id
        axios.create({
          headers: {
            'Content-Type': 'application/json',
          }
        })
            .get(fetchUrl)
            .then(r => {
              this.isLoading = false
              if (r.data && r.data.data) {
                this.measurementsDetail = r.data.data
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
      }
    }
  }
</script>

