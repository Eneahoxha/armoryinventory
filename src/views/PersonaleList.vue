<template>
  <div class="personale-container">
    <div class="header">
      <h1>Gestione Personale</h1>
      <button @click="showAddForm = true" class="btn-add">
        <i class="icon">+</i> Aggiungi Personale
      </button>
    </div>

    <div class="filters">
      <input 
        v-model="search" 
        type="text" 
        placeholder="Cerca per nome o cognome..."
        @keyup="filterPersonale"
      >
      <select v-model="filterSquadra" @change="filterPersonale">
        <option value="">Tutte le Squadre</option>
        <option v-for="s in squadre" :key="s.squadra_id" :value="s.squadra_id">
          {{ s.nome_squadra }}
        </option>
      </select>
      <select v-model="filterStato" @change="filterPersonale">
        <option value="">Tutti gli Stati</option>
        <option value="Attivo">Attivo</option>
        <option value="Congedato">Congedato</option>
        <option value="Deceduto">Deceduto</option>
        <option value="Infortunato">Infortunato</option>
        <option value="Sospeso">Sospeso</option>
      </select>
    </div>

    <div v-if="loading" class="loading">Caricamento...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <table v-else class="personale-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Cognome</th>
          <th>Grado</th>
          <th>Squadra</th>
          <th>Stato</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in personale" :key="p.personale_id">
          <td>{{ p.nome }}</td>
          <td>{{ p.cognome }}</td>
          <td>{{ p.grado }}</td>
          <td>{{ p.nome_squadra || '-' }}</td>
          <td>
            <span :class="['status', 'status-' + (p.stato_servizio || '').toLowerCase()]">
              {{ p.stato_servizio }}
            </span>
          </td>
          <td class="actions">
            <button @click="editPersonale(p)" class="btn-small">Modifica</button>
            <button @click="deletePersonale(p.personale_id)" class="btn-small btn-danger">Elimina</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!loading && personale.length" class="pagination">
      <button @click="previousPage" :disabled="offset === 0">← Precedente</button>
      <span>Pagina {{ currentPage }} di {{ totalPages }}</span>
      <button @click="nextPage" :disabled="offset + limit >= total">Successiva →</button>
    </div>

    <!-- Modal Form -->
    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingId ? 'Modifica Personale' : 'Aggiungi Personale' }}</h2>
        <form @submit.prevent="savePersonale">
          <input v-model="form.nome" type="text" placeholder="Nome" required>
          <input v-model="form.cognome" type="text" placeholder="Cognome" required>
          <input v-model="form.grado" type="text" placeholder="Grado" required>
          <select v-model="form.squadra_id" required>
            <option value="">Seleziona Squadra</option>
            <option v-for="s in squadre" :key="s.squadra_id" :value="s.squadra_id">
              {{ s.nome_squadra }}
            </option>
          </select>
          <select v-model="form.sesso">
            <option value="Maschio">Maschio</option>
            <option value="Femmina">Femmina</option>
            <option value="Non specificato">Non specificato</option>
          </select>
          <select v-model="form.stato_servizio" required>
            <option value="Attivo">Attivo</option>
            <option value="Congedato">Congedato</option>
            <option value="Deceduto">Deceduto</option>
            <option value="Infortunato">Infortunato</option>
            <option value="Sospeso">Sospeso</option>
          </select>
          <input v-model="form.altezza" type="text" placeholder="Altezza (es. 5-11)">
          <input v-model="form.soprannome" type="text" placeholder="Soprannome">
          
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
  name: 'PersonaleList',
  setup() {
    const personale = ref([]);
    const squadre = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const search = ref('');
    const filterSquadra = ref('');
    const filterStato = ref('');
    const showAddForm = ref(false);
    const editingId = ref(null);
    
    const limit = 10;
    const offset = ref(0);
    const total = ref(0);

    const form = ref({
      nome: '',
      cognome: '',
      grado: '',
      squadra_id: '',
      sesso: 'Maschio',
      stato_servizio: 'Attivo',
      soprannome: '',
      altezza: ''
    });

    const fetchPersonale = async () => {
      try {
        loading.value = true;
        const res = await axios.get('/api/personale', {
          params: { limit: limit, offset: offset.value }
        });
        personale.value = res.data.data || [];
        total.value = res.data.pagination?.total || 0;
        error.value = null;
      } catch (err) {
        error.value = err.response?.data?.error || 'Errore nel caricamento personale';
        console.error(err);
      } finally {
        loading.value = false;
      }
    };

    const fetchSquadre = async () => {
      try {
        const res = await axios.get('/api/squadre', { params: { limit: 100 } });
        squadre.value = res.data.data || [];
      } catch (err) {
        console.error('Errore caricamento squadre', err);
      }
    };

    const filterPersonale = () => {
      offset.value = 0;
      fetchPersonale();
    };

    const editPersonale = (p) => {
      editingId.value = p.personale_id;
      form.value = { ...p };
      showAddForm.value = true;
    };

    const savePersonale = async () => {
      try {
        if (editingId.value) {
          await axios.put(`/api/personale/${editingId.value}`, form.value);
        } else {
          await axios.post('/api/personale', form.value);
        }
        closeForm();
        fetchPersonale();
      } catch (err) {
        error.value = 'Errore nel salvataggio: ' + (err.response?.data?.error || err.message);
      }
    };

    const deletePersonale = async (id) => {
      if (confirm('Sei sicuro di voler eliminare questo personale?')) {
        try {
          await axios.delete(`/api/personale/${id}`);
          fetchPersonale();
        } catch (err) {
          error.value = 'Errore nella eliminazione: ' + (err.response?.data?.error || err.message);
        }
      }
    };

    const closeForm = () => {
      showAddForm.value = false;
      editingId.value = null;
      form.value = {
        nome: '',
        cognome: '',
        grado: '',
        squadra_id: '',
        sesso: 'Maschio',
        stato_servizio: 'Attivo',
        soprannome: '',
        altezza: ''
      };
    };

    const nextPage = () => {
      offset.value += limit;
      fetchPersonale();
    };

    const previousPage = () => {
      if (offset.value > 0) {
        offset.value = Math.max(0, offset.value - limit);
        fetchPersonale();
      }
    };

    const currentPage = () => Math.floor(offset.value / limit) + 1;
    const totalPages = () => Math.ceil(total.value / limit);

    onMounted(() => {
      fetchSquadre();
      fetchPersonale();
    });

    return {
      personale,
      squadre,
      loading,
      error,
      search,
      filterSquadra,
      filterStato,
      showAddForm,
      editingId,
      form,
      limit,
      offset,
      total: total.value,
      currentPage: currentPage(),
      totalPages: totalPages(),
      fetchPersonale,
      filterPersonale,
      editPersonale,
      savePersonale,
      deletePersonale,
      closeForm,
      nextPage,
      previousPage
    };
  }
};
</script>

<style scoped>
.personale-container {
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
  transition: background-color 0.2s;
}

.btn-add:hover {
  background-color: #0056b3;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filters input,
.filters select {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.personale-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-radius: 0.375rem;
}

.personale-table th {
  background-color: #f8f9fa;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
}

.personale-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.personale-table tbody tr:hover {
  background-color: #f9f9f9;
}

.status {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  font-weight: 500;
  font-size: 0.875rem;
}

.status-attivo {
  background-color: #d4edda;
  color: #155724;
}

.status-inattivo {
  background-color: #f8d7da;
  color: #721c24;
}

.status-congedato {
  background-color: #fff3cd;
  color: #856404;
}

.status-deceduto {
  background-color: #e2e3e5;
  color: #383d41;
}

.status-infortunato {
  background-color: #f8d7da;
  color: #721c24;
}

.status-sospeso {
  background-color: #fff3cd;
  color: #856404;
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
  transition: background-color 0.2s;
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

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.pagination button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  cursor: pointer;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
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
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
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

.btn-primary {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
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
