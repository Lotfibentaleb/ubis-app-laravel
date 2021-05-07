<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/products/template" class="button">
        {{$t('createProductionTemplatePage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <create-production-flow-modal :is-json-modal="isJsonModal" :section-data="sectionData" @addedJsonData="addedJsonData" @cancelJsonAdd="cancelJsonAdd"></create-production-flow-modal>
    <section class="section is-main-section">
      <tiles>
        <card-component :title="formCardTitle" icon="package-variant-closed" class="tile is-child">
          <form @submit.prevent="submit">
            <b-field :label="$t('createProductionTemplatePage.card.articleNr')" :message="$t('createProductionTemplatePage.card.articleNrMessage')">
              <b-autocomplete
                      :data="articleList"
                      v-model="article_nr"
                      placeholder="Nach ERP Artikelnummer suchen z.B. 800000114B2"
                      :loading="isFetchingArticleList"
                      icon="magnify"
                      field="articleNumber"
                      :clearable="clearable"
                      @typing="getAsyncArticleList"
                      @select="option => articleSelected = option"
                      size="is-medium"
                      required
              >
                <template slot-scope="props">
                  <div class="media">
                    <div class="media-content">
                      {{ props.option.articleNumber }}<br>
                      <small>
                        <div v-show="props.option.productionArticle == true">- Produktions Artikel -</div>
                        {{ props.option.name }}
                      </small>
                    </div>
                  </div>
                </template>
              </b-autocomplete>
            </b-field>
            <b-field expanded>
              <b-button v-if="!isJsonEmpty" @click="clickedAddJsonBtn">{{$t('createProductionTemplatePage.card.addBasicDataButton')}}</b-button>
              <b-button v-else type="is-danger" @click="clickedAddJsonBtn">{{$t('createProductionTemplatePage.card.addBasicDataButton')}}</b-button>
            </b-field>
            <div class="level">
              <div class="level-left">
              </div>
              <div class="level-right">
                <b-field >
                  <b-button class="btn btn-ok" :loading="isLoading" native-type="submit">{{$t('createProductionTemplatePage.card.submitButton')}}</b-button>
                </b-field>
              </div>
            </div>
          </form>
        </card-component>
        <card-component v-if="hasJsonItem" title="Produktions-Ablauf" icon="package-variant-closed" class="tile is-child">
          <b-field label="Produktions-Ablauf" message="Produktions-Ablauf" >
            <v-jsoneditor v-model="jsonData" :options="options"/>
          </b-field>
          <b-field>
            <b-message v-if="!isValidSchema" title="Schema Error!" type="is-danger" aria-close-label="Close message" has-icon>
              <p>path: {{schemaErrorData.instancePath}}</p>
              <p>message: {{schemaErrorData.message}}</p>
              <p v-if="schemaErrorData.message == errorMessage.enumValues">allowed values: {{JSON.stringify(schemaErrorData.params.allowedValues)}}</p>
              <b-tooltip :label="JSON.stringify(schemaData.schema, null, 2)" position="is-left" size="is-large" multilined>
                <a href="#"><p>See schema</p></a>
              </b-tooltip>
            </b-message>
          </b-field>
          <b-field>
            <b-message v-if="isValidSchema" auto-close title="Success" type="is-success" aria-close-label="Close message" has-icon>
              <p>JSON Schema Validation Success</p>
            </b-message>
          </b-field>
          <hr>
        </card-component>
      </tiles>
    </section>
  </div>
</template>

<script>
  import Ajv from 'ajv'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import Tiles from '@/components/Tiles'
  import CardComponent from '@/components/CardComponent'
  import FilePicker from '@/components/FilePicker'
  import UserAvatar from '@/components/UserAvatar'
  import Notification from '@/components/Notification'
  import BField from "buefy/src/components/field/Field"
  import VJsoneditor from 'v-jsoneditor'
  import debounce from 'lodash/debounce'
  import CreateProductionFlowModal from '@/components/ProductsTemplate/CreateProductionFlowModal'
  import ProductionFlowSchema from '@/schema/ProductionFlowSchema'

  const ajv = new Ajv()

  export default {
    name: 'ProductionSectionTemplateForm',
    components: {BField, UserAvatar, FilePicker, CardComponent, VJsoneditor, CreateProductionFlowModal, Tiles, HeroBar, TitleBar, Notification },
    props: {
      id: {
        default: null
      }
    },
    watch: {
      jsonData: function() {
        const valid = ajv.validate(this.schemaData.schema, this.jsonData)
        if(!valid) {
          this.isValidSchema = false
          this.schemaErrorData = ajv.errors[0]
        } else {
          this.isValidSchema = true
        }
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

        //auto complete
        articleList: [],
        articleSelected: null,
        isFetchingArticleList: false,
        clearable: false,

        //production section template information
        pdSecTemData: [],
        availableSectionIds: [],
        availableSectionNames: [],
        availableSectionGroups: [],
        selectedSectionIndex: 0,
        sectionData: {},

        //json schema
        options: {
          mode: 'tree',
          modes: ['code', 'tree'], // allowed modes
          enableTransform: false,
          enableSort: false,
        },
        isValidSchema: true,
        schemaErrorData: {},
        errorMessage: {
          enumValues: 'must be equal to one of the allowed values'
        },
        schemaData: ProductionFlowSchema,
      }
    },
    computed: {
      titleStack () {
        return [
          this.$t('createProductionTemplatePage.titleBar.main'),
          this.$t('createProductionTemplatePage.titleBar.sub1'),
          this.$t('createProductionTemplatePage.titleBar.sub2')
        ]
      },
      heroTitle () {
        return this.$t('createProductionTemplatePage.heroBar.title')
      },
      formCardTitle () {
        return this.$t('createProductionTemplatePage.card.title')
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
        } else if(!this.isValidSchema) {
          this.$buefy.snackbar.open({
            type: 'is-danger',
            message: 'Json schema Validation Error',
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
            this.$router.push({name: 'products.template'})
            this.$buefy.snackbar.open({
              message: 'Created New Product Template',
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
      },
      getAsyncArticleList: debounce(function (name) {
            if (!name.length) {
              this.articleList = []
              return
            }
            this.isFetchingArticleList = true

            axios
                .get(`/registration/articles?search_artnr=${name}`, {
                  headers: {
                    "Content-Type": "application/json",
                    "Access-Control-Allow-Origin": "*",
                    "Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
                    "Access-Control-Allow-Headers": "Authorization, Content-Type, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, Retry-After, DNT, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Range"
                  }
                })
                .then( result => {
                  console.log('Article list');
                  console.log(result.data)
                  this.isFetchingArticleList = false
                  this.articleList = []
                  result.data.data.forEach((item) => this.articleList.push(item))
                })
                .catch( error => {
                  if (error.request) {
                    // The request was made but no response was received, `error.request` is an instance of XMLHttpRequest in the browser and an instance
                    // of http.ClientRequest in Node.js
                    console.log(error.request);
                  }else  if (error.response) {
                    // The request was made and the server responded with a status code that falls out of the range of 2xx
                    console.log(error.response.data);
                    console.log(error.response.status);
                    console.log(error.response.headers);
                  } else {
                    // Something happened in setting up the request and triggered an Error
                    console.log('Error', error.message);
                  }

                })
                .finally(() => {
                  this.isFetchingArticleList = false
                })
          },
          250)
    }
  }
</script>
