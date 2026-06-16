import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import AuctionListView from '../views/AuctionListView.vue'
import AuctionDetailView from '../views/AuctionDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      name: 'auctions',
      component: AuctionListView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/auction/:id',
      name: 'auction-detail',
      component: AuctionDetailView
    }
  ]
})

export default router