# 🎖️ Armory Inventory - Project Configuration Summary

## ✅ Setup Completato

Il tuo progetto **Armory Inventory** è pronto per lo sviluppo!

---

## 📊 Struttura Progetto

```
armoryinventory/
├── 📂 frontend/                    # Vue.js + Vite (JavaScript/Vue)
├── 📂 backend/                     # PHP API
│   ├── index.php                   # API Router
│   └── api/config/Database.php     # Database connection
├── 📂 nginx/                       # Web server config
│   ├── nginx.conf                  # Main config
│   └── default.conf                # Virtual host
├── 📂 database/                    # Database scripts
│   └── init.sql                    # Schema + seed data
├── docker-compose.yml              # Docker services
├── Dockerfile                      # Production build
├── .gitignore                      # Git ignore rules
├── .dockerignore                   # Docker ignore rules
├── .env.example                    # Environment template
├── SETUP.md                        # Detailed guide
└── README.md                       # Project info
```

---

## 🐳 Docker Services

| Servizio | Porta | Accesso | Ruolo |
|----------|-------|---------|-------|
| **Frontend** (Vite) | 5173 | http://localhost:5173 | Vue.js dev server |
| **Nginx** | 80 | http://localhost | Web server + API proxy |
| **PHP-FPM** | 9000 | Interno | Backend API |
| **MySQL** | 3306 | localhost:3306 | Database |
| **phpMyAdmin** | 8080 | http://localhost:8080 | Database UI |

---

## 🚀 Quick Start

### 1. Avviare i container
```bash
docker-compose up -d
```

### 2. Accedere ai servizi
- Frontend: http://localhost:5173
- API: http://localhost/api
- Database UI: http://localhost:8080

### 3. Login phpMyAdmin
- User: `armory_user`
- Password: `armory_password`
- Database: `USNAVY`

---

## 💾 Database Schema

Database **USNAVY** con 7 tabelle:

| Tabella | Descrizione |
|---------|-------------|
| `squadre` | Bravo, Alpha, Echo Teams |
| `personale` | Dati operatori Navy SEAL |
| `relazioni_personali` | Familiari e contatti |
| `affiliazioni` | Storico reparti (Green Team, BUD/S, CIA) |
| `categorie_equipaggiamento` | Categorie armi (Fucili, Pistole, etc) |
| `equipaggiamento` | Dettagli armi/attrezzature |
| `dotazioni_personale` | Assegnazioni equipaggiamento a operatori |

---

## 🌳 Git Workflow

**Repository:** https://github.com/Eneahoxha/armoryinventory.git

### Branch Configuration
- `main` - Produzione (protected)
- `test` - Sviluppo attivo (current) ✅

### Commits
```bash
# Sei sul branch test
git checkout test

# Fai le modifiche, poi:
git add .
git commit -m "Descrizione del cambio"
git push origin test
```

---

## 🔐 Credenziali Default

| Servizio | User | Password |
|----------|------|----------|
| MySQL | `armory_user` | `armory_password` |
| MySQL Root | `root` | `root_password` |
| phpMyAdmin | armory_user | armory_password |

---

## 📝 File di Configurazione

### Environment (`.env.example`)
- DB_HOST, DB_NAME, DB_USER, DB_PASSWORD
- APP_ENV, APP_DEBUG, APP_URL
- API_URL, VITE_API_URL

### Docker Compose (`docker-compose.yml`)
- 5 servizi: MySQL, PHP, Nginx, Frontend (Vite), phpMyAdmin
- Network interno: `armory_network`
- Volumi persistenti: `mysql_data`
- Health checks automatici

### Nginx (`nginx/default.conf`)
- Frontend routing (Vue Router)
- API proxy a PHP-FPM
- CORS headers
- Static asset caching
- Security headers

---

## 🛠️ Sviluppo Locale

### Frontend (senza Docker)
```bash
npm install
npm run dev      # Vite dev server
npm run build    # Build per produzione
```

### Backend (senza Docker)
```bash
# Modifica DB_HOST in backend/api/config/Database.php
php -S localhost:8000 -t backend/
```

---

## 📂 Project Metadata

- **Framework Frontend:** Vue.js 3 + Vite
- **Backend:** PHP 8.2 FPM
- **Database:** MySQL 8.0
- **Web Server:** Nginx (Alpine)
- **Node Version:** 18 Alpine
- **Git Workflow:** Feature branch (test) + main
- **Deploy Target:** Railway (pronto!)

---

## 🎯 Prossimi Passi

1. ✅ Setup Docker completato
2. ✅ Database schema pronto
3. ✅ Git repository e branch test configurati
4. ⏳ **Sviluppare API endpoints** (`personale`, `squadre`, `equipaggiamento`)
5. ⏳ **Creare componenti Vue** (Dashboard, Personale, Armeria)
6. ⏳ **Implementare autenticazione** (login, ruoli, permessi)
7. ⏳ **Deploy su Railway**

---

## 📖 Documentazione

- **SETUP.md** - Guida dettagliata installazione
- **Progettazione.docx** - User stories e schema DB originale
- **docker-compose.yml** - Documentazione inline

---

**Buon sviluppo! 🚀**

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>
