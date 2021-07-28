<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{$gettext('productsPage.heroBar.title')}}
      <p class="subtitle">
        {{$gettext('productsPage.heroBar.subTitle')}}
      </p>
      <router-link slot="right" to="/" class="button">
        {{$gettext('productsPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <div class="columns is-mobile">
        <div class="column" v-bind:class="{'is-10': isClickedRow}">
          <card-component class="has-table has-mobile-sort-spaced" :title="$gettext('productsPage.productsTable.title')" icon="package-variant-closed">
            <products-table data-url="/productlist" @clickedRow="clickedRow" :checkable="true"/>
          </card-component>
        </div>
        <div v-if="isClickedRow" class="column">
          <card-component :title="$gettext('productsPage.productsSettingPanel.title')" class="has-mobile-sort-spaced" icon="lead-pencil">
            <b-field :label="$gettext('productsPage.productsSettingPanel.label')" :message="$gettext('productsPage.productsSettingPanel.fieldMessage')">
              <b-input type="text" v-model="selectedPdOrNr" />
            </b-field>
            <div class="level">
              <div class="level-left">
                <div><b-button class="btn btn-ok-small" @click="onSettingSave">{{$gettext('productsPage.productsSettingPanel.saveButton')}}</b-button></div>
              </div>
              <div class="level-right">
                <div><b-button class="btn btn-cancel-small" @click="onSettingPanelClose">{{$gettext('productsPage.productsSettingPanel.closeButton')}}</b-button></div>
              </div>
            </div>
          </card-component>
        </div>
      </div>
    </section>
  </div>

</template>

<script>
  import Notification from '@/components/Notification'
  import ProductsTable from '@/components/ProductsTable'
  import CardComponent from '@/components/CardComponent'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import BField from "buefy/src/components/field/Field";

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, ProductsTable, Notification },
    computed: {
      titleStack () {
        return [
          this.$gettext('productsPage.titleBar.main'),
          this.$gettext('productsPage.titleBar.sub1')
        ]
      },
    },
    watch: {
    },
    data () {
      return {
        tableRowData: {},
        isClickedRow: false,
        selectedPdOrNr: ''
      }
    },
    methods: {
      clickedRow(rowData) {
        this.isClickedRow = true
        this.tableRowData = rowData
        this.selectedPdOrNr = this.tableRowData.production_order_nr
      },
      onSettingPanelClose () {
        this.isClickedRow = false
      },
      onSettingSave() {
        if (this.selectedPdOrNr === '' || this.selectedPdOrNr === null)
        {
          let infoMessage = `Please input product order number`
          this.$buefy.snackbar.open({
            message: infoMessage,
            type: 'is-danger',
            queue: false
          })
          return;
        }
        this.tableRowData.production_order_nr = this.selectedPdOrNr
        let method = 'put'
        let productId = this.tableRowData.id
        let url = `/productlist/product/${productId}`
        var data = this.tableRowData
        axios({
          method,
          url,
          data
        }).then( r => {
          this.productDetails = r.data.data
          let infoMessage = `Product update success`
          this.$buefy.snackbar.open({
            message: infoMessage,
            queue: false
          })
        }).catch( err => {
          let message = `Fehler: ${err.message}`
          if( err.response.status == 404){
            message = `Product update failed`
          }
          this.$buefy.toast.open({
            message: message,
            type: 'is-danger',
            queue: false
          })
        }).finally(() => {

        })
      }
    }
  }
</script>

