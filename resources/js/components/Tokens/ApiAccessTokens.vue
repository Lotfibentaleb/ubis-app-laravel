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
            <b-table-column label="Api Access Token" field="token" style="display: flex; justify-content: space-between">
              {{ props.row.token }}
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
      const data = []
      return {
        data,
        selected: data[1],
      }
    },
    created() {
      this.getApiTokens()
    },
    methods: {
      getApiTokens() {
        axios.get('/tokens/getApiTokens')
            .then(r => {
              if(r.data && r.data.data) {
                if(r.data.data.length !=0 ){
                  this.data = r.data.data
                  this.data.forEach(item => {
                    item.token = this.madeHiddenStr(item.token)
                  })
                }
              }
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
      generateApiToken() {
        axios.get('/tokens/createApiToken')
            .then(r => {
              this.hasToken = true
              console.log(r.data.data)
              this.data = r.data.data
              this.data.forEach(item => {
                item.token = this.madeHiddenStr(item.token)
              })
              this.data[this.data.length - 1].token =  r.data['api-access']
              this.$buefy.toast.open({
                message: 'This is the only time you will see this token, make sure to copy it now.',
                type: 'is-success',
                queue: false
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
      },
    }
  }
</script>