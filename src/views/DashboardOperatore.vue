<template>
  <div class="dashboard-operatore">
    <header class="dashboard-header">
      <h1>🎖️ Il Mio Equipaggiamento</h1>
      <div class="operator-info">
        <span class="role-label">Operatore</span>
        <span class="operator-rank" v-if="currentUser">{{ currentUser.grado }} - {{ currentUser.cognome }}</span>
      </div>
    </header>

    <div class="squad-info" v-if="squadraInfo">
      <h3>Squadra di Appartenenza</h3>
      <div class="squad-card">
        <div class="squad-icon">🎖️</div>
        <div class="squad-details">
          <h4>{{ squadraInfo.nome_squadra }}</h4>
          <p><strong>Stato:</strong> <span :class="['status', 'status-' + squadraInfo.stato.toLowerCase()]">{{ squadraInfo.stato }}</span></p>
          <p><strong>Personale:</strong> {{ squadraInfo.num_personale }} operatori</p>
        </div>
      </div>
    </div>

    <div class="equipment-section">
      <h2>Equipaggiamento Assegnato</h2>
      <div v-if="myEquipment.length === 0" class="empty-state">
        <p>Non hai equipaggiamento assegnato al momento</p>
      </div>
      <div v-else class="equipment-grid">
        <div class="equipment-card" v-for="equip in myEquipment" :key="equip.dotazione_id">
          <div class="equip-header">
            <h3>{{ equip.nome_modello }}</h3>
            <span :class="['status-badge', equip.data_ritiro ? 'status-returned' : 'status-active']">
              {{ equip.data_ritiro ? 'Ritirato' : 'Attivo' }}
            </span>
          </div>
          <div class="equip-details">
            <p><strong>Seriale:</strong> {{ equip.numero_seriale }}</p>
            <p><strong>Categoria:</strong> {{ equip.nome_categoria }}</p>
            <p><strong>Tipo Uso:</strong> <span class="badge" :class="'badge-' + equip.tipo_uso.toLowerCase()">{{ equip.tipo_uso }}</span></p>
            <p><strong>Assegnato:</strong> {{ formatDate(equip.data_assegnazione) }}</p>
            <p v-if="equip.data_ritiro"><strong>Ritirato:</strong> {{ formatDate(equip.data_ritiro) }}</p>
            <p v-if="equip.note"><strong>Note:</strong> {{ equip.note }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="team-equipment" v-if="teamMembers.length > 0">
      <h2>Equipaggiamento della Squadra</h2>
      <table class="team-table">
        <thead>
          <tr>
            <th>Operatore</th>
            <th>Equipaggiamento</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="member in teamMembers" :key="member.dotazione_id" :class="{ 'is-self': member.personale_id === currentUser?.personale_id }">
            <td>{{ member.cognome }} {{ member.nome }}</td>
            <td>{{ member.nome_modello }}</td>
            <td>{{ member.tipo_uso }}</td>
            <td>
              <span :class="['badge', member.data_ritiro ? 'badge-returned' : 'badge-active']">
                {{ member.data_ritiro ? 'Ritirato' : 'Attivo' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="quick-info">
      <h2>Informazioni Utili</h2>
      <div class="info-grid">
        <div class="info-card">
          <h4>📋 Regole di Utilizzo</h4>
          <ul>
            <li>Conservare l'equipaggiamento in perfette condizioni</li>
            <li>Segnalare danni immediatamente all'Armaiolo</li>
            <li>Non prestare equipaggiamento ad altri</li>
            <li>Restituire al ritiro della missione</li>
          </ul>
        </div>
        <div class="info-card">
          <h4>📞 Contatti Importanti</h4>
          <ul>
            <li><strong>Armaiolo:</strong> Armeria - Sezione Equipaggiamento</li>
            <li><strong>Capo Squadra:</strong> Disponibile per problemi operativi</li>
            <li><strong>Emergenza:</strong> Contattare comando immediato</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

export default {
  name: 'DashboardOperatore',
  setup() {
    const authStore = useAuthStore();
    const currentUser = ref(null);
    const myEquipment = ref([]);
    const teamMembers = ref([]);
    const squadraInfo = ref(null);
    const loading = ref(true);

    const formatDate = (date) => {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    const fetchMyData = async () => {
      try {
        // Get current user info
        if (authStore.user) {
          currentUser.value = authStore.user;

          // Get my assignments
          const myAssignRes = await axios.get(`/api/dotazioni?personale_id=${authStore.user.personale_id}&limit=100`);
          myEquipment.value = myAssignRes.data.data || [];

          // Get squad info
          if (authStore.user.squadra_id) {
            try {
              const squadRes = await axios.get(`/api/squadre/${authStore.user.squadra_id}`);
              squadraInfo.value = squadRes.data.data;

              // Get all squad members' equipment
              const squadEqRes = await axios.get(`/api/dotazioni?limit=200`);
              teamMembers.value = (squadEqRes.data.data || []).filter(e =>
                // Filter to only show other team members' current/active assignments
                e.personale_id !== authStore.user.personale_id && !e.data_ritiro
              );
            } catch (err) {
              console.error('Errore caricamento squadra', err);
            }
          }
        }
      } catch (err) {
        console.error('Errore caricamento dati operatore', err);
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchMyData();
    });

    return {
      currentUser,
      myEquipment,
      teamMembers,
      squadraInfo,
      loading,
      formatDate
    };
  }
};
</script>

<style scoped>
.dashboard-operatore {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.dashboard-header {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

.operator-info {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-top: 1rem;
}

.role-label {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.operator-rank {
  display: inline-block;
  background: rgba(255, 255, 255, 0.3);
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.squad-info {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.squad-info h3 {
  margin: 0 0 1rem 0;
}

.squad-card {
  background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
  border: 1px solid #fbbf24;
  border-radius: 0.5rem;
  padding: 1.5rem;
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
}

.squad-icon {
  font-size: 2rem;
}

.squad-details h4 {
  margin: 0 0 0.5rem 0;
  color: #92400e;
}

.squad-details p {
  margin: 0.25rem 0;
  color: #b45309;
  font-size: 0.875rem;
}

.status {
  display: inline-block;
  padding: 0.2rem 0.5rem;
  border-radius: 0.25rem;
  font-weight: 600;
  font-size: 0.75rem;
  background: rgba(255, 255, 255, 0.5);
}

.equipment-section {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.equipment-section h2 {
  margin: 0 0 1.5rem 0;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #9ca3af;
}

.equipment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.equipment-card {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  border-left: 4px solid #0284c7;
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.equip-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.equip-header h3 {
  margin: 0;
  color: #0c4a6e;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-active {
  background: #22c55e;
  color: white;
}

.status-returned {
  background: #ef4444;
  color: white;
}

.equip-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.equip-details p {
  margin: 0;
  color: #0c4a6e;
}

.badge {
  display: inline-block;
  padding: 0.2rem 0.5rem;
  border-radius: 0.25rem;
  font-weight: 600;
  font-size: 0.75rem;
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

.badge-active {
  background: #dcfce7;
  color: #166534;
}

.badge-returned {
  background: #fee2e2;
  color: #991b1b;
}

.team-equipment {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.team-equipment h2 {
  margin: 0 0 1.5rem 0;
}

.team-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.team-table th {
  background: #f3f4f6;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.team-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.team-table tbody tr:hover {
  background: #f9fafb;
}

.team-table tbody tr.is-self {
  background: #fef3c7;
  font-weight: 600;
}

.quick-info {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.quick-info h2 {
  margin: 0 0 1.5rem 0;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.info-card {
  background: #f9fafb;
  border-left: 4px solid #3b82f6;
  border-radius: 0.375rem;
  padding: 1rem;
}

.info-card h4 {
  margin: 0 0 0.75rem 0;
  color: #1f2937;
}

.info-card ul {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.info-card li {
  margin: 0.25rem 0;
  color: #6b7280;
  font-size: 0.875rem;
}
</style>
