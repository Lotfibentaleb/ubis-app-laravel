<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <!--<router-link slot="right" to="/products/template" class="button">-->
      <!--{{$gettext('ArticleOptions.heroBar.goto')}}-->
      <!--</router-link>-->
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <card-component :title="formCardTitle" icon="package-variant-closed" class="tile is-child">
          <form @submit.prevent="submit">
            <b-field :label="$gettext('createProductionTemplatePage.card.articleNr')" :message="$gettext('createProductionTemplatePage.card.articleNrMessage')">
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
            <!--<b-field expanded>-->
            <!--<b-button v-if="!isJsonEmpty" @click="clickedAddJsonBtn">{{$gettext('createProductionTemplatePage.card.addBasicDataButton')}}</b-button>-->
            <!--<b-button v-else type="is-danger" @click="clickedAddJsonBtn">{{$gettext('createProductionTemplatePage.card.addBasicDataButton')}}</b-button>-->
            <!--</b-field>-->
            <b-field :label="$gettext('ArticleOptions.form.show_article_on_registration')">
              <b-switch ></b-switch>
            </b-field>
            <b-field :label="$gettext('ArticleOptions.form.bar_code_pattern')">
              <b-input placeholder="e.g. John Doe" required />
            </b-field>
            <b-field :label="$gettext('ArticleOptions.form.bar_code_pattern_verification')">
              <b-switch ></b-switch>
            </b-field>
            <b-field :label="$gettext('ArticleOptions.form.serial_in_bar_code_pattern')">
              <b-input placeholder="e.g. John Doe" required />
            </b-field>
            <b-field :label="$gettext('ArticleOptions.form.use_serial_in_bar_code_on_search')">
              <b-switch ></b-switch>
            </b-field>
            <b-field :label="$gettext('ArticleOptions.form.use_serial_in_bar_code_on_store')">
              <b-switch ></b-switch>
            </b-field>
            <div class="level">
              <div class="level-left">
              </div>
              <div class="level-right">
                <b-field >
                  <b-button class="btn btn-ok" :loading="isLoading" native-type="submit">{{$gettext('ArticleOptions.form.saveButton')}}</b-button>
                </b-field>
              </div>
            </div>
          </form>
        </card-component>
      </tiles>
    </section>
  </div>
</template>

<script>
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import Tiles from '@/components/Tiles'
  import CardComponent from '@/components/CardComponent'
  import FilePicker from '@/components/FilePicker'
  import UserAvatar from '@/components/UserAvatar'
  import Notification from '@/components/Notification'
  import BField from "buefy/src/components/field/Field"
  import debounce from 'lodash/debounce'

  export default {
    name: 'ProductionSectionTemplateForm',
    components: {BField, UserAvatar, FilePicker, CardComponent, Tiles, HeroBar, TitleBar, Notification },
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

      }
    },
    computed: {
      titleStack () {
        return [
          this.$gettext('ArticleOptions.titleBar.main'),
          this.$gettext('ArticleOptions.titleBar.sub1')
        ]
      },
      heroTitle () {
        return this.$gettext('ArticleOptions.heroBar.title')
      },
      formCardTitle () {
        return this.$gettext('ArticleOptions.card.title')
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
        setTimeout(function(){ this.$refs.jeditor.editor.expandAll(); }.bind(this), 50);
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
