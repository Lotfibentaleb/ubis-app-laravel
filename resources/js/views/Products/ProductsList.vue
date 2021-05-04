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
      <div class="columns is-mobile">
        <div class="column" v-bind:class="{'is-four-fifths': isClickedRow}">
          <card-component class="has-table has-mobile-sort-spaced" title="Produkte" icon="account-multiple">
            <products-table data-url="/productlist" @clickedRow="clickedRow" :checkable="true"/>
          </card-component>
        </div>
        <div v-if="isClickedRow" class="column">
          <card-component title="Product Order Nr" class="has-mobile-sort-spaced" icon="lead-pencil">
            <b-field label="Product Order Nr" message="selected product order nr">
              <b-input type="text" v-model="selectedPdOrNr" />
            </b-field>
            <div class="level">
              <div class="level-left">
                <div><b-button class="btn btn-ok-small" @click="onSettingSave">Save</b-button></div>
              </div>
              <div class="level-right">
                <div><b-button class="btn btn-cancel-small" @click="onSettingPanelClose">Close</b-button></div>
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
  import TableEditPanel from "@/components/TableEditPanel";

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, ProductsTable, Notification, TableEditPanel },
    computed: {
      titleStack () {
        return [
          'Production',
          'Products'
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

