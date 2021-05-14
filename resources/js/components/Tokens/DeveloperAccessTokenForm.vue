<template>
  <b-table :data="data" >
    <template slot-scope="props">
      <b-table-column label="Token Name" field="token_name">
        {{ props.row.token_name }}
      </b-table-column>
      <b-table-column label="Developer Access Token" field="developer_access_token" style="display: flex; justify-content: space-between">
        {{ props.row.developer_access_token }}
      </b-table-column>
      <b-table-column label="Created At" field="created_at">
        {{ props.row.created_at | moment("DD.MM.YYYY / k:mm:ss") }}
      </b-table-column>
      <b-table-column custom-key="actions" class="is-actions-cell">
        <div v-if="hasToken" class="buttons is-right" @click="resetToken" style="cursor: pointer">
          <b-icon icon="trash-can" size="is-small"/>
          <span>&nbsp; Reset</span>
        </div>
        <div v-else class="buttons is-right" @click="generateToken" style="cursor: pointer">
          <b-icon icon="key" size="is-small"/>
          <span>&nbsp; Generate</span>
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
          {
            'token_name': 'developer-access',
            'developer_access_token': '*******************************************',
            'created_at': ''
          }
        ],
        hasToken: false
      }
    },
    created() {
      this.getToken()
    },
    methods: {
      getToken() {
        axios.get('/tokens/getDeveloperToken')
          .then(r => {
            if(r.data && r.data.data) {
              this.hasToken = true
              this.data[0].created_at = r.data.data.created_at
            } else {
              this.data[0].developer_access_token = ''
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
      generateToken() {
        axios.get('/tokens/createDeveloperToken')
          .then(r => {
            this.hasToken = true
            this.data[0].developer_access_token = r.data['developer-access']
            this.data[0].created_at = r.data.data.created_at
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
      resetToken() {
        axios.get('/tokens/resetDeveloperToken')
            .then(r => {
              this.hasToken = true
              this.data[0].developer_access_token = r.data['developer-access']
              this.data[0].created_at = r.data.data.created_at
              this.$buefy.toast.open({
                message: 'A new API token has been generated. This is the only time you will see this token, make sure to copy it now.',
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
    }
  }
</script>