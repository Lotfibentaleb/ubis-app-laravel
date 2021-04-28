<template>
  <!--<modal-box :is-active.sync="isActive" @confirm="confirm" @cancel="cancel" confirm-type="is-danger" confirm-label="Cancel">-->
    <!--<p>Dauerhaftes Löschen von <b>{{ modalSubject }}</b>?</p>-->
    <!--<p>Diese Aktion kann nicht rückgängig gemacht werden</p>-->
  <!--</modal-box>-->

  <b-modal :active="isActive" has-modal-card>
    <div class="modal-card section-template-modal">
      <header class="modal-card-head">
        <p class="modal-card-title">Modal to check the changed data</p>
      </header>
      <section class="modal-card-body">
        <p>You must save the changed data before selecting data</p>
      </section>
      <footer class="modal-card-foot custom-foot">
        <b-button class="btn btn-ok"  @click="confirm">Save</b-button>
        <b-button class="btn btn-cancel"  @click="cancel">Cancel</b-button>
      </footer>
    </div>
  </b-modal>

</template>

<script>
  import ModalBox from "../ModalBox";
  export default {
    name: "ModalUpdateCheck",
    components: {ModalBox},
    props: {
      isActive: {
        type: Boolean,
        default: false
      },
      modalSubject: {
        type: String,
        default: null
      }
    },
    methods: {
      cancel () {
        this.$emit('cancel')
      },
      confirm () {
        this.$emit('confirm')
      }
    }
  }
</script>

