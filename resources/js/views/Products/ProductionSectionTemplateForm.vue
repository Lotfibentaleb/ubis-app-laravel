<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/products/section/template" class="button">
        Section Templates
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <card-component :title="formCardTitle" icon="package-variant-closed" class="tile is-child">
          <form @submit.prevent="submit">
            <b-field :label="$gettext('createProductionSectionPage.card.name')" :message="$gettext('createProductionSectionPage.card.nameMessage')">
              <b-input placeholder="e.g. measurement.daisy" v-model="form.name" required expanded/>
            </b-field>
            <b-field :label="$gettext('createProductionSectionPage.card.render')">
              <b-select placeholder="default" v-model="form.render_hint" required expanded>
                <option v-for="(render_hints, index) in formHelper.render_hints" :key="index" :value="render_hints">
                  {{ render_hints }}
                </option>
              </b-select>
            </b-field>
            <b-field :label="$gettext('createProductionSectionPage.card.group')">
              <b-select placeholder="default" v-model="form.group" required expanded>
                <option v-for="(groups, index) in formHelper.groups" :key="index" :value="groups">
                  {{ groups }}
                </option>
              </b-select>
            </b-field>
            <b-field :label="$gettext('createProductionSectionPage.card.description')" :message="$gettext('createProductionSectionPage.card.descriptionMessage')" expanded>
              <b-input type="textarea" placeholder="Explain how we can help you" v-model="form.description" maxlength="255" required/>
            </b-field>
            <b-field expanded>
              <b-tooltip :label="JSON.stringify(basicData, null, 2)" position="is-right" size="is-large" multilined>
                <b-button v-if="!isJsonEmpty" @click="clickedAddJsonBtn">{{$gettext('createProductionSectionPage.card.addBasicDataButton')}}</b-button>
                <b-button v-else type="is-danger" @click="clickedAddJsonBtn">{{$gettext('createProductionSectionPage.card.addBasicDataButton')}}</b-button>
              </b-tooltip>
            </b-field>
            <div class="level">
              <div class="level-left">
              </div>
              <div class="level-right">
                <b-field >
                  <b-button class="btn btn-ok" :loading="isLoading" native-type="submit">{{$gettext('createProductionSectionPage.card.submitButton')}}</b-button>
                </b-field>
              </div>
            </div>
          </form>
        </card-component>
        <card-component  v-if="hasJsonItem" :title="$gettext('createProductionSectionPage.card.data')" icon="package-variant-closed" class="tile is-child">
          <b-field :label="$gettext('createProductionSectionPage.card.data')" :message="$gettext('createProductionSectionPage.card.data')" >
            <v-jsoneditor ref="jeditor" v-model="jsonData" :options="options" :plus="false"/>
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
  import BTooltip from "buefy/src/components/tooltip/Tooltip"
  import ProductionSectionSchema from '@/schema/ProductionSectionSchema'

  const ajv = new Ajv()

  export default {
    name: 'ProductionSectionTemplateForm',
    components: {
      BTooltip,
      BField, UserAvatar, FilePicker, CardComponent, VJsoneditor, Tiles, HeroBar, TitleBar, Notification },
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
        item: null,
        form: this.getClearFormObject(),
        createdReadable: null,
        formHelper: [],
        jsonData: [],
        isJsonEmpty: false,
        basicData: {
          max: 999.5,
          min: -999.5,
          type: 'double',
          unit: '°C',
          title: 'sample_value',
          strict: false,
          nominal: 0
        },

        options: {
          mode: 'tree',
          modes: ['code', 'tree'], // allowed modes
          enableTransform: false,
          enableSort: false,
        },

        //json schema
        isValidSchema: true,
        schemaErrorData: {},
        errorMessage: {
          enumValues: 'must be equal to one of the allowed values',
          hasSeveralTypes: ''
        },
        schemaData: ProductionSectionSchema
      }
    },
    computed: {
      titleStack () {
        return [
          this.$gettext('createProductionSectionPage.titleBar.main'),
          this.$gettext('createProductionSectionPage.titleBar.sub1'),
        ]
      },
      heroTitle () {
        return this.$gettext('createProductionSectionPage.heroBar.title')
      },
      formCardTitle () {
        return this.$gettext('createProductionSectionPage.card.title')
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
    created () {
      this.updateFormHelper()
    },
    methods: {
      clickedAddJsonBtn() {
        this.addedJsonData(this.basicData)
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
      getClearFormObject () {
        return {
          name: null,
          render_hint: null,
          group: null,
          description: null,
        }
      },
      updateFormHelper() {
        axios.get(`/production_section/form_support`, {
          headers: {
            "Content-Type": "application/json"
          }
        })
            .then( result => {
              this.formHelper = result.data;
            })
            .catch( error => {
              console.log('Error', error.message);
            })
            .finally(() => {
            })
      },
      submit () {
        if(this.jsonData.length == 0) {
          this.isJsonEmpty = true
          this.$buefy.snackbar.open({
            type: 'is-danger',
            message: 'Empty data field, Please add data',
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
        let url = '/production_section'
        let data = {
          name: this.form.name,
          description: this.form.description,
          group: this.form.group,
          data:JSON.stringify(this.jsonData),
          render_hint: this.form.render_hint
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
