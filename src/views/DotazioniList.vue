<template>
  <div class="dotazioni-container">
    <div class="header">
      <h1>Gestione Dotazioni</h1>
      <button @click="showAddForm = true" class="btn-add">
        <i class="icon">+</i> Assegna Equipaggiamento
      </button>
    </div>

    <div class="filters">
      <select v-model="filterPersonale" @change="fetchDotazioni">
        <option value="">Tutti i Personale</option>
        <option v-for="p in personale" :key="p.personale_id" :value="p.personale_id">
          {{ p.cognome }} {{ p.nome }}
        </option>
      </select>
      <select v-model="filterEquip" @change="fetchDotazioni">
        <option value="">Tutto l'Equipaggiamento</option>
        <option v-for="e in equipaggiamento" :key="e.equip_id" :value="e.equip_id">
          {{ e.nome_modello }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="loading">Caricamento...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <table v-else class="dotazioni-table">
      <thead>
        <tr>
          <th>Operatore</th>
          <th>Equipaggiamento</th>
          <th>Tipo Uso</th>
          <th>Data Assegnazione</th>
          <th>Data Ritiro</th>
          <th>Stato</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="d in dotazioni" :key="d.dotazione_id">
          <td>{{ d.cognome }} {{ d.nome }}</td>
          <td>{{ d.nome_modello }} ({{ d.numero_seriale }})</td>
          <td>{{ d.tipo_uso }}</td>
          <td>{{ formatDate(d.data_assegnazione) }}</td>
          <td>{{ formatDate(d.data_ritiro) || '-' }}</td>
          <td>
            <span :class="['status', d.data_ritiro ? 'status-ritirato' : 'status-assegnato']">
              {{ d.data_ritiro ? 'Ritirato' : 'Assegnato' }}
            </span>
          </td>
          <td class="actions">
            <button @click="returnEquip(d.dotazione_id)" v-if="!d.data_ritiro" class="btn-small">Ritira</button>
            <button @click="deleteDotazione(d.dotazione_id)" class="btn-small btn-danger">Elimina</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Form -->
    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>Assegna Equipaggiamento</h2>
        <form @submit.prevent="saveDotazione">
          <select v-model="form.personale_id" required>
            <option value="">Seleziona Personale</option>
            <option v-for="p in personale" :key="p.personale_id" :value="p.personale_id">
              {{ p.cognome }} {{ p.nome }}
            </option>
          </select>
          <select v-model="form.equip_id" required>
            <option value="">Seleziona Equipaggiamento</option>
            <option v-for="e in equipaggiamento" :key="e.equip_id" :value="e.equip_id">
              {{ e.nome_modello }} ({{ e.numero_seriale }})
            </option>
          </select>
          <select v-model="form.tipo_uso" required>
            <option value="Operazione">Operazione</option>
            <option value="Addestramento">Addestramento</option>
            <option value="Manutenzione">Manutenzione</option>
            <option value="Custodia">Custodia</option>
          </select>
          <input v-model="form.data_assegnazione" type="date" required>
          <textarea v-model="form.note" placeholder="Note"></textarea>
          
          <div class="modal-actions">
            <button type="submit" class="btn-primary">Assegna</button>
            <button type="button" @click="closeForm" class="btn-secondary">Annulla</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'DotazioniList',
  setup() {
    const dotazioni = ref([]);
    const personale = ref([]);
    const equipaggiamento = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const filterPersonale = ref('');
    const filterEquip = ref('');
    const showAddForm = ref(false);

    const form = ref({
      personale_id: '',
      equip_id: '',
      tipo_uso: 'Operazione',
      data_assegnazione: new Date().toISOString().split('T')[0],
      note: ''
    });

    const fetchDotazioni = async () => {
      try {
        loading.value = true;
        const res = await axios.get('/api/dotazioni', { params: { limit: 100 } });
        dotazioni.value = res.data.data || [];
        error.value = null;
      } catch (err) {
        error.value = err.response?.data?.error || 'Errore nel caricamento';
      } finally {
        loading.value = false;
      }
    };

    const fetchPersonale = async () => {
      try {
        const res = await axios.get('/api/personale', { params: { limit: 100 } });
        personale.value = res.data.data || [];
      } catch (err) {
        console.error('Errore caricamento personale', err);
      }
    };

    const fetchEquipaggiamento = async () => {
      try {
        const res = await axios.get('/api/equipaggiamento', { params: { limit: 100 } });
        equipaggiamento.value = res.data.data || [];
      } catch (err) {
        console.error('Errore caricamento equipaggiamento', err);
      }
    };

    const saveDotazione = async () => {
      try {
        await axios.post('/api/dotazioni', form.value);
        closeForm();
        fetchDotazioni();
      } catch (err) {
        error.value = 'Errore nel salvataggio: ' + (err.response?.data?.error || err.message);
      }
    };

    const returnEquip = async (id) => {
      if (confirm('Ritirare questo equipaggiamento?')) {
        try {
          const today = new Date().toISOString().split('T')[0];
          await axios.put(`/api/dotazioni/${id}`, { data_ritiro: today });
          fetchDotazioni();
        } catch (err) {
          error.value = 'Errore nel ritiro: ' + (err.response?.data?.error || err.message);
        }
      }
    };

    const deleteDotazione = async (id) => {
      if (confirm('Sei sicuro di voler eliminare questa assegnazione?')) {
        try {
          await axios.delete(`/api/dotazioni/${id}`);
          fetchDotazioni();
        } catch (err) {
          error.value = 'Errore nella eliminazione: ' + (err.response?.data?.error || err.message);
        }
      }
    };

    const closeForm = () => {
      showAddForm.value = false;
      form.value = {
        personale_id: '',
        equip_id: '',
        tipo_uso: 'Operazione',
        data_assegnazione: new Date().toISOString().split('T')[0],
        note: ''
      };
    };

    const formatDate = (date) => {
      if (!date) return null;
      return new Date(date).toLocaleDateString('it-IT');
    };

    onMounted(() => {
      fetchPersonale();
      fetchEquipaggiamento();
      fetchDotazioni();
    });

    return {
      dotazioni,
      personale,
      equipaggiamento,
      loading,
      error,
      filterPersonale,
      filterEquip,
      showAddForm,
      form,
      saveDotazione,
      returnEquip,
      deleteDotazione,
      closeForm,
      fetchDotazioni,
      formatDate
    };
  }
};
</script>

<style scoped>
.dotazioni-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h1 {
  margin: 0;
  color: #1a1a1a;
}

.btn-add {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
}

.btn-add:hover {
  background-color: #0056b3;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filters select {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.375rem;
}

.dotazioni-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dotazioni-table th {
  background-color: #f8f9fa;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
}

.dotazioni-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.dotazioni-table tbody tr:hover {
  background-color: #f9f9f9;
}

.status {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  font-weight: 500;
  font-size: 0.875rem;
}

.status-assegnato {
  background-color: #cce5ff;
  color: #004085;
}

.status-ritirato {
  background-color: #e2e3e5;
  color: #383d41;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-small {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-small:hover {
  background-color: #5a6268;
}

.btn-danger {
  background-color: #dc3545;
}

.btn-danger:hover {
  background-color: #c82333;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 2rem;
  border-radius: 0.5rem;
  max-width: 500px;
  width: 90%;
}

.modal-content h2 {
  margin-top: 0;
  margin-bottom: 1rem;
}

.modal-content form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modal-content input,
.modal-content select,
.modal-content textarea {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.375rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
  color: white;
}

.btn-primary {
  background-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.loading,
.error {
  padding: 1rem;
  border-radius: 0.375rem;
  text-align: center;
}

.loading {
  background-color: #cfe2ff;
  color: #084298;
}

.error {
  background-color: #f8d7da;
  color: #721c24;
}
</style>
