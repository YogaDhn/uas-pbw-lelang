import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import AuctionListView from '../views/AuctionListView.vue'
import AuctionDetailView from '../views/AuctionDetailView.vue'
import MyAuctionsView from "../views/MyAuctionsView.vue";
import RegisterView from '../views/RegisterView.vue'
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
  path: '/register',
  name: 'register',
  component: RegisterView
},
    {
  path: "/auctions/:id",
  name: "auction-detail",
  component: () => import("../views/AuctionDetailView.vue")
},

{
  path: "/auctions/create",
  component: () => import("../views/CreateAuctionView.vue")
},
{
  path: "/auctions/:id/edit",
  component: () =>
    import("../views/EditAuctionView.vue")
},
{
  path: "/my-auctions",
  component: MyAuctionsView
}
  ]
})

router.beforeEach((to, from, next) => {

  const token = localStorage.getItem("token");

  const publicPages = [
    "/login",
    "/register"
  ];

  if (
    !publicPages.includes(to.path) &&
    !token
  ) {
    next("/login");
    return;
  }

  next();
});

export default router