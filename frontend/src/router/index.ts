import { createRouter, createWebHistory } from 'vue-router'
import AdGeneratorView from '../views/AdGeneratorView.vue'
import DashboardView from '../views/DashboardView.vue'

const routes = [
  {
    path: '/generator',
    name: 'AdGenerator',
    component: AdGeneratorView,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
  },
  {
    path: '/',
    redirect: '/generator',
  },
]

const router = createRouter({
  // Pass Vite's BASE_URL (which resolves to '/app/') to the history manager
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router