<template>
  <div class="equipaggiamento-container">
    <div class="header">
      <h1>Gestione Equipaggiamento</h1>
      <button @click="showAddForm = true" class="btn-add">
        <i class="icon">+</i> Aggiungi Equipaggiamento
      </button>
    </div>

    <div class="filters">
      <input 
        v-model="search" 
        type="text" 
        placeholder="Cerca per modello o seriale..."
        @keyup="fetchEquipaggiamento"
      >
      <select v-model="filterCategoria" @change="fetchEquipaggiamento">
        <option value="">Tutte le Categorie</option>
        <option value="Fucile">Fucile</option>
        <option value="Pistola">Pistola</option>
        <option value="Granata">Granata</option>
        <option value="Equipaggiamento">Equipaggiamento</option>
        <option value="Comunicazioni">Comunicazioni</option>
      </select>
      <select v-model="filterStato" @change="fetchEquipaggiamento">
        <option value="">Tutti gli Stati</option>
        <option value="Disponibile">Disponibile</option>
        <option value="Assegnato">Assegnato</option>
        <option value="Manutenzione">Manutenzione</option>
        <option value="Ritirato">Ritirato</option>
      </select>
    </div>

    <div v-if="loading" class="loading">Caricamento...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <table v-else class="equip-table">
      <thead>
        <tr>
          <th>Modello</th>
          <th>Seriale</th>
          <th>Categoria</th>
          <th>Stato</th>
          <th>Quantità</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="e in equipaggiamento" :key="e.equip_id">
          <td>{{ e.nome_modello }}</td>
          <td>{{ e.numero_seriale }}</td>
          <td>{{ e.nome_categoria }}</td>
          <td>
            <span :class="['status', 'status-' + (e.stato || '').toLowerCase()]">
              {{ e.stato }}
            </span>
          </td>
          <td>{{ e.quantita }}</td>
          <td class="actions">
            <button @click="editEquip(e)" class="btn-small">Modifica</button>
            <button @click="deleteEquip(e.equip_id)" class="btn-small btn-danger">Elimina</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Form -->
    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingId ? 'Modifica Equipaggiamento' : 'Aggiungi Equipaggiamento' }}</h2>
        <form @submit.prevent="saveEquip">
          <input v-model="form.nome_modello" type="text" placeholder="Modello" required>
          <input v-model="form.numero_seriale" type="text" placeholder="Numero Seriale" required>
          <select v-model="form.categoria_id" required>
            <option value="">Seleziona Categoria</option>
            <option value="1">Fucile</option>
            <option value="2">Pistola</option>
            <option value="3">Granata</option>
            <option value="4">Equipaggiamento</option>
            <option value="5">Comunicazioni</option>
          </select>
          <input v-model.number="form.quantita" type="number" placeholder="Quantità" required>
          <select v-model="form.stato" required>
            <option value="Disponibile">Disponibile</option>
            <option value="Assegnato">Assegnato</option>
            <option value="Manutenzione">Manutenzione</option>
            <option value="Ritirato">Ritirato</option>
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
  name: 'EquipaggiamentoList',
  setup() {
    const equipaggiamento = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const search = ref('');
    const filterCategoria = ref('');
    const filterStato = ref('');
    const showAddForm = ref(false);
    const editingId = ref(null);

    const form = ref({
      nome_modello: '',
      numero_seriale: '',
      categoria_id: '',
      quantita: 1,
      stato: 'Disponibile',
      note: ''
    });

    const fetchEquipaggiamento = async () => {
      try {
        loading.value = true;
        const res = await axios.get('/api/equipaggiamento', { params: { limit: 100 } });
        equipaggiamento.value = res.data.data || [];
        error.value = null;
      } catch (err) {
        error.value = err.response?.data?.error || 'Errore nel caricamento';
      } finally {
        loading.value = false;
      }
    };

    const editEquip = (e) => {
      editingId.value = e.equip_id;
      form.value = { ...e };
      showAddForm.value = true;
    };

    const saveEquip = async () => {
      try {
        if (editingId.value) {
          await axios.put(`/api/equipaggiamento/${editingId.value}`, form.value);
        } else {
          await axios.post('/api/equipaggiamento', form.value);
        }
        closeForm();
        fetchEquipaggiamento();
      } catch (err) {
        error.value = 'Errore nel salvataggio: ' + (err.response?.data?.error || err.message);
      }
    };

    const deleteEquip = async (id) => {
      if (confirm('Sei sicuro di voler eliminare questo equipaggiamento?')) {
        try {
          await axios.delete(`/api/equipaggiamento/${id}`);
          fetchEquipaggiamento();
        } catch (err) {
          error.value = 'Errore nella eliminazione: ' + (err.response?.data?.error || err.message);
        }
      }
    };

    const closeForm = () => {
      showAddForm.value = false;
      editingId.value = null;
      form.value = {
        nome_modello: '',
        numero_seriale: '',
        categoria_id: '',
        quantita: 1,
        stato: 'Disponibile',
        note: ''
      };
    };

    onMounted(() => {
      fetchEquipaggiamento();
    });

    return {
      equipaggiamento,
      loading,
      error,
      search,
      filterCategoria,
      filterStato,
      showAddForm,
      editingId,
      form,
      editEquip,
      saveEquip,
      deleteEquip,
      closeForm,
      fetchEquipaggiamento
    };
  }
};
</script>

<style scoped>
.equipaggiamento-container {
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

.filters input,
.filters select {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.375rem;
}

.equip-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.equip-table th {
  background-color: #f8f9fa;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
}

.equip-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.equip-table tbody tr:hover {
  background-color: #f9f9f9;
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

.status-assegnato {
  background-color: #cce5ff;
  color: #004085;
}

.status-manutenzione {
  background-color: #fff3cd;
  color: #856404;
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
