<template>
  <b-modal :active="isJsonModal" has-modal-card>
    <div class="modal-card section-template-warning-modal">
      <form id="add-json" @submit.prevent="submit">
        <header class="modal-card-head">
          <p class="modal-card-title">{{$gettext('createProductionTemplatePage.productionFlowModal.title')}}</p>
        </header>
        <section class="modal-card-body">
          <div class="column is-full">
            <b-field  label="required" horizontal>
              <b-switch v-model="form.required"></b-switch>
            </b-field>
          </div>
          <div class="column is-full">
            <b-field label="step_name">
              <b-input type="text" placeholder="e.g. backlight.test.1" v-model="form.step_name" required/>
            </b-field>
          </div>
          <div class="column is-full">
            <b-field label="Production Section template">
              <b-dropdown v-model="selectedSectionIndex" aria-role="list" class="section-template-drop-down-for-adding">
                <template #trigger>
                  <b-button
                          icon-right="menu-down">
                    {{setDropDownLabel(selectedSectionIndex)}}
                  </b-button>
                </template>
                <b-dropdown-item v-for="(availableSectionName, index) in sectionData.availableNames" :value="index" :key="index" aria-role="listitem">
                  <div class="media">
                    <div class="media-content">
                      <h3>{{availableSectionName + ' [' + sectionData.availableIds[index] + ']'}}</h3>
                      <small>{{'group' + ': ' + sectionData.availableGroups[index]}}</small>
                    </div>
                  </div>
                </b-dropdown-item>
              </b-dropdown>
            </b-field>
          </div>
          <!--to avoid y-scroll-->
          <br><br><br>
          <!---->
        </section>
        <footer class="modal-card-foot custom-foot">
          <b-button class="btn btn-ok"  native-type="submit">{{$gettext('createProductionTemplatePage.productionFlowModal.addButton')}}</b-button>
          <b-button class="btn btn-cancel"  @click="cancelModal">{{$gettext('createProductionTemplatePage.productionFlowModal.cancelButton')}}</b-button>
        </footer>
      </form>
    </div>
  </b-modal>
</template>

<script>

  import CardComponent from '@/components/CardComponent'
  import BField from "buefy/src/components/field/Field";
  import BInput from "buefy/src/components/input/Input";
  import BSelect from "buefy/src/components/select/Select";
  import BButton from "buefy/src/components/button/Button";

  export default {
    components: {BButton, BSelect, BInput, BField, CardComponent},
    props: {
      isJsonModal: {
        type: Boolean,
        default: false
      },
      sectionData: {
        type: Object,
        default: {}
      }
    },
    data() {
      return {
        form: this.clearFormObject(),
        selectedSectionIndex: 0,
      }
    },
    methods: {
      clearFormObject() {
        return {
          required: false,
          step_name: '',
          production_section_template: '',
        }
      },
      setDropDownLabel(index) {
        return this.sectionData.availableNames[index] + ' [' + this.sectionData.availableIds[index] + ']'
      },
      submit() {
        this.form.production_section_template = this.sectionData.availableIds[this.selectedSectionIndex]
        let data = {
          required: this.form.required,
          step_name: this.form.step_name,
          production_section_template: this.form.production_section_template,
        }
        this.$emit('addedJsonData', data)
        this.form = this.clearFormObject()
      },
      cancelModal() {
        this.$emit('cancelJsonAdd')
      }
    }
  }
</script>