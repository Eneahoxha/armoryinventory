<template>
  <div class="squadre-container">
    <div class="header">
      <h1>Gestione Squadre</h1>
      <button @click="showAddForm = true" class="btn-add">
        <i class="icon">+</i> Aggiungi Squadra
      </button>
    </div>

    <div v-if="loading" class="loading">Caricamento...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="squadre-grid">
      <div v-for="s in squadre" :key="s.squadra_id" class="squadra-card">
        <div class="card-header">
          <h3>{{ s.nome_squadra }}</h3>
          <span :class="['status', 'status-' + (s.stato || '').toLowerCase()]">
            {{ s.stato }}
          </span>
        </div>
        <div class="card-body">
          <p><strong>Personale:</strong> {{ s.num_personale }} operatori</p>
          <p><strong>Creata:</strong> {{ formatDate(s.created_at) }}</p>
        </div>
        <div class="card-actions">
          <button @click="editSquadra(s)" class="btn-small">Modifica</button>
          <button @click="deleteSquadra(s.squadra_id)" class="btn-small btn-danger">Elimina</button>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingId ? 'Modifica Squadra' : 'Aggiungi Squadra' }}</h2>
        <form @submit.prevent="saveSquadra">
          <input v-model="form.nome_squadra" type="text" placeholder="Nome Squadra" required>
          <select v-model="form.stato" required>
            <option value="Disponibile">Disponibile</option>
            <option value="In Missione">In Missione</option>
            <option value="Manutenzione">Manutenzione</option>
          </select>
          <textarea v-model="form.note" placeholder="Note"></textarea>
          
          <div class="modal-actions">
            <button type="submit" class="btn-primary">Salva</button>
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
  name: 'SquadreList',
  setup() {
    const squadre = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const showAddForm = ref(false);
    const editingId = ref(null);

    const form = ref({
      nome_squadra: '',
      stato: 'Disponibile',
      note: ''
    });

    const fetchSquadre = async () => {
      try {
        loading.value = true;
        const res = await axios.get('/api/squadre', { params: { limit: 100 } });
        squadre.value = res.data.data || [];
        error.value = null;
      } catch (err) {
        error.value = err.response?.data?.error || 'Errore nel caricamento';
      } finally {
        loading.value = false;
      }
    };

    const editSquadra = (s) => {
      editingId.value = s.squadra_id;
      form.value = { ...s };
      showAddForm.value = true;
    };

    const saveSquadra = async () => {
      try {
        if (editingId.value) {
          await axios.put(`/api/squadre/${editingId.value}`, form.value);
        } else {
          await axios.post('/api/squadre', form.value);
        }
        closeForm();
        fetchSquadre();
      } catch (err) {
        error.value = 'Errore nel salvataggio: ' + (err.response?.data?.error || err.message);
      }
    };

    const deleteSquadra = async (id) => {
      if (confirm('Sei sicuro di voler eliminare questa squadra?')) {
        try {
          await axios.delete(`/api/squadre/${id}`);
          fetchSquadre();
        } catch (err) {
          error.value = 'Errore nella eliminazione: ' + (err.response?.data?.error || err.message);
        }
      }
    };

    const closeForm = () => {
      showAddForm.value = false;
      editingId.value = null;
      form.value = { nome_squadra: '', stato: 'Disponibile', note: '' };
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('it-IT');
    };

    onMounted(() => {
      fetchSquadre();
    });

    return {
      squadre,
      loading,
      error,
      showAddForm,
      editingId,
      form,
      editSquadra,
      saveSquadra,
      deleteSquadra,
      closeForm,
      formatDate
    };
  }
};
</script>

<style scoped>
.squadre-container {
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

.squadre-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.squadra-card {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.squadra-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.card-header {
  background-color: #f8f9fa;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #dee2e6;
}

.card-header h3 {
  margin: 0;
  color: #1a1a1a;
}

.status {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  font-weight: 500;
  font-size: 0.875rem;
}

.status-disponibile {
  background-color: #d4edda;
  color: #155724;
}

.status-in\ missione {
  background-color: #cce5ff;
  color: #004085;
}

.status-manutenzione {
  background-color: #fff3cd;
  color: #856404;
}

.card-body {
  padding: 1rem;
}

.card-body p {
  margin: 0.5rem 0;
  color: #666;
}

.card-actions {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  gap: 0.5rem;
}

.btn-small {
  flex: 1;
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 0.5rem;
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
  font-size: 1rem;
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
