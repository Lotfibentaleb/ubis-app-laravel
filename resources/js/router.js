import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'

Vue.use(Router)

export default new Router({
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/tables',
      name: 'tables',
      component: () => import('./views/Tables.vue')
    },
    {
      path: '/forms',
      name: 'forms',
      component: () => import('./views/Forms.vue')
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('./views/Profile.vue')
    },
    {
      path: '/users/index',
      name: 'users.index',
      component: () => import('./views/Clients/ClientsIndex.vue'),
    },
    {
      path: '/users/new',
      name: 'users.new',
      component: () => import('./views/Clients/ClientsForm.vue'),
    },
    {
      path: '/users/:id',
      name: 'users.edit',
      component: () => import('./views/Clients/ClientsForm.vue'),
      props: true
    },
    {
      path: '/products/register',
        name: 'products.register',
      component: () => import('./views/Products/BulkRegister.vue'),
    },
    {
      path: '/products/list',
      name: 'products.list',
      component: () => import('./views/Products/ProductsList.vue'),
    },
    {
      path: '/measurements/list',
      name: 'measurements.list',
      component: () => import('./views/Products/MeasurementsList.vue'),
    },
    {
      path: '/products/template',
      name: 'products.template',
      component: () => import('./views/Products/ProductionTemplate.vue'),
    },
    {
      path: '/products/new-template',
      name: 'products.new-template',
      component: () => import('./views/Products/ProductionTemplateForm.vue'),
    },
    {
      path: '/products/section/template',
      name: 'products.section_template',
      component: () => import('./views/Products/ProductionSectionTemplate.vue'),
    },
    {
      path: '/products/section/new-template',
      name: 'products.section_template.new',
      component: () => import('./views/Products/ProductionSectionTemplateForm.vue'),
    },
    {
      path: '/tokens',
      name: 'tokens.index',
      component: () => import('./views/Tokens/TokensIndex.vue'),
    },
  ],
  scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { x: 0, y: 0 }
    }
  }
})
