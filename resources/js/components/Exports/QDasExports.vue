<template>
  <card-component :title="$gettext('exportsPage.qdasExports.card.title')" icon="package-variant-closed" class="tile is-child">
    <div class="level">
      <div class="level-left"></div>
      <div class="level-right">
        <b-icon
                icon="calendar-today">
        </b-icon>
        <date-range-picker
                ref="picker"
                :timePicker="timePicker"
                :timePicker24Hour="timePicker24Hour"
                v-model="dateRange"
                @update="updateDateRange"
        >
          <template v-slot:input="picker" style="min-width: 350px;">
            {{ picker.startDate | moment("DD.MM.YYYY / k:mm:ss") }} - {{ picker.endDate | moment("DD.MM.YYYY / k:mm:ss") }}
          </template>
        </date-range-picker>
      </div>
    </div>
    <div class="column is-one-fifths is-offset-two-fifths">
      <p>{{$gettext('exportsPage.qdasExports.explanation')}}</p>
    </div>
    <div class="column is-1 is-offset-11">
      <b-button :label="$gettext('exportsPage.qdasExports.exportBtn')" type="is-primary" @click="qdisExports" />
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

  export default {
    name: 'QDasExports',
    components: {CardComponent, DateRangePicker},
    data() {
      const today = new Date()
      return {
        dateRange: {
          startDate: new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()),
          endDate: today,
        },
        dateRangeValues: '{}',
        timePicker: true,
        timePicker24Hour: true,
      }
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
    methods: {
      qdisExports() {

      },
      updateDateRange() {
        this.dateRangeValues = ''
        this.dateRangeValues = encodeURIComponent(JSON.stringify(this.dateRange))
      },
    }
  }
</script>