<template>
  <card-component :title="$gettext('exportsPage.excelExports.card.title')" icon="package-variant-closed" class="tile is-child">
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
      <p>{{$gettext('exportsPage.excelExports.explanation')}}</p>
    </div>
    <div class="column is-1 is-offset-11">
      <a :href="hrefUrl"><b-button :label="$gettext('exportsPage.excelExports.exportBtn')" type="is-primary" /></a>
    </div>
  </card-component>
</template>

<script>
  import CardComponent from '@/components/CardComponent'
  import BButton from "buefy/src/components/button/Button";
  import DateRangePicker from 'vue2-daterange-picker'
  import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

  export default {
    name: 'ExcelExports',
    components: {BButton, CardComponent, DateRangePicker},
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
        hrefUrl: '',
        dataUrl: '/productlist',
        sortField:'created_at',
        sortOrder:'desc',
        filterValues: '{}',
      }
    },
    created () {
      this.getHrefURL()
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
      updateDateRange() {
        this.dateRangeValues = '';
        this.dateRangeValues = encodeURIComponent(JSON.stringify(this.dateRange));
        this.getHrefURL()
      },
      getHrefURL() {
        const paramsFullExcel = [
          `enhanced=2`,
          `sort_by=${this.sortField}.${this.sortOrder}`,
          `page=${this.page}`,
          `date_range=${this.dateRangeValues}`,
          `filter=${this.filterValues}`
        ].join('&')
        this.hrefUrl = this.dataUrl + '/fullExcel?' + paramsFullExcel
      }
    }
  }
</script>