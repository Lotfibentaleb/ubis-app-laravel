<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/products/template" class="button">
        Product Templates
      </router-link>
    </hero-bar>
    <create-production-flow-modal :is-json-modal="isJsonModal" :section-data="sectionData" @addedJsonData="addedJsonData" @cancelJsonAdd="cancelJsonAdd"></create-production-flow-modal>
    <section class="section is-main-section">
      <tiles>
        <card-component :title="formCardTitle" icon="package-variant-closed" class="tile is-child">
          <form @submit.prevent="submit">
            <b-field label="Artikel-Nr." message="Artikel-Nr.">
              <b-input placeholder="e.g. 80000101A1" v-model="article_nr" required expanded/>
            </b-field>
            <b-field expanded>
              <b-button v-if="!isJsonEmpty" @click="clickedAddJsonBtn">Add Production Flow</b-button>
              <b-button v-else type="is-danger" @click="clickedAddJsonBtn">Add Production Flow</b-button>
            </b-field>
            <div class="level">
              <div class="level-left">
              </div>
              <div class="level-right">
                <b-field >
                  <b-button class="btn btn-ok" :loading="isLoading" native-type="submit">Submit</b-button>
                </b-field>
              </div>
            </div>
          </form>
        </card-component>
        <card-component v-if="hasJsonItem" title="Produktions-Ablauf" icon="package-variant-closed" class="tile is-child">
          <b-field label="Produktions-Ablauf" message="Produktions-Ablauf" >
            <v-jsoneditor v-model="jsonData" />
          </b-field>
          <hr>
        </card-component>
      </tiles>
    </section>
  </div>
</template>

<script>
  import clone from 'lodash/clone'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import Tiles from '@/components/Tiles'
  import CardComponent from '@/components/CardComponent'
  import FilePicker from '@/components/FilePicker'
  import UserAvatar from '@/components/UserAvatar'
  import Notification from '@/components/Notification'
  import BField from "buefy/src/components/field/Field"
  import VJsoneditor from 'v-jsoneditor'
  import CreateProductionFlowModal from '@/components/ProductsTemplate/CreateProductionFlowModal'

  export default {
    name: 'ProductionSectionTemplateForm',
    components: {BField, UserAvatar, FilePicker, CardComponent, VJsoneditor, CreateProductionFlowModal, Tiles, HeroBar, TitleBar, Notification },
    props: {
      id: {
        default: null
      }
    },
    data () {
      return {
        isLoading: false,
        isJsonModal: false,
        hasJsonItem: false,
        article_nr: '',
        jsonData: [],
        isJsonEmpty: false,

        //production section template information
        pdSecTemData: [],
        availableSectionIds: [],
        availableSectionNames: [],
        availableSectionGroups: [],
        selectedSectionIndex: 0,
        sectionData: {}
      }
    },
    computed: {
      titleStack () {
        return [
          'Products',
          'Template',
          'New Template'
        ]
      },
      heroTitle () {
        return 'Create Product Template'
      },
      formCardTitle () {
        return 'New Product Template'
      }
    },
    created() {
      this.getSectionTemplateData()
    },
    methods: {
      clickedAddJsonBtn() {
        this.isJsonModal = true
      },
      addedJsonData(data) {
        this.hasJsonItem = true
        this.isJsonModal = false
        this.jsonData.push(data)
        this.isJsonEmpty = false
      },
      cancelJsonAdd() {
        this.isJsonModal = false
      },
      getSectionTemplateData() {
        const params = [
          `size=100`,
          `page=1`
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
                this.pdSecTemData = r.data.data
                this.pdSecTemData.forEach(pdSectionTempItem => {
                  this.availableSectionIds.push(pdSectionTempItem.id)
                  this.availableSectionNames.push(pdSectionTempItem.name)
                  this.availableSectionGroups.push(pdSectionTempItem['group'])
                })
                this.sectionData = {
                  availableIds: this.availableSectionIds,
                  availableNames: this.availableSectionNames,
                  availableGroups: this.availableSectionGroups
                }
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
      submit () {
        if(this.jsonData.length == 0) {
          this.isJsonEmpty = true
          this.$buefy.snackbar.open({
            type: 'is-danger',
            message: 'Empty production flow field',
            queue: false
          })
          return
        }
        this.isLoading = true
        let method = 'post'
        let url = '/production_flow'
        let data = {
          article_nr: this.article_nr,
          production_flow: JSON.stringify(this.jsonData)
        }
        axios({
          method,
          url,
          data: data
        }).then(r => {
          this.isLoading = false
          if (r.data ) {
            this.$router.push({name: 'products.section_template'})
            this.$buefy.snackbar.open({
              message: 'Created New Section Template',
              queue: false
            })
          }
        }).catch(e => {
          this.isLoading = false
          this.$buefy.toast.open({
            message: `Error: ${e.message}`,
            type: 'is-danger',
            queue: false
          })
        })
      }
    }
  }
</script>
