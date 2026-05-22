<template>
  <div class="dashboard-capo">
    <header class="dashboard-header">
      <h1>⚔️ Comando Armeria</h1>
      <span class="role-label">Capo di Stato Maggiore</span>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3>Personale Totale</h3>
          <p class="stat-number">{{ totalPersonale }}</p>
          <span class="stat-label">Operatori Attivi</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🎖️</div>
        <div class="stat-content">
          <h3>Squadre</h3>
          <p class="stat-number">{{ totalSquadre }}</p>
          <span class="stat-label">Squadre Operative</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🔫</div>
        <div class="stat-content">
          <h3>Equipaggiamento</h3>
          <p class="stat-number">{{ totalEquipaggiamento }}</p>
          <span class="stat-label">Articoli in Inventario</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-content">
          <h3>Assegnazioni Attive</h3>
          <p class="stat-number">{{ totalAssignazioni }}</p>
          <span class="stat-label">Dotazioni in Corso</span>
        </div>
      </div>
    </div>

    <div class="reports-grid">
      <div class="report-section">
        <h2>Stato Personale</h2>
        <div class="status-breakdown">
          <div class="status-item" v-for="(count, status) in personaleByStatus" :key="status">
            <span class="status-label">{{ status }}</span>
            <span class="status-count">{{ count }}</span>
          </div>
        </div>
      </div>

      <div class="report-section">
        <h2>Disponibilità Equipaggiamento</h2>
        <div class="status-breakdown">
          <div class="status-item" v-for="(count, status) in equipByStatus" :key="status">
            <span class="status-label">{{ status }}</span>
            <span class="status-count">{{ count }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="quick-actions">
      <h2>Azioni Rapide</h2>
      <div class="action-buttons">
        <button @click="$router.push('/personale')" class="action-btn">
          <span class="btn-icon">👤</span> Gestisci Personale
        </button>
        <button @click="$router.push('/squadre')" class="action-btn">
          <span class="btn-icon">🎖️</span> Gestisci Squadre
        </button>
        <button @click="$router.push('/equipaggiamento')" class="action-btn">
          <span class="btn-icon">🔫</span> Catalogo Armeria
        </button>
        <button @click="$router.push('/dotazioni')" class="action-btn">
          <span class="btn-icon">📦</span> Assegnazioni
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'DashboardCapoSM',
  setup() {
    const totalPersonale = ref(0);
    const totalSquadre = ref(0);
    const totalEquipaggiamento = ref(0);
    const totalAssignazioni = ref(0);
    const personaleByStatus = ref({});
    const equipByStatus = ref({});

    const fetchStats = async () => {
      try {
        const [pRes, sRes, eRes, dRes] = await Promise.all([
          axios.get('/api/personale?limit=1'),
          axios.get('/api/squadre?limit=1'),
          axios.get('/api/equipaggiamento?limit=1'),
          axios.get('/api/dotazioni?limit=1')
        ]);

        totalPersonale.value = pRes.data.pagination?.total || 0;
        totalSquadre.value = sRes.data.pagination?.total || 0;
        totalEquipaggiamento.value = eRes.data.pagination?.total || 0;
        totalAssignazioni.value = dRes.data.pagination?.total || 0;

        // Calculate status breakdown
        const personale = pRes.data.data || [];
        const equip = eRes.data.data || [];

        personaleByStatus.value = {
          'Attivo': personale.filter(p => p.stato_servizio === 'Attivo').length,
          'Congedato': personale.filter(p => p.stato_servizio === 'Congedato').length,
          'Infortunato': personale.filter(p => p.stato_servizio === 'Infortunato').length,
          'Sospeso': personale.filter(p => p.stato_servizio === 'Sospeso').length
        };

        equipByStatus.value = {
          'Disponibile': equip.filter(e => e.stato === 'Disponibile').length,
          'Assegnato': equip.filter(e => e.stato === 'Assegnato').length,
          'Manutenzione': equip.filter(e => e.stato === 'Manutenzione').length,
          'Ritirato': equip.filter(e => e.stato === 'Ritirato').length
        };
      } catch (err) {
        console.error('Errore caricamento stats', err);
      }
    };

    onMounted(() => {
      fetchStats();
    });

    return {
      totalPersonale,
      totalSquadre,
      totalEquipaggiamento,
      totalAssignazioni,
      personaleByStatus,
      equipByStatus
    };
  }
};
</script>

<style scoped>
.dashboard-capo {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.dashboard-header {
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
  color: white;
  padding: 2rem;
  border-radius: 1rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dashboard-header h1 {
  margin: 0;
  font-size: 2rem;
}

.role-label {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  font-size: 2rem;
  display: flex;
  align-items: center;
}

.stat-content {
  flex: 1;
}

.stat-content h3 {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
  font-weight: 600;
  text-transform: uppercase;
}

.stat-number {
  margin: 0.5rem 0;
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
}

.stat-label {
  font-size: 0.75rem;
  color: #999;
}

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.report-section {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.report-section h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.25rem;
  color: #1a1a1a;
}

.status-breakdown {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
}

.status-label {
  font-weight: 600;
  color: #495057;
}

.status-count {
  background: #2563eb;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 0.25rem;
  font-weight: 700;
  min-width: 2.5rem;
  text-align: center;
}

.quick-actions {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.quick-actions h2 {
  margin: 0 0 1.5rem 0;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-btn {
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
  color: white;
  border: none;
  padding: 1.25rem;
  border-radius: 0.75rem;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-icon {
  font-size: 1.75rem;
}
</style>
