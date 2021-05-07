<template>
  <b-modal :active="isActive" has-modal-card>
    <div class="modal-card section-template-warning-modal">
      <header class="modal-card-head">
        <p class="modal-card-title">Achtung! Daten wurden geändert aber noch nicht gesichert</p>
      </header>
      <section class="modal-card-body">
        <p>Wollen sie die Änderungen sichern oder verwerfen?</p>
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

