import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import LoginPage from '../views/LoginPage.vue'
import Dashboard from '../views/Dashboard.vue'
import PersonaleList from '../views/PersonaleList.vue'
import SquadreList from '../views/SquadreList.vue'
import EquipaggiamentoList from '../views/EquipaggiamentoList.vue'
import DotazioniList from '../views/DotazioniList.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/personale',
    name: 'Personale',
    component: PersonaleList,
    meta: { requiresAuth: true }
  },
  {
    path: '/squadre',
    name: 'Squadre',
    component: SquadreList,
    meta: { requiresAuth: true }
  },
  {
    path: '/equipaggiamento',
    name: 'Equipaggiamento',
    component: EquipaggiamentoList,
    meta: { requiresAuth: true }
  },
  {
    path: '/dotazioni',
    name: 'Dotazioni',
    component: DotazioniList,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Se la rotta richiede autenticazione
  if (to.meta.requiresAuth) {
    // Se non abbiamo token, vai a login
    if (!authStore.token) {
      next('/login')
      return
    }

    // Se abbiamo token ma non l'utente, valida il token
    if (!authStore.user) {
      const isValid = await authStore.validateToken()
      if (!isValid) {
        next('/login')
        return
      }
    }
  }

  // Se sei autenticato e vai a login, vai alla home
  if (to.path === '/login' && authStore.isAuthenticated) {
    next('/')
    return
  }

  next()
})

export default router

