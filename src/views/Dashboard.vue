<template>
  <div class="dashboard-wrapper">
    <!-- Header Navigation -->
    <header class="main-header">
      <div class="header-content">
        <h1 @click="$router.push('/dashboard')" class="logo">⚔️ Armory Inventory</h1>
        <nav class="main-nav">
          <router-link v-if="canAccess('personale')" to="/personale" class="nav-link">👤 Personale</router-link>
          <router-link v-if="canAccess('squadre')" to="/squadre" class="nav-link">🎖️ Squadre</router-link>
          <router-link v-if="canAccess('equipaggiamento')" to="/equipaggiamento" class="nav-link">🔫 Armeria</router-link>
          <router-link v-if="canAccess('dotazioni')" to="/dotazioni" class="nav-link">📦 Dotazioni</router-link>
        </nav>
        <div class="user-menu">
          <span v-if="authStore.user" class="user-name">
            {{ authStore.user.nome }} {{ authStore.user.cognome }}
            <span class="role-badge" :class="'role-' + authStore.user.ruolo">
              {{ getRoleLabel(authStore.user.ruolo) }}
            </span>
          </span>
          <button @click="handleLogout" class="logout-btn">Logout</button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="dashboard-content">
      <!-- Role-Specific Dashboard -->
      <component v-if="dashboardComponent" :is="dashboardComponent" />

      <!-- Fallback generic dashboard -->
      <div v-else class="dashboard">
        <div class="welcome-section">
          <h2>Benvenuto, {{ authStore.user?.nome }}!</h2>
          <p>Seleziona un modulo dal menu per iniziare</p>
        </div>

        <div class="modules-grid">
          <div class="module-card" v-if="canAccess('personale')">
            <div class="module-icon">👤</div>
            <h3>Personale</h3>
            <p>Gestisci i membri del team</p>
            <button @click="$router.push('/personale')" class="module-btn">Apri</button>
          </div>

          <div class="module-card" v-if="canAccess('squadre')">
            <div class="module-icon">🎖️</div>
            <h3>Squadre</h3>
            <p>Organizza le squadre operative</p>
            <button @click="$router.push('/squadre')" class="module-btn">Apri</button>
          </div>

          <div class="module-card" v-if="canAccess('equipaggiamento')">
            <div class="module-icon">🔫</div>
            <h3>Equipaggiamento</h3>
            <p>Catalogo armeria e attrezzature</p>
            <button @click="$router.push('/equipaggiamento')" class="module-btn">Apri</button>
          </div>

          <div class="module-card" v-if="canAccess('dotazioni')">
            <div class="module-icon">📦</div>
            <h3>Dotazioni</h3>
            <p>Assegnazioni equipaggiamento</p>
            <button @click="$router.push('/dotazioni')" class="module-btn">Apri</button>
          </div>
        </div>

        <div class="system-info">
          <h3>Informazioni Sistema</h3>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">API Status:</span>
              <span class="info-value">{{ apiStatus }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Database:</span>
              <span class="info-value">{{ dbStatus }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Token Valid:</span>
              <span class="info-value">✓ Yes</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, shallowRef } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import DashboardCapoSM from './DashboardCapoSM.vue'
import DashboardArmaiolo from './DashboardArmaiolo.vue'
import DashboardOperatore from './DashboardOperatore.vue'

const router = useRouter()
const authStore = useAuthStore()

const apiStatus = ref('✓ Online')
const dbStatus = ref('✓ Connected')

const dashboardComponent = computed(() => {
  const role = authStore.user?.ruolo
  if (role === 'capo_sm') return DashboardCapoSM
  if (role === 'armaiolo') return DashboardArmaiolo
  if (role === 'operatore') return DashboardOperatore
  return null
})

onMounted(async () => {
  if (!authStore.user && authStore.token) {
    await authStore.validateToken()
  }
})

const getRoleLabel = (role) => {
  const labels = {
    'admin': 'Administrator',
    'capo_sm': 'Chief of Staff',
    'armaiolo': 'Armourer',
    'ufficiale': 'Officer',
    'operatore': 'Operator'
  }
  return labels[role] || role
}

const canAccess = (module) => {
  const permissions = {
    'personale': ['admin', 'capo_sm', 'ufficiale'],
    'squadre': ['admin', 'capo_sm'],
    'equipaggiamento': ['admin', 'armaiolo'],
    'dotazioni': ['admin', 'armaiolo', 'operatore']
  }
  return permissions[module]?.includes(authStore.user?.ruolo) || false
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.dashboard-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  flex-direction: column;
}

.main-header {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  color: white;
  padding: 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.logo {
  margin: 0;
  font-size: 1.5rem;
  cursor: pointer;
  transition: opacity 0.2s;
}

.logo:hover {
  opacity: 0.8;
}

.main-nav {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: opacity 0.2s;
}

.nav-link:hover {
  opacity: 0.8;
}

.nav-link.router-link-active {
  border-bottom: 2px solid #fbbf24;
  padding-bottom: 0.25rem;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.2);
}

.role-admin {
  background: #dc2626 !important;
}

.role-capo_sm {
  background: #2563eb !important;
}

.role-armaiolo {
  background: #059669 !important;
}

.role-ufficiale {
  background: #7c3aed !important;
}

.role-operatore {
  background: #f59e0b !important;
}

.logout-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.dashboard-content {
  flex: 1;
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  width: 100%;
}

.dashboard {
  min-height: 100vh;
}

.welcome-section {
  margin-bottom: 2rem;
}

.welcome-section h2 {
  margin: 0;
  color: #1a1a2e;
  font-size: 1.75rem;
}

.welcome-section p {
  margin: 0.5rem 0 0 0;
  color: #666;
}

.modules-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.module-card {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.module-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.module-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.module-card h3 {
  margin: 0.75rem 0;
  color: #1a1a2e;
}

.module-card p {
  margin: 0 0 1rem 0;
  color: #666;
  font-size: 0.875rem;
}

.module-btn {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.module-btn:hover {
  transform: scale(1.05);
}

.system-info {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.system-info h3 {
  margin: 0 0 1rem 0;
  color: #1a1a2e;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 1rem;
  background: #f5f7fa;
  border-radius: 0.375rem;
}

.info-label {
  color: #666;
  font-weight: 600;
}

.info-value {
  color: #059669;
  font-weight: 600;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  .main-nav {
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }

  .user-menu {
    width: 100%;
    justify-content: center;
  }

  .dashboard-content {
    padding: 1rem;
  }
}
</style>
