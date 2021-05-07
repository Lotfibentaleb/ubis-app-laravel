<template>
  <div>
    <title-bar :title-stack="titleStack"/>
    <hero-bar>
      {{ heroTitle }}
      <router-link slot="right" to="/users/index" class="button">
        {{$t('createUserPage.heroBar.goto')}}
      </router-link>
    </hero-bar>
    <section class="section is-main-section">
      <tiles>
        <card-component :title="formCardTitle" icon="account-edit" class="tile is-child">
          <form @submit.prevent="submit">
            <template v-if="id">
              <b-field label="ID" horizontal>
                <b-input :value="id" custom-class="is-static" readonly />
              </b-field>
              <hr>
            </template>
            <b-field :label="$t('createUserPage.card.name')" :message="$t('createUserPage.card.nameMessage')" horizontal>
              <b-input placeholder="e.g. John Doe" v-model="form.name" required />
            </b-field>
            <b-field :label="$t('createUserPage.card.email')" :message="$t('createUserPage.card.emailMessage')" horizontal>
              <b-input placeholder="e.g. user@test.com" v-model="form.email" required />
            </b-field>
            <b-field :label="$t('createUserPage.card.uuid')" :message="$t('createUserPage.card.uuidMessage')" horizontal>
              <b-input :placeholder="$t('createUserPage.card.uuidPlaceholder')" v-model="form.uuid" readonly/>
            </b-field>
            <b-field :label="$t('createUserPage.card.role')" :message="$t('createUserPage.card.roleMessage')" horizontal>
              <b-select v-model="form.role">
                <option value="0">{{$t('createUserPage.titleBar.main')}}</option>
                <option value="1">{{$t('createUserPage.titleBar.sub1')}}</option>
              </b-select>
            </b-field>
            <b-field v-if="id" :label="$t('createUserPage.card.createdAt')" horizontal>
              <b-datepicker
                @input="input"
                v-model="form.created_date"
                placeholder="Click to select..."
                icon="calendar-today">
              </b-datepicker>
            </b-field>
            <b-field v-if="!id" horizontal>
              <b-field :label="$t('createUserPage.card.password')" :message="$t('createUserPage.card.passwordMessage')">
                <b-input type="password" placeholder="" v-model="form.password" required />
              </b-field>
              <b-field :label="$t('createUserPage.card.confirmPassword')" :message="$t('createUserPage.card.confirmPasswordMessage')">
                <b-input type="password" placeholder="" v-model="form.confirm_password" required />
              </b-field>
            </b-field>
            <b-field horizontal>
              <b-button type="is-primary" :loading="isLoading" native-type="submit">{{$t('createUserPage.card.submitButton')}}</b-button>
            </b-field>
          </form>
        </card-component>
        <card-component v-if="isProfileExists" title="User Profile" icon="account" class="tile is-child">
          <hr>
          <b-field :label="$t('createUserPage.card.name')">
            <b-input :value="item.name" custom-class="is-static" readonly/>
          </b-field>
          <b-field :label="$t('createUserPage.card.email')">
            <b-input :value="item.email" custom-class="is-static" readonly/>
          </b-field>
          <b-field :label="$t('createUserPage.card.uuid')">
            <b-input :value="item.uuid" custom-class="is-static" readonly/>
          </b-field>
          <b-field :label="$t('createUserPage.card.role')">
            <b-input :value="showRole" custom-class="is-static" readonly/>
          </b-field>
          <b-field :label="$t('createUserPage.card.createdAt')">
            <b-input :value="item.created" custom-class="is-static" readonly/>
          </b-field>
          <hr>
        </card-component>
      </tiles>
    </section>
  </div>
</template>

<script>
import clone from 'lodash/clone'
import TitleBar from '@/components/TitleBar'
import HeroBar from '@/components/HeroBar'
import Tiles from '@/components/Tiles'
import CardComponent from '@/components/CardComponent'
import FilePicker from '@/components/FilePicker'
import UserAvatar from '@/components/UserAvatar'
import Notification from '@/components/Notification'
import BField from "buefy/src/components/field/Field";

export default {
  name: 'ClientForm',
  components: {BField, UserAvatar, FilePicker, CardComponent, Tiles, HeroBar, TitleBar, Notification },
  props: {
    id: {
      default: null
    }
  },
  data () {
    return {
      isLoading: false,
      item: null,
      form: this.getClearFormObject(),
      createdReadable: null,
    }
  },
  computed: {
    titleStack () {
      let lastCrumb

      if (this.isProfileExists) {
        lastCrumb = this.form.name
      } else {
        lastCrumb = this.$t('createUserPage.titleBar.sub2')
      }

      return [
        this.$t('createUserPage.titleBar.main'),
        this.$t('createUserPage.titleBar.sub1'),
        lastCrumb
      ]
    },
    heroTitle () {
      if (this.isProfileExists) {
        return this.form.name
      } else {
        return this.$t('createUserPage.heroBar.title')
      }
    },
    formCardTitle () {
      if (this.isProfileExists) {
        return 'Edit User'
      } else {
        return this.$t('createUserPage.card.title')
      }
    },
    isProfileExists () {
      return !!this.item
    },
    showRole () {
      if (this.item.role == 0) {
          return 'Admin'
      } else {
          return 'User'
      }
    }
  },
  created () {
    this.getData()
  },
  methods: {
    getClearFormObject () {
      return {
        id: null,
        name: null,
        email: null,
        uuid: null,
        created_date: new Date(),
        created_mm_dd_yyyy: null,
        progress: 0,
        avatar: null,
        avatar_filename: null,
        file_id: null,
        role: 1,
        password: '',
        confirm_password: ''
      }
    },
    getData () {
      if (this.id) {
        axios
          .get(`/users/${this.id}`)
          .then(r => {
            this.form = r.data.data
            this.item = clone(r.data.data)

            this.form.created_date = new Date(r.data.data.created_mm_dd_yyyy)
          })
          .catch(e => {
            this.item = null

            this.$buefy.toast.open({
              message: `Error: ${e.message}`,
              type: 'is-danger',
              queue: false
            })
          })
      }
    },
    fileIdUpdated (fileId) {
      this.form.file_id = fileId
      this.form.avatar_filename = null
    },
    input (v) {
      //this.createdReadable = moment(v).format('MMM D, Y').toString()
    },
    submit () {
      this.isLoading = true
      let method = 'post'
      let url = '/users/create'

      if (this.id) {
        method = 'patch'
        url = `/users/${this.id}`
      } else {
          if (this.form.password != this.form.confirm_password) {
              this.form.password = ''
              this.form.confirm_password = ''
              this.$buefy.snackbar.open({
                  type: 'is-danger',
                  message: 'do not match password and confirm password',
                  queue: false
              })
              this.isLoading = false
              return
          }
      }

      axios({
        method,
        url,
        data: this.form
      }).then(r => {
        this.isLoading = false

        if (!this.id && r.data.data.id) {
          this.$router.push({name: 'users.edit', params: {id: r.data.data.id}})

          this.$buefy.snackbar.open({
            message: 'Created',
            queue: false
          })
        } else {
          this.item = r.data.data

          this.$buefy.snackbar.open({
            message: 'Updated',
            queue: false
          })
        }
      }).catch(e => {
        this.isLoading = false

        this.$buefy.toast.open({
          message: `Error: ${e.message}`,
          type: 'is-danger',
          queue: false
        })
      })
    }
  },
  watch: {
    id (newValue) {
      this.form = this.getClearFormObject()
      this.item = null

      if (newValue) {
        this.getData()
      }
    }
  }
}
</script>
