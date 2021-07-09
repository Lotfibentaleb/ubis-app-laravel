<template>
  <card-component :title="$gettext('exportsPage.excelExports.card.title')" icon="package-variant-closed" class="tile is-child">
    <div class="level">
      <div class="level-left"></div>
      <div class="level-right">
        <b-icon
                icon="calendar-today">
        </b-icon>
        <date-range-picker
                ref="picker"
                v-model="dateRange"
                @update="updateDateRange"
        >
          <template v-slot:input="picker" style="min-width: 350px;">
            {{ picker.startDate | moment("DD.MM.YYYY") }} - {{ picker.endDate | moment("DD.MM.YYYY") }}
          </template>
        </date-range-picker>
      </div>
    </div>
    <div class="column is-one-fifths is-offset-two-fifths">
      <p>{{$gettext('exportsPage.excelExports.explanation')}}</p>
    </div>
    <div class="column is-12">
      <b-field :label="$gettext('exportsPage.excelExports.articleNr')" :message="$gettext('exportsPage.excelExports.articleNr.message')" horizontal>
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
      <b-field :label="$gettext('exportsPage.excelExports.productionOrderNr')" :message="$gettext('exportsPage.excelExports.productionOrderNr.message')" horizontal>
        <b-input placeholder="e.g. 2021-1446" v-model="prodOrderNr" required />
      </b-field>
    </div>
    <div class="column is-1 is-offset-11">
      <a :href="hrefUrl"><b-button :label="$gettext('exportsPage.excelExports.exportBtn')" type="is-primary" /></a>
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  import BButton from "buefy/src/components/button/Button"
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
  import debounce from 'lodash/debounce'

  export default {
    name: 'ExcelExports',
    components: {BButton, CardComponent, DateRangePicker},
    data() {
      const today = new Date()
      return {
        dateRange: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        dateRangeValues: '{}',
        hrefUrl: '',
        dataUrl: '/productlist',
        sortField:'created_at',
        sortOrder:'desc',
        filterValues: '{}',

        //auto complete
        articleNr: '',
        articleList: [],
        articleSelected: null,
        isFetchingArticleList: false,
        clearable: false,

        prodOrderNr: '',
      }
    },
    created () {
      this.getHrefURL()
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
    watch: {
      articleNr: function() {
        this.setFilterValues()
      },
      prodOrderNr: function() {
        this.setFilterValues()
      }
    },
    methods: {
      updateDateRange() {
        this.dateRangeValues = '';
        this.dateRangeValues = encodeURIComponent(JSON.stringify(this.dateRange));
        this.getHrefURL()
      },
      getHrefURL() {
        const paramsFullExcel = [
          `enhanced=2`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `date_range=${this.dateRangeValues}`,
          `filter=${this.filterValues}`
        ].join('&')
        this.hrefUrl = this.dataUrl + '/fullExcel?' + paramsFullExcel
      },
      setFilterValues() {
        let objData = {}
        if(this.articleNr != '') {
          if(this.prodOrderNr != '') {
            objData = {
              production_order_nr: this.prodOrderNr,
              st_article_nr: this.articleNr
            }
          }else {
            objData = {
              st_article_nr: this.articleNr
            }
          }
        }else {
          if(this.prodOrderNr != '') {
            objData = {
              production_order_nr: this.prodOrderNr
            }
          }
        }
        this.filterValues = '';
        this.filterValues = encodeURIComponent(JSON.stringify(objData));
        this.getHrefURL()
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
    }
  }
</script>