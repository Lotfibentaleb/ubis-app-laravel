<template>

    <card-component :title="$gettext('bulkRegisterPage.card.title')" icon="account-edit" class="tile is-child">
      <div class="column is-one-fifths is-offset-two-fifths">
        <p>{{$gettext('bulkRegisterPage.uploadComponents.title')}}</p>
      </div>
      <div class="column is-4 is-offset-4">
        <section>
          <b-field>
            <b-upload v-model="dropFiles"
                      multiple
                      drag-drop
                      accept=".csv">
              <section class="section">
                <div class="content has-text-centered">
                  <p>
                    <b-icon
                            icon="upload"
                            size="is-large">
                    </b-icon>
                  </p>
                  <p>{{$gettext('bulkRegisterPage.uploadComponents.content')}}</p>
                </div>
              </section>
            </b-upload>
          </b-field>

          <div class="tags">
                <span v-for="(file, index) in dropFiles"
                      :key="index"
                      class="tag is-primary" >
                    {{file.name}}
                    <button class="delete is-small"
                            type="button"
                            @click="deleteDropFile(index)">
                    </button>
                </span>
          </div>
        </section>
      </div>
      <div class="column is-12">
        <b-field :label="$gettext('bulkRegisterPage.articleNr')" :message="$gettext('bulkRegisterPage.articleNr.message')" horizontal>
          <b-autocomplete
                  :data="articleList"
                  v-model="articleNr"
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
      </div>
      <div class="column is-12">
        <b-field :label="$gettext('bulkRegisterPage.productionOrderNr')" :message="$gettext('bulkRegisterPage.productionOrderNr.message')" horizontal>
          <b-input placeholder="e.g. 2021-1446" v-model="prodOrderNr" required />
        </b-field>
      </div>
      <div class="column is-1 is-offset-11">
        <b-button :label="$gettext('bulkRegisterPage.registerButton')" type="is-primary" @click="register"></b-button>
      </div>
      <div v-if="isResponseData" class="column is-12">
        <b-table
                :loading="isLoading"
                :striped="true"
                :hoverable="true"
                :data="tableData">

          <template slot-scope="props">

            <b-table-column label="productId" field="productId">
              {{ getProdId(props.row) }}
            </b-table-column>
            <b-table-column label="SerialNr" field="serialNr">
              {{ props.row.serialNr }}
            </b-table-column>
            <b-table-column label="ArticleNr" field="">
              {{ articleNr }}
            </b-table-column>
            <b-table-column label="Status" field="">
                  <span v-if="props.row.status" class="tag is-success">
                    {{getStatus(props.row.status)}}
                  </span>
              <span v-else class="tag is-danger">
                    {{getStatus(props.row.status)}}
                  </span>
            </b-table-column>
          </template>

          <section class="section" slot="empty">
            <div class="content has-text-grey has-text-centered">
              <template v-if="isLoading">
                <b-loading :is-full-page="isFullPage" v-model="isLoading">
                  <b-icon
                          pack="fas"
                          icon="sync-alt"
                          size="is-large"
                          custom-class="fa-spin">
                  </b-icon>
                </b-loading>
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
          </template>
        </b-table>
      </div>
    </card-component>
</template>
<script>
  import CardComponent from '@/components/CardComponent'
  import FilePicker from '@/components/FilePicker'
  import BButton from "buefy/src/components/button/Button";
  import debounce from 'lodash/debounce'

  export default {
    name: 'BulkRegister',
    components: {BButton, FilePicker, CardComponent, },
    data () {
      return {
        item: null,
        createdReadable: null,
        dropFiles: [],
        articleNr: '',
        prodOrderNr: '',
        isResponseData: false,

        //auto complete
        articleList: [],
        articleSelected: null,
        isFetchingArticleList: false,
        clearable: false,

        tableData: [],

        //loading
        isLoading: false,
        isFullPage: true
      }
    },
    computed: {

    },
    created () {

    },
    methods: {
      deleteDropFile(index) {
        this.dropFiles.splice(index, 1)
      },
      register() {

        if(this.prodOrderNr == '' || this.articleNr == '') {
          this.$buefy.snackbar.open({
            message: 'Error: Please check all input parameters',
            type: 'is-danger',
            queue: false
          })
          return
        } else if(this.dropFiles.length == 0) {
          this.$buefy.snackbar.open({
            message: 'Error: Please upload csv file for serial nr',
            type: 'is-danger',
            queue: false
          })
          return
        }
        this.isResponseData = true
        this.isLoading = true
        this.tableData = []
        let data = new FormData();
        data.append('productionOrderNr', this.prodOrderNr)
        data.append('articleNr', this.articleNr)
        data.append('file', this.dropFiles[0])

        axios.post('/productlist/bulkregister', data)
            .then(r => {
              if(r.data) {
                this.isLoading = false
                this.tableData = r.data
                this.$buefy.snackbar.open({
                  message: 'Succeed register',
                  queue: false
                })
              }
            })
            .catch(err => {
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
          250),
      getStatus(state) {
        if(state) {
          return 'Created'
        } else {
          return 'Failed'
        }
      },
      getProdId(row) {
        if(row.status) {
          return row.productId
        } else {
          return ''
        }
      }
    },
    watch: {

    }
  }
</script>