<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <div class="header-content">
        <h1>⚔️ Armory Inventory</h1>
        <div class="user-info">
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

    <main class="dashboard-content">
      <div class="welcome-section">
        <h2>Benvenuto, {{ authStore.user?.nome }}!</h2>
        <p>Seleziona un modulo dal menu per iniziare</p>
      </div>

      <div class="modules-grid">
        <div class="module-card" v-if="canAccess('personale')">
          <div class="module-icon">👤</div>
          <h3>Personale</h3>
          <p>Gestisci i membri del team</p>
          <button class="module-btn">Apri</button>
        </div>

        <div class="module-card" v-if="canAccess('squadre')">
          <div class="module-icon">🎖️</div>
          <h3>Squadre</h3>
          <p>Organizza le squadre operative</p>
          <button class="module-btn">Apri</button>
        </div>

        <div class="module-card" v-if="canAccess('equipaggiamento')">
          <div class="module-icon">🔫</div>
          <h3>Equipaggiamento</h3>
          <p>Catalogo armeria e attrezzature</p>
          <button class="module-btn">Apri</button>
        </div>

        <div class="module-card" v-if="canAccess('dotazioni')">
          <div class="module-icon">📦</div>
          <h3>Dotazioni</h3>
          <p>Assegnazioni equipaggiamento</p>
          <button class="module-btn">Apri</button>
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
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const apiStatus = ref('✓ Online')
const dbStatus = ref('✓ Connected')

onMounted(async () => {
  // Validate token on mount
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
.dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.dashboard-header {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  color: white;
  padding: 20px 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dashboard-header h1 {
  margin: 0;
  font-size: 24px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-name {
  display: flex;
  align-items: center;
  gap: 10px;
}

.role-badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.role-admin {
  background: #dc2626;
}

.role-capo_sm {
  background: #2563eb;
}

.role-armaiolo {
  background: #059669;
}

.role-ufficiale {
  background: #7c3aed;
}

.role-operatore {
  background: #f59e0b;
}

.logout-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.welcome-section {
  margin-bottom: 40px;
}

.welcome-section h2 {
  margin: 0;
  color: #1a1a2e;
  font-size: 28px;
}

.welcome-section p {
  margin: 10px 0 0 0;
  color: #666;
}

.modules-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.module-card {
  background: white;
  border-radius: 12px;
  padding: 30px 20px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.module-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.module-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

.module-card h3 {
  margin: 15px 0;
  color: #1a1a2e;
}

.module-card p {
  margin: 0 0 20px 0;
  color: #666;
  font-size: 14px;
}

.module-btn {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.module-btn:hover {
  transform: scale(1.05);
}

.system-info {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.system-info h3 {
  margin: 0 0 20px 0;
  color: #1a1a2e;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 15px;
  background: #f5f7fa;
  border-radius: 8px;
}

.info-label {
  color: #666;
  font-weight: 600;
}

.info-value {
  color: #059669;
  font-weight: 600;
}
</style>
