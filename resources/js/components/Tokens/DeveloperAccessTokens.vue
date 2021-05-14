<template>
  <b-table :data="data" >
    <template slot-scope="props">
      <b-table-column label="No" field="id">
        {{ props.row.id }}
      </b-table-column>
      <b-table-column label="User Name" field="user_name">
        {{ props.row.user_name }}
      </b-table-column>
      <b-table-column label="Developer Access Token" field="developer_access_token" style="display: flex; justify-content: space-between">
        {{ props.row.developer_access_token }}
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
</template>

<script>
  export default {
    data() {
      return {
        data: [
          { 'id': 1, 'user_name': 'Markus Harrieder', 'developer_access_token': 'fszAXREUX4X7VkzRDMPisW3E0pMINqdxBQxebJ4H1G1gdGUMSY1o8N3tSUs', 'created_at': '2016-10-15 13:43:27', 'updated_at': '2016-10-15 13:43:27' },
          { 'id': 2, 'user_name': 'Test API Benutzer', 'developer_access_token': 'RDMPisW3E0pMjyvbdwR3vZJuHsCfcESNQxebJ4H1G1gdGUMSY1o8N3tSUs', 'created_at': '2016-12-15 06:00:53', 'updated_at': '2016-12-15 06:00:53' },
          { 'id': 3, 'user_name': 'Stefan Klette', 'developer_access_token': 'XREUX4X7VkzRisW3E0pMINqdxBUjyvbdwR3vZJuHsCfcESNQxebJ4H1G1gd', 'created_at': '2016-04-26 06:26:28', 'updated_at': '2016-04-26 06:26:28' }
        ],
        prevData: [
          { 'id': 1, 'user_name': 'Markus Harrieder', 'developer_access_token': 'fszAXREUX4X7VkzRDMPisW3E0pMINqdxBQxebJ4H1G1gdGUMSY1o8N3tSUs', 'created_at': '2016-10-15 13:43:27', 'updated_at': '2016-10-15 13:43:27' },
          { 'id': 2, 'user_name': 'Test API Benutzer', 'developer_access_token': 'RDMPisW3E0pMjyvbdwR3vZJuHsCfcESNQxebJ4H1G1gdGUMSY1o8N3tSUs', 'created_at': '2016-12-15 06:00:53', 'updated_at': '2016-12-15 06:00:53' },
          { 'id': 3, 'user_name': 'Stefan Klette', 'developer_access_token': 'XREUX4X7VkzRisW3E0pMINqdxBUjyvbdwR3vZJuHsCfcESNQxebJ4H1G1gd', 'created_at': '2016-04-26 06:26:28', 'updated_at': '2016-04-26 06:26:28' }
        ],
        flagEyes: [true, true, true]
      }
    },
    created() {
      this.data.forEach(item => {
        item.developer_access_token = this.madeHiddenStr(item.developer_access_token)
      })
    },
    methods: {
      toggleToken(row) {
        this.flagEyes[row.id - 1] = !this.flagEyes[row.id - 1]
        this.data[row.id - 1].developer_access_token = !this.flagEyes[row.id - 1] ?
            this.prevData[row.id - 1].developer_access_token :
            this.madeHiddenStr(this.prevData[row.id - 1].developer_access_token)
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