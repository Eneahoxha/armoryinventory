<template>
  <div class="relazioni-container">
    <div class="header">
      <h1>Relazioni Familiari</h1>
      <p class="subtitle">Informazioni sulla famiglia e relazioni personali del personale</p>
    </div>

    <div class="search-section">
      <input 
        v-model="searchName" 
        type="text" 
        placeholder="Cerca per nome operatore..."
        @keyup="filterPersonale"
      >
    </div>

    <div v-if="loading" class="loading">Caricamento...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else-if="filteredPersonale.length === 0" class="empty-state">
      <p>Nessun operatore trovato</p>
    </div>

    <div v-else class="relazioni-grid">
      <div class="persona-card" v-for="persona in filteredPersonale" :key="persona.personale_id">
        <div class="card-header">
          <div class="operator-info">
            <img :src="getOperatorImage(persona.personale_id)" :alt="persona.cognome" class="operator-photo">
            <div class="info">
              <h3>{{ persona.cognome }} {{ persona.nome }}</h3>
              <p class="rank">{{ persona.grado }}</p>
              <p class="team" v-if="persona.nome_squadra">{{ persona.nome_squadra }}</p>
            </div>
          </div>
        </div>

        <div class="card-body">
          <!-- Coniuge/Partner -->
          <div v-if="getRelation(persona.personale_id, 'coniuge')" class="relation-block">
            <h4>👫 Coniuge</h4>
            <p>{{ getRelation(persona.personale_id, 'coniuge') }}</p>
          </div>

          <!-- Figli -->
          <div v-if="getRelations(persona.personale_id, 'figlio').length > 0" class="relation-block">
            <h4>👶 Figli</h4>
            <ul>
              <li v-for="(figlio, idx) in getRelations(persona.personale_id, 'figlio')" :key="idx">
                {{ figlio }}
              </li>
            </ul>
          </div>

          <!-- Genitori -->
          <div v-if="getRelations(persona.personale_id, 'genitore').length > 0" class="relation-block">
            <h4>👨‍👩 Genitori</h4>
            <ul>
              <li v-for="(genitore, idx) in getRelations(persona.personale_id, 'genitore')" :key="idx">
                {{ genitore }}
              </li>
            </ul>
          </div>

          <!-- Fratelli/Sorelle -->
          <div v-if="getRelations(persona.personale_id, 'fratello').length > 0" class="relation-block">
            <h4>👫 Fratelli/Sorelle</h4>
            <ul>
              <li v-for="(fratello, idx) in getRelations(persona.personale_id, 'fratello')" :key="idx">
                {{ fratello }}
              </li>
            </ul>
          </div>

          <!-- Interessi/Relazioni Romantiche -->
          <div v-if="getRelations(persona.personale_id, 'interesse').length > 0" class="relation-block">
            <h4>💔 Interessi/Ex</h4>
            <ul>
              <li v-for="(interesse, idx) in getRelations(persona.personale_id, 'interesse')" :key="idx">
                {{ interesse }}
              </li>
            </ul>
          </div>

          <!-- Animali Domestici -->
          <div v-if="getRelations(persona.personale_id, 'animale').length > 0" class="relation-block">
            <h4>🐕 Animali</h4>
            <ul>
              <li v-for="(animale, idx) in getRelations(persona.personale_id, 'animale')" :key="idx">
                {{ animale }}
              </li>
            </ul>
          </div>

          <!-- Affiliazioni Speciali -->
          <div v-if="getRelations(persona.personale_id, 'godson').length > 0" class="relation-block">
            <h4>✨ Legami Speciali</h4>
            <ul>
              <li v-for="(legame, idx) in getRelations(persona.personale_id, 'godson')" :key="idx">
                {{ legame }}
              </li>
            </ul>
          </div>

          <div v-if="!hasRelations(persona.personale_id)" class="no-relations">
            <p>Nessuna informazione disponibile</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'RelazioniList',
  setup() {
    const personale = ref([]);
    const searchName = ref('');
    const loading = ref(true);
    const error = ref(null);

    // Database delle relazioni familiari basato su Json.md
    const relazioni = {
      'SEAL_001': { // Clay Spenser
        coniuge: 'Stella Baxter',
        genitori: ['Ash Spenser (Padre)', 'Unnamed Mother (Deceduta)'],
        figli: ['Brian Spenser (Figlio)'],
        interessi: ['Rebecca Bowen (Ex-girlfriend)']
      },
      'SEAL_002': { // Sonny Quinn
        genitori: ['Emmet Quinn (Padre)', 'Eileen Quinn (Madre)'],
        figli: ['Leanne Oliver (Figlia)'],
        interessi: ['Hannah Oliver (Estranged)', 'Lisa Davis (Ex)']
      },
      'SEAL_003': { // Lisa Davis
        genitori: ['Unnamed Mother (Deceduta)', 'Unnamed Father'],
        fratelli: ['Ronnie Davis (Sorella)', 'Michelle Davis (Sorella, Deceduta)'],
        interessi: ['Reiss Julian (Ex)', 'Sonny Quinn (Ex)', 'Danny Cooper (Ex)']
      },
      'SEAL_004': { // Jason Hayes
        coniuge: 'Alana Hayes (Deceduta)',
        genitori: ['Dave Hayes (Padre)', 'Linda Hayes (Madre)'],
        figli: ['Emma Hayes (Figlia)', 'Michael Hayes (Figlio)'],
        godson: ['Landon Massey (Godson)'],
        interessi: ['Mandy Ellis', 'Natalie Pierce (Ex)', 'Amy Nelson (Former Affair)']
      },
      'SEAL_005': { // Ray Perry
        coniuge: 'Naima Perry',
        figli: ['Jameelah Perry (Figlia)', 'Raymond Perry Jr (Figlio)']
      },
      'SEAL_006': { // Brock Reynolds
        animali: ['Cerberus (Cane)', 'Pepper (Cane)']
      },
      'SEAL_007': { // Trent Sawyer
        coniuge: 'Ex-Wife'
      },
      'SEAL_008': { // Omar Hamza
        // No family info available in source
      },
      'SEAL_009': { // Drew Franklin
        fratelli: ['Nora (Sorella)']
      },
      'SEAL_010': { // Scott Carter
        // Deceased - no family info
      },
      'SEAL_011': { // Michael Chen
        // No family info available
      },
      'SEAL_012': { // Wes Soto
        // No family info available
      },
      'SEAL_013': { // Eric Blackburn
        coniuge: 'Multiple ex-wives'
      },
      'SEAL_014': { // Amanda Ellis
        interessi: ['Jason Hayes']
      }
    };

    const fetchPersonale = async () => {
      try {
        loading.value = true;
        const res = await axios.get('/api/personale', { params: { limit: 200 } });
        personale.value = res.data.data || [];
        error.value = null;
      } catch (err) {
        error.value = err.response?.data?.error || 'Errore nel caricamento personale';
        console.error(err);
      } finally {
        loading.value = false;
      }
    };

    const filteredPersonale = computed(() => {
      if (!searchName.value) return personale.value;
      return personale.value.filter(p =>
        `${p.cognome} ${p.nome}`.toLowerCase().includes(searchName.value.toLowerCase())
      );
    });

    const filterPersonale = () => {
      // Computed property handles filtering
    };

    const getRelation = (personaleId, type) => {
      const rels = relazioni[personaleId];
      if (!rels) return null;
      
      switch(type) {
        case 'coniuge': return rels.coniuge;
        case 'figlio': return rels.figli?.[0];
        case 'genitore': return rels.genitori?.[0];
        case 'fratello': return rels.fratelli?.[0];
        case 'interesse': return rels.interessi?.[0];
        case 'animale': return rels.animali?.[0];
        case 'godson': return rels.godson?.[0];
        default: return null;
      }
    };

    const getRelations = (personaleId, type) => {
      const rels = relazioni[personaleId];
      if (!rels) return [];
      
      switch(type) {
        case 'figlio': return rels.figli || [];
        case 'genitore': return rels.genitori || [];
        case 'fratello': return rels.fratelli || [];
        case 'interesse': return rels.interessi || [];
        case 'animale': return rels.animali || [];
        case 'godson': return rels.godson || [];
        default: return [];
      }
    };

    const hasRelations = (personaleId) => {
      return !!relazioni[personaleId];
    };

    const getOperatorImage = (personaleId) => {
      // Map SEAL IDs to image names
      const imageMap = {
        'SEAL_001': 'download (6).jpeg', // Clay
        'SEAL_002': 'OmarHamza.JPG.webp', // Sonny (placeholder)
        'SEAL_003': 'Lisa_Davis.webp',
        'SEAL_004': 'hero.png', // Jason
        'SEAL_005': 'download (5).jpeg', // Ray
        'SEAL_006': 'download (8).jpeg', // Brock
        'SEAL_007': 'Wes_Soto.webp', // Trent
        'SEAL_008': 'OmarHamza.JPG.webp', // Omar
        'SEAL_009': 'download (9).jpeg', // Drew
        'SEAL_010': 'Devgru logo .jpeg', // Scott
        'SEAL_011': 'Michael_Chen.webp', // Michael
        'SEAL_012': 'Wes_Soto.webp', // Wes
        'SEAL_013': 'DELTA FORCE.jpeg', // Eric
        'SEAL_014': 'Mandy_Ellis.webp' // Amanda
      };
      
      const imageName = imageMap[personaleId] || 'hero.png';
      return `/Immagini/${imageName}`;
    };

    onMounted(() => {
      fetchPersonale();
    });

    return {
      personale,
      searchName,
      loading,
      error,
      filteredPersonale,
      filterPersonale,
      getRelation,
      getRelations,
      hasRelations,
      getOperatorImage
    };
  }
};
</script>

<style scoped>
.relazioni-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  margin-bottom: 2rem;
}

.header h1 {
  margin: 0;
  color: #1a1a1a;
  font-size: 2rem;
}

.subtitle {
  margin: 0.5rem 0 0 0;
  color: #666;
  font-size: 1rem;
}

.search-section {
  margin-bottom: 2rem;
}

.search-section input {
  width: 100%;
  max-width: 500px;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.relazioni-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.persona-card {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.persona-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.card-header {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  color: white;
  padding: 1.5rem;
}

.operator-info {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.operator-photo {
  width: 80px;
  height: 80px;
  border-radius: 0.5rem;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.info h3 {
  margin: 0;
  font-size: 1.25rem;
}

.info .rank {
  margin: 0.25rem 0;
  font-size: 0.875rem;
  color: #fbbf24;
}

.info .team {
  margin: 0.25rem 0 0 0;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
}

.card-body {
  padding: 1.5rem;
}

.relation-block {
  margin-bottom: 1.5rem;
}

.relation-block h4 {
  margin: 0 0 0.5rem 0;
  color: #1a1a2e;
  font-size: 0.95rem;
}

.relation-block p {
  margin: 0;
  color: #495057;
  font-size: 0.875rem;
  line-height: 1.5;
}

.relation-block ul {
  margin: 0;
  padding-left: 1.5rem;
  list-style: disc;
}

.relation-block li {
  color: #495057;
  font-size: 0.875rem;
  margin: 0.25rem 0;
}

.no-relations {
  text-align: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 0.375rem;
  color: #999;
  font-size: 0.875rem;
}

.loading,
.error,
.empty-state {
  text-align: center;
  padding: 2rem;
  border-radius: 0.375rem;
}

.loading {
  background: #cfe2ff;
  color: #084298;
}

.error {
  background: #f8d7da;
  color: #721c24;
}

.empty-state {
  background: #f8f9fa;
  color: #666;
}
</style>
