<template>
  <div class="dashboard-armaiolo">
    <header class="dashboard-header">
      <h1>🔫 Gestione Armeria</h1>
      <span class="role-label">Armaiolo</span>
    </header>

    <div class="alert-section" v-if="lowStockItems.length > 0">
      <h3>⚠️ Avviso: Equipaggiamento in Esaurimento</h3>
      <div class="alert-items">
        <div class="alert-item" v-for="item in lowStockItems" :key="item.equip_id">
          <strong>{{ item.nome_modello }}</strong> - Disponibili: {{ item.quantita }}
        </div>
      </div>
    </div>

    <div class="inventory-stats">
      <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-content">
          <h3>Articoli Inventario</h3>
          <p class="stat-number">{{ totalEquipaggiamento }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">✓</div>
        <div class="stat-content">
          <h3>Disponibili</h3>
          <p class="stat-number">{{ disponibili }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-content">
          <h3>Assegnati</h3>
          <p class="stat-number">{{ assegnati }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🔧</div>
        <div class="stat-content">
          <h3>In Manutenzione</h3>
          <p class="stat-number">{{ manutenzione }}</p>
        </div>
      </div>
    </div>

    <div class="categories-section">
      <h2>Equipaggiamento per Categoria</h2>
      <div class="category-grid">
        <div class="category-card" v-for="cat in categorieWithCounts" :key="cat.category">
          <h3>{{ cat.category }}</h3>
          <p class="category-count">{{ cat.count }} articoli</p>
          <button @click="viewCategory(cat.category)" class="category-btn">Visualizza</button>
        </div>
      </div>
    </div>

    <div class="actions-section">
      <h2>Gestione Inventario</h2>
      <div class="action-buttons">
        <button @click="$router.push('/equipaggiamento')" class="action-btn primary">
          <span>📋</span> Catalogo Completo
        </button>
        <button @click="$router.push('/dotazioni')" class="action-btn secondary">
          <span>📦</span> Assegnazioni Attive
        </button>
        <button @click="$router.push('/personale')" class="action-btn secondary">
          <span>👥</span> Operatori
        </button>
      </div>
    </div>

    <div class="recent-assignments" v-if="recentAssignments.length > 0">
      <h2>Assegnazioni Recenti</h2>
      <table class="assignments-table">
        <thead>
          <tr>
            <th>Operatore</th>
            <th>Equipaggiamento</th>
            <th>Data</th>
            <th>Tipo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="assignment in recentAssignments.slice(0, 5)" :key="assignment.dotazione_id">
            <td>{{ assignment.cognome }} {{ assignment.nome }}</td>
            <td>{{ assignment.nome_modello }}</td>
            <td>{{ formatDate(assignment.data_assegnazione) }}</td>
            <td>
              <span :class="['badge', 'badge-' + assignment.tipo_uso.toLowerCase()]">
                {{ assignment.tipo_uso }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'DashboardArmaiolo',
  setup() {
    const equipaggiamento = ref([]);
    const dotazioni = ref([]);
    const loading = ref(true);

    const totalEquipaggiamento = computed(() => equipaggiamento.value.length);
    const disponibili = computed(() => 
      equipaggiamento.value.filter(e => e.stato === 'Disponibile').length
    );
    const assegnati = computed(() =>
      equipaggiamento.value.filter(e => e.stato === 'Assegnato').length
    );
    const manutenzione = computed(() =>
      equipaggiamento.value.filter(e => e.stato === 'Manutenzione').length
    );

    const lowStockItems = computed(() =>
      equipaggiamento.value.filter(e => e.quantita <= 2)
    );

    const categorieWithCounts = computed(() => {
      const categories = {};
      equipaggiamento.value.forEach(item => {
        const cat = item.nome_categoria || 'Non categorizzato';
        categories[cat] = (categories[cat] || 0) + 1;
      });
      return Object.entries(categories).map(([category, count]) => ({
        category,
        count
      }));
    });

    const recentAssignments = computed(() =>
      dotazioni.value.slice(0, 10)
    );

    const fetchEquipaggiamento = async () => {
      try {
        const res = await axios.get('/api/equipaggiamento?limit=500');
        equipaggiamento.value = res.data.data || [];
      } catch (err) {
        console.error('Errore caricamento equipaggiamento', err);
      }
    };

    const fetchDotazioni = async () => {
      try {
        const res = await axios.get('/api/dotazioni?limit=500');
        dotazioni.value = res.data.data || [];
      } catch (err) {
        console.error('Errore caricamento dotazioni', err);
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    const viewCategory = (category) => {
      // TODO: Implement category filter in equipaggiamento page
      // For now, just redirect to equipaggiamento list
      window.location.href = '/equipaggiamento';
    };

    onMounted(async () => {
      await Promise.all([fetchEquipaggiamento(), fetchDotazioni()]);
      loading.value = false;
    });

    return {
      equipaggiamento,
      dotazioni,
      loading,
      totalEquipaggiamento,
      disponibili,
      assegnati,
      manutenzione,
      lowStockItems,
      categorieWithCounts,
      recentAssignments,
      formatDate,
      viewCategory
    };
  }
};
</script>

<style scoped>
.dashboard-armaiolo {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.dashboard-header {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
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

.alert-section {
  background: #fed7aa;
  border-left: 4px solid #d97706;
  padding: 1.5rem;
  border-radius: 0.5rem;
  margin-bottom: 2rem;
}

.alert-section h3 {
  margin: 0 0 1rem 0;
  color: #92400e;
}

.alert-items {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.alert-item {
  background: rgba(255, 255, 255, 0.7);
  padding: 0.75rem;
  border-radius: 0.375rem;
  color: #92400e;
  font-size: 0.875rem;
}

.inventory-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
  align-items: flex-start;
}

.stat-icon {
  font-size: 2rem;
  min-width: 3rem;
  text-align: center;
}

.stat-content h3 {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
  font-weight: 600;
  text-transform: uppercase;
}

.stat-number {
  margin: 0.5rem 0 0 0;
  font-size: 1.75rem;
  font-weight: 700;
  color: #059669;
}

.categories-section {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.categories-section h2 {
  margin: 0 0 1.5rem 0;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
}

.category-card {
  background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
  border: 1px solid #a7f3d0;
  border-radius: 0.5rem;
  padding: 1rem;
  text-align: center;
}

.category-card h3 {
  margin: 0;
  font-size: 1rem;
  color: #065f46;
}

.category-count {
  margin: 0.5rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #059669;
}

.category-btn {
  background: #059669;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
  width: 100%;
  margin-top: 0.75rem;
}

.category-btn:hover {
  background: #047857;
}

.actions-section {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.actions-section h2 {
  margin: 0 0 1.5rem 0;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-btn {
  padding: 1rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
}

.action-btn.primary {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
}

.action-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.action-btn.secondary {
  background: #f3f4f6;
  color: #374151;
}

.action-btn.secondary:hover {
  background: #e5e7eb;
}

.action-btn span {
  font-size: 1.5rem;
}

.recent-assignments {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.recent-assignments h2 {
  margin: 0 0 1.5rem 0;
}

.assignments-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.assignments-table th {
  background: #f3f4f6;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.assignments-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.assignments-table tbody tr:hover {
  background: #f9fafb;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-standard {
  background: #dbeafe;
  color: #0c4a6e;
}

.badge-temporaneo {
  background: #fef3c7;
  color: #92400e;
}

.badge-addestramento {
  background: #ddd6fe;
  color: #4c1d95;
}
</style>
