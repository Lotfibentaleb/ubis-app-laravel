<template>
  <b-table :data="data" >
    <template slot-scope="props">
      <b-table-column label="No" field="id">
        {{ props.row.id }}
      </b-table-column>
      <b-table-column label="User Name" field="tokenable_id">
        {{ props.row.tokenable_id }}
      </b-table-column>
      <b-table-column label="Developer Access Token" field="token" style="display: flex; justify-content: space-between">
        {{ props.row.token }}
        <!--<div v-if="flagEyes[props.row.id - 1]" style="cursor: pointer">-->
          <!--<font-awesome-icon icon="eye" @click.prevent="toggleToken(props.row)"/>-->
        <!--</div>-->
        <!--<div v-else style="cursor: pointer">-->
          <!--<font-awesome-icon icon="eye-slash" @click.prevent="toggleToken(props.row)"/>-->
        <!--</div>-->
      </b-table-column>
      <b-table-column label="Created At" field="created_at">
        {{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss") }}
      </b-table-column>
      <b-table-column label="LastUsed At" field="last_used_at">
        {{ props.row.last_used_at | moment("DD.MM.YYYY / k:mm:ss") }}
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
        data: []
      }
    },
    created() {
      this.getDeveloperTokens()
      this.data.forEach(item => {
        item.token = this.madeHiddenStr(item.developer_access_token)
      })
    },
    methods: {
      getDeveloperTokens() {
        axios.get('/tokens/getDeveloperTokens')
          .then(r => {
            console.log(r.data)
            this.data = r.data.data
            this.data.forEach(item => {
              item.token = this.madeHiddenStr(item.token)
            })
          })
          .catch(err => {
            if (err.response.status == 401) {
              document.getElementById('logout-form').submit()
            } else {
              this.isLoading = false
              this.$buefy.toast.open({
                message: `Error: ${err.message}`,
                type: 'is-danger',
                queue: false
              })
            }
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