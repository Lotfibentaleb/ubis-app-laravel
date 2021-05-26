<template>
  <div>
    <title-bar :title-stack="[$t('usersPage.titleBar.main'), $t('usersPage.titleBar.sub1')]"/>
    <hero-bar>
      {{$t('usersPage.heroBar.title')}}
      <router-link to="/users/new" class="button" slot="right">
        {{$t('usersPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <card-component class="has-table has-mobile-sort-spaced" :title="$t('usersPage.table.title')" icon="account-multiple">
        <modal-trash-box :is-active="isModalActive" :trash-subject="trashSubject" @confirm="trashConfirm" @cancel="trashCancel"/>
        <b-table
          :checked-rows.sync="checkedRows"
          :checkable="true"
          :loading="isLoading"
          :paginated="paginated"
          :per-page="perPage"
          :striped="true"
          :hoverable="true"
          default-sort="name"
          :data="clients">
          <template slot-scope="props">
            <b-table-column :label="$t('usersPage.table.name')" field="name" sortable>
              {{ props.row.name }}
            </b-table-column>
            <b-table-column :label="$t('usersPage.table.email')" field="email" sortable>
              {{ props.row.email }}
            </b-table-column>
            <b-table-column  :label="$t('usersPage.table.uuid')" field="uuid" sortable>
              {{ props.row.uuid }}
            </b-table-column>
            <b-table-column :label="$t('usersPage.table.role')" field="uuid" sortable>
              {{ props.row.role == 0 ? 'Admin' : 'User' }}
            </b-table-column>
            <b-table-column :label="$t('usersPage.table.createdAt')">
              <small class="has-text-grey is-abbr-like" :title="props.row.created">{{ props.row.created }}</small>
            </b-table-column>
            <b-table-column custom-key="actions" class="is-actions-cell">
              <div class="buttons is-right">
                <router-link :to="{name:'users.edit', params: {id: props.row.id}}" class="button is-small is-primary">
                  <b-icon icon="account-edit" size="is-small"/>
                </router-link>
                <button class="button is-small is-danger" type="button" @click.prevent="trashModal(props.row)" :disabled="!props.row.role">
                  <b-icon icon="trash-can" size="is-small"/>
                </button>
              </div>
            </b-table-column>
          </template>
          <section class="section" slot="empty">
            <div class="content has-text-grey has-text-centered">
              <template v-if="isLoading">
                <p>
                  <b-icon icon="dots-horizontal" size="is-large"/>
                </p>
                <p>Fetching data...</p>
              </template>
              <template v-else>
                <p>
                  <b-icon icon="emoticon-sad" size="is-large"/>
                </p>
                <p>Nothing's here&hellip;</p>
              </template>
            </div>
          </section>
        </b-table>
      </card-component>
    </section>
  </div>

</template>

<script>
import map from 'lodash/map'
import CardComponent from '@/components/CardComponent'
import ModalBox from '@/components/ModalBox'
import TitleBar from '@/components/TitleBar'
import HeroBar from '@/components/HeroBar'
import CardToolbar from '@/components/CardToolbar'
import ModalTrashBox from '@/components/ModalTrashBox'
export default {
  name: "ClientIndex",
  components: {ModalTrashBox, CardToolbar, HeroBar, TitleBar, ModalBox, CardComponent},
  data () {
    return {
      isModalActive: false,
      trashObject: null,
      clients: [],
      isLoading: false,
      paginated: false,
      perPage: 10,
      checkedRows: []
    }
  },
  computed: {
    trashSubject () {
      if (this.trashObject) {
        return this.trashObject.name
      }

      if (this.checkedRows.length) {
        return `${this.checkedRows.length} entries`
      }

      return null
    }
  },
  created () {
    this.getData()
  },
  methods: {
    getData () {
      this.isLoading = true
      axios
        .get('/users')
        .then(r => {
          this.isLoading = false
          if (r.data && r.data.data) {
            if (r.data.data.length > this.perPage) {
              this.paginated = true
            }
            this.clients = r.data.data
          }
        })
        .catch( err => {
          if (err.response){
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
          } else if(err.message) {
            this.isLoading = false
            this.$buefy.toast.open({
              message: `Error: ${err.message}`,
              type: 'is-danger',
              queue: false
            })
          }
        })
    },
    trashModal (trashObject = null) {
      if (trashObject || this.checkedRows.length) {
        this.trashObject = trashObject
        this.isModalActive = true
      }
    },
    trashConfirm () {
      let url
      let method
      let data = null

      this.isModalActive = false

      if (this.trashObject) {
        method = 'delete'
        url = `/users/${this.trashObject.id}/destroy`
      } else if (this.checkedRows.length) {
        method = 'post'
        url = '/users/  destroy'
        data = {
          ids: map(this.checkedRows, row => row.id)
        }
      }

      axios({
        method,
        url,
        data
      }).then( r => {
        this.getData()

        this.trashObject = null
        this.checkedRows = []

        this.$buefy.snackbar.open({
          message: `Deleted`,
          queue: false
        })
      }).catch( err => {
        this.$buefy.toast.open({
          message: `Error: ${err.message}`,
          type: 'is-danger',
          queue: false
        })
      })
    },
    trashCancel () {
      this.isModalActive = false
    }
  }
}
</script>
