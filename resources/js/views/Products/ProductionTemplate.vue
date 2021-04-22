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
      <div class="json-editor">
        <vue-json-editor v-model="json" :show-btns="false" :expandedOnStart="false" @json-change="onJsonChange"/>
      </div>
    </section>
  </div>
</template>

<script>
  import Notification from '@/components/Notification'
  import CardComponent from '@/components/CardComponent'
  import TitleBar from '@/components/TitleBar'
  import HeroBar from '@/components/HeroBar'
  import BField from "buefy/src/components/field/Field";
  import vueJsonEditor from 'vue-json-editor'

  export default {
    name: 'products.list',
    components: {BField, HeroBar, TitleBar, CardComponent, Notification, vueJsonEditor},
    computed: {
      titleStack () {
        return [
          'Production',
          'Template'
        ]
      }
    },
    data () {
      return {
        isShow: false,
        tableRowData: {},
        json: [
          {
            required: 'false',
            step_name: 'visual.test.1',
            production_section_template: 1,
          },
          {
            required: 'false',
            step_name: 'backlight.test.1',
            production_section_template: 2,
          },
          {
            required: 'false',
            step_name: 'touch.test.1',
            production_section_template: 3,
          },
          {
            required: 'false',
            step_name: 'tilt.test.1',
            production_section_template: 4,
          }
        ],
      }
    },
    methods: {
      onJsonChange (value) {
        console.log('value:', value)
      }
    }
  }
</script>

