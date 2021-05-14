<template>
  <section>
    <b-field>
      <b-button
              label="Generate Api Access Token"
              type="is-success"
              @click="generateApiToken" />
    </b-field>

    <b-tabs>
      <b-tab-item label="Api Tokens">
        <b-table
                :data="data"
                :selected.sync = "selected">
          <template slot-scope="props">
            <b-table-column label="No" field="id">
              {{ props.row.id }}
            </b-table-column>
            <b-table-column label="Tokenable ID" field="tokenable_id">
              {{ props.row.tokenable_id }}
            </b-table-column>
            <b-table-column label="Api Access Token" field="api_access_token" style="display: flex; justify-content: space-between">
              {{ props.row.api_access_token }}
              <div v-if="flagEyes[props.row.id - 1]">
                <font-awesome-icon icon="eye" @click.prevent="toggleToken(props.row)"/>
              </div>
              <div v-if="!flagEyes[props.row.id - 1]">
                <font-awesome-icon icon="eye-slash" @click.prevent="toggleToken(props.row)"/>
              </div>
            </b-table-column>
            <b-table-column label="Created At" field="created_at">
              {{ props.row.created_at }}
            </b-table-column>
            <b-table-column label="Updated At" field="updated_at">
              {{ props.row.updated_at }}
            </b-table-column>
            <b-table-column custom-key="actions" class="is-actions-cell">
              <div class="buttons is-right">
                <button class="button is-small is-danger" type="button">
                  <b-icon icon="trash-can" size="is-small"/>
                </button>
              </div>
            </b-table-column>
          </template>
        </b-table>
      </b-tab-item>

      <b-tab-item label="Detail Info">
        <pre>{{ selected }}</pre>
      </b-tab-item>
    </b-tabs>
  </section>
</template>

<script>
  import BButton from "buefy/src/components/button/Button";
  export default {
    components: {BButton},
    data() {
      const data = [
        { 'id': 1, 'tokenable_id': '1', 'api_access_token': '79abfe70742365694cebdc44d24bcba75084feaabe18eb1aa64f', 'created_at': '2016-10-15 13:43:27', 'updated_at': '2016-10-15 13:43:27' },
        { 'id': 2, 'tokenable_id': '1', 'api_access_token': '7fh074236ebdc44d24dfb87fcbbbcba75084feaabe18eb1aa64f', 'created_at': '2016-12-15 06:00:53', 'updated_at': '2016-12-15 06:00:53' },
        { 'id': 3, 'tokenable_id': '1', 'api_access_token': '742365694cebdc44d24dfb885c7fcbbbcba75084feaabe18eb1a', 'created_at': '2016-04-26 06:26:28', 'updated_at': '2016-04-26 06:26:28' }
      ]
      const prevData = [
        { 'id': 1, 'tokenable_id': '1', 'api_access_token': '79abfe70742365694cebdc44d24bcba75084feaabe18eb1aa64f', 'created_at': '2016-10-15 13:43:27', 'updated_at': '2016-10-15 13:43:27' },
        { 'id': 2, 'tokenable_id': '1', 'api_access_token': '7fh074236ebdc44d24dfb87fcbbbcba75084feaabe18eb1aa64f', 'created_at': '2016-12-15 06:00:53', 'updated_at': '2016-12-15 06:00:53' },
        { 'id': 3, 'tokenable_id': '1', 'api_access_token': '742365694cebdc44d24dfb885c7fcbbbcba75084feaabe18eb1a', 'created_at': '2016-04-26 06:26:28', 'updated_at': '2016-04-26 06:26:28' }
      ]
      return {
        data,
        prevData,
        selected: data[1],
        columns: [
          {
            field: 'id',
            label: 'No',
            width: '40',
            numeric: true
          },
          {
            field: 'tokenable_id',
            label: 'Tokenable ID',
          },
          {
            field: 'api_access_token',
            label: 'Api Access Token',
          },
          {
            field: 'created_at',
            label: 'Created At',
            centered: true
          },
          {
            field: 'updated_at',
            label: 'Updated At',
          }
        ],
        flagEyes: [true, true, true]
      }
    },
    created() {
      this.data.forEach(item => {
        item.api_access_token = this.madeHiddenStr(item.api_access_token)
      })
    },
    methods: {
      toggleToken(row) {
        this.flagEyes[row.id - 1] = !this.flagEyes[row.id - 1]
        this.data[row.id - 1].api_access_token = !this.flagEyes[row.id - 1] ?
            this.prevData[row.id - 1].api_access_token :
            this.madeHiddenStr(this.prevData[row.id - 1].api_access_token)
      },
      generateApiToken() {
        const data = {
          id: this.data.length + 1,
          tokenable_id: '1',
          api_access_token: '79abfe70742365694cebdc44d24dfb885c7fcbbbcba75084feaabe18eb1aa64f',
          created_at: '2016-04-26 06:26:28',
          updated_at: '2016-04-26 06:26:28'
        }
        this.data.push(data)
        this.prevData.push(data)
        this.flagEyes.push(true)
        this.data.forEach(item => {
          item.api_access_token = this.madeHiddenStr(item.api_access_token)
        })
      },
      madeHiddenStr(strParam) {
        let strResult = ''
        let i = 0
        while(i < strParam.length){
          strResult += '*'
          i++
        }
        return strResult;
      }
    }
  }
</script>