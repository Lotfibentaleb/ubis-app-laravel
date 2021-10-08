<template>
  <section>
    <card-component :title="'Komponente: '+componentarticledata.articleNumber+' - '+componentarticledata.name" icon="view-grid">

        <div class="is-full">
          <h6 class="title is-5">
{{componentarticledata.articleNumber}} - {{componentarticledata.name}}
          </h6>
        </div>
        </br>
        <div class="is-full">
          <b-field label="Serial number" label-position="on-border">
            <b-input size="is-medium"
                    class="is-expanded"
                    :value="component_serial"
                    @change.native="component_serial = $event.target.value"
                    :disabled="component_id != null || globaltransmissionactive"
                    :tabindex="tabindex"/>
             <p class="control">
                <b-button v-show="component_id == null" :disabled="transmissionActive" type="is-success" label="speichern" size="is-medium"/>
                <b-button v-show="component_id != null" @click="submitComponent(true)" :disabled="transmissionActive" type="is-dark" label="löschen" size="is-medium"/>
            </p>
          </b-field>
        </div>

    </card-component>
    </section>
</template>

<script>
import CardComponent from '@/components/CardComponent'
import debounce from 'lodash/debounce'

export default {
  name: 'SubComponent',
  components: { CardComponent },
  props: {
    id: { default: null },
    componentarticledata: { type: Object, required: true }, // display components name/art.nr.
    articlenumber: { required: true },    // article number of parent article
    productid: { default: null },         // ID of parent product, if given
    componentserial:{ default: null },    // this components serial nr.
    componentid:{ default: null },         // this components id
    productionordernr:{ default: ''},
    productionordernrtype:{ default: 'is-normal'},
    globaltransmissionactive:{ default: false}, // set by parrent, so we could lock ourself
    tabindex:{ type:Number, default: 1},
  },
  data () {
    return {
      transmissionActive: false,
      component_serial: null,
      component_id: null,
      initialUpdate: false,
    }
  },
  computed: {
  },
  created () {
      console.log('Sub created');
      if( this.componentid != null ){
        this.initialUpdate = true
        // we gor some valid parent product
        this.component_serial = this.componentserial
        this.component_id = this.componentid
      }
  },
  methods: {
    // Emit product and serial nr to parent on creation
    productUpdate: function(productSerial, productId) {
      this.$emit('productUpdate', productSerial, productId)
    },
    setTransmissionActive: function () {
      console.log('Transmission is'+this.transmissionActive+' Set transmission TRUE');
      this.$emit('setTransmissionActive', true);
      this.transmissionActive = true
    },
    submitComponent: function(deleteComponent = false) {
      console.log('submitComponent entered');
      if( this.productionordernr == '' ){
        let message = 'Bitte Produktions-Auftrags-Nr. angeben! Für Tests die 777.'
        this.$buefy.toast.open({
          message: message,
          type: 'is-danger',
          queue: false
        })
        this.$emit('setProductionOrderNrType', 'is-danger')
        this.component_serial = ''; // reset input field, so it could be filled and sent again
        return
      }
      this.$emit('setProductionOrderNrType', 'is-normal')

      this.setTransmissionActive()  // inform others that we going to transmitt now

      let method = 'post'
      let url = `/registration/product/${this.productid}?article_nr=${this.articlenumber}`
      let data = {
        component_article_nr: `${this.componentarticledata.articleNumber}`,
        component_serial_nr: `${this.component_serial}`,
        production_order_nr: `${this.productionordernr}`,
      }
      if( deleteComponent ){
        method = 'delete'
        // 'products/{id}/components/{componentId}
        url = `/registration/product/${this.productid}/components/${this.component_id}`
      }

      axios({
        method,
        url,
        data
      }).then( r => {
        r = r.data.data
        console.log(r)
        let infoMessage = `Komponente mit der Ser.Nr. '${this.component_serial}' gespeichert`
        if( deleteComponent ){
          // delete handling
          infoMessage = `Komponente mit der Ser.Nr. '${this.component_serial}' gelöscht`
          this.component_id = null
          this.component_serial = null
        }else{
          // add handling
          if( this.productid == '-' || this.producproduct_idtid == null){
            // if first component is stored, a new product is implicite created, so we have to publish its ID/Serial to our parent
            this.productUpdate(r.product_serial, r.product_id)
          }
          // componente_serial still valid from input
          this.component_id = r.component_id
        }
        this.$buefy.snackbar.open({
          message: infoMessage,
          queue: false
        })
      }).catch( err => {

        let message = `Fehler: ${err.message}`
        if( err.response.status == 409){
            message = `Fehler: Komponente mit der Ser.Nr. '${this.component_serial}' kann nicht angelegt werden. Möglicherweise existiert bereits eine Komponente mit der identischen Serien Nr. oder es existiert ein Hauptprodukt mit einer nicht numerischen Serien Nr.`
        }
        if( err.response.status == 422){
            message = `Fehler: Komponente mit der Ser.Nr. '${this.component_serial}' kann nicht angelegt werden. Die Serien Nr. ist leer oder ungültig.`
        }
        this.component_serial = null
        this.$buefy.toast.open({
          message: message,

          type: 'is-danger',
          queue: false
        })
      }).finally(() => {
        this.$emit('setTransmissionActive', false);
        console.log('Transmission is '+this.transmissionActive+' Set transmission FALSE');
        this.transmissionActive = false;
      })
    }
  },
  watch: {
    component_serial : function(newValue, oldValue) {
      console.log('Sub component_serial watch triggered');
      if( newValue == null || newValue.length === 0 ){
        console.log('... but value is null');
        return;
      }
      console.log('Value was changed from ' + oldValue + ' to ' + newValue )
      if( this.transmissionActive != true && this.initialUpdate == false){  // mutal exclusive sending
        this.submitComponent(false);
      }
      this.initialUpdate = false;
    }
  }
}
</script>
