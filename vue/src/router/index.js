import { createRouter, createWebHistory } from 'vue-router'
import Main from '../views/Main.vue'
import LogIn from '../views/LogIn.vue'
import SignUp from '@/views/SignUp.vue'
import UserAccount from '@/views/UserAccount.vue'
import Admin from '@/views/Admin.vue'

const routes = [
  {
    path: '/',
    name: 'main',
    component: Main
  },
  {
    path: '/login',
    name: 'login',
    component: LogIn
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignUp
  },
  {
    path: '/user_account',
    name: 'user_account',
    component: UserAccount
  },
  {
    path: '/admin',
    name: 'admin',
    component: Admin
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
