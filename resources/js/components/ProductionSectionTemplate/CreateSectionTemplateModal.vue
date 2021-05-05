<template>
  <b-modal :active="isJsonModal" has-modal-card>
    <div class="modal-card section-template-warning-modal">
      <form id="add-json" @submit.prevent="submit">
        <header class="modal-card-head">
          <p class="modal-card-title">Messkonfiguration</p>
        </header>
        <section class="modal-card-body">
          <div class="columns">
            <div class="column">
              <div class="column is-full">
                <b-field label="min">
                  <b-input type="number" v-model="form.min" step="any" required/>
                </b-field>
              </div>
              <div class="column is-full">
                <b-field label="type">
                  <b-select v-model="form.type" required>
                    <option v-for="(item, index) in typeFormat" :value="item" :key="index">{{item}}</option>
                  </b-select>
                </b-field>
              </div>
              <div class="column is-full">
                <b-field label="title">
                  <b-input type="text" v-model="form.title" required/>
                </b-field>
              </div>
              <div class="column is-full">
                <b-field>
                  <b-checkbox v-model="checkStrict">has strict?</b-checkbox>
                </b-field>
                <b-field v-if="checkStrict" label="strict">
                  <b-switch v-model="form.strict" ></b-switch>
                </b-field>
              </div>
            </div>
            <div class="column">
              <div class="column is-full">
                <b-field label="max">
                  <b-input type="number" v-model="form.max" step="any" required/>
                </b-field>
              </div>
              <div class="column is-full">
                <b-field label="unit">
                  <b-input type="text" v-model="form.unit" required/>
                </b-field>
              </div>
              <div class="column is-full">
                <b-field v-if="form.type != 'bool'" label="nominal">
                  <b-input type="number" v-model="form.nominalNum" step="any" required/>
                </b-field>
                <b-field v-else label="nominal">
                  <b-switch v-model="form.nominalBool"></b-switch>
                </b-field>
              </div>

              <div class="column is-full">
                <b-field>
                  <b-checkbox v-model="checkVerify">has verify?</b-checkbox>
                </b-field>
                <b-field v-if="checkVerify" label="verify">
                  <b-switch v-model="form.verify"></b-switch>
                </b-field>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot custom-foot">
          <b-button class="btn btn-ok"  native-type="submit">Add</b-button>
          <b-button class="btn btn-cancel"  @click="cancelModal">Cancel</b-button>
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
      }
    },
    data() {
      return {
        typeFormat: ['integer', 'double', 'bool'],
        form: this.clearFormObject(),
        checkVerify: false,
        checkStrict: false,
      }
    },
    methods: {
      clearFormObject() {
        return {
          min: 0,
          max: 0,
          type: '',
          unit: '',
          title: '',
          nominalBool: false,
          nominalNum: 0,
          strict: false,
          verify: false
        }
      },
      submit() {
        console.log(this.form)
        let data = {
          min: parseFloat(this.form.min),
          max: parseFloat(this.form.max),
          type: this.form.type,
          unit: this.form.unit,
          title: this.form.unit,
          nominal: this.form.type == 'bool' ? this.form.nominalBool : parseFloat(this.form.nominalNum)
        }
        if(this.checkStrict) {
          data['strict'] = this.form.strict
        }
        if(this.checkVerify) {
          data['verify'] = this.form.verify
        }
        this.$emit('addedJsonData', data)
        this.form = this.clearFormObject()
        this.checkStrict = false
        this.checkVerify = false
      },
      cancelModal() {
        this.$emit('cancelJsonAdd')
      }
    }
  }
</script>