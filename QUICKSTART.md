# 🚀 AVVIO RAPIDO - Armory Inventory

## ✨ Quello che è stato fatto:

✅ **Repository GitHub configurato**
- URL: https://github.com/Eneahoxha/armoryinventory.git
- Branch `main` + `test` creati
- Remote origin aggiunto

✅ **Docker completo**
- MySQL 8.0 (database USNAVY)
- PHP 8.2 FPM (API backend)
- Nginx Alpine (web server)
- Vite Node.js (dev server frontend)
- phpMyAdmin (UI database)

✅ **Database pronto**
- Schema con 7 tabelle
- Seed data (Squadre, Categorie)
- Indici su campi importanti

✅ **Backend scaffold**
- Router API PHP
- Connessione database MySQL
- CORS configurato
- Error handling

✅ **Frontend Vite + Vue.js**
- Vue 3 setup completo
- Vite configurato
- Pronto per i componenti

✅ **Git workflow**
- .gitignore completo
- 3 commits con history
- Branch test attivo (origin/test)

---

## 🎯 Come iniziare lo sviluppo

### Opzione 1: Con Docker (CONSIGLIATO)

```bash
# 1. Avvia i container
docker-compose up -d

# 2. Accedi ai servizi
Frontend:   http://localhost:5173
API:        http://localhost/api
Database:   http://localhost:8080 (phpMyAdmin)

# 3. Credenziali phpMyAdmin
User: armory_user
Password: armory_password
```

### Opzione 2: Sviluppo locale

```bash
# Frontend
npm install
npm run dev        # http://localhost:5173

# Backend (in altra finestra)
# Assicurati che MySQL locale sia attivo
# poi edita backend/api/config/Database.php con i tuoi dati
php -S localhost:8000 -t backend/
```

---

## 📝 Workflow Git

```bash
# Sei già sul branch test
git checkout test

# Fai modifiche, poi:
git add .
git commit -m "Descrizione cambio"
git push origin test

# Quando pronto per produzione:
git checkout main
git merge test
git push origin main
```

---

## 📂 File Importanti

| File | Descrizione |
|------|-------------|
| `docker-compose.yml` | Stack Docker completo |
| `database/init.sql` | Schema e seed data |
| `backend/index.php` | Router API |
| `nginx/default.conf` | Config web server |
| `SETUP.md` | Guida dettagliata |
| `PROJECT_STATUS.md` | Status completo |

---

## 🛠️ Database

### Tabelle principali
- `squadre` (Bravo, Alpha, Echo)
- `personale` (Navy SEAL operators)
- `equipaggiamento` (Armi e gear)
- `dotazioni_personale` (Assegnazioni)

### Accesso
- **Locale Docker:** `mysql:3306`
- **User:** armory_user
- **Password:** armory_password
- **Database:** USNAVY

---

## ❓ Troubleshooting

### Docker non avvia MySQL
```bash
docker-compose down -v
docker-compose up -d
```

### Porta già in uso
Modifica in `docker-compose.yml`:
```yaml
ports:
  - "8001:80"    # Nginx su 8001
  - "3307:3306"  # MySQL su 3307
```

### Permessi file
```bash
docker-compose exec php chown -R www-data:www-data /app
```

---

## 🎓 Prossimi Passi

1. **Sviluppa API endpoints** (GET/POST/PUT/DELETE)
   - `/api/personale` - CRUD operatori
   - `/api/squadre` - CRUD squadre
   - `/api/equipaggiamento` - CRUD armi

2. **Crea componenti Vue**
   - Dashboard (statistiche)
   - Lista personale
   - Gestione armeria
   - Assegnazioni equipaggiamento

3. **Implementa autenticazione**
   - Login/Logout
   - Ruoli (Admin, Armaiolo, Operatore, Comandante)
   - Middleware auth

4. **Deploy su Railway**
   - Create Railway account
   - Connetti repository
   - Deploy main branch

---

## 📞 Info Progetto

**Tecnologie:**
- Frontend: Vue.js 3, Vite
- Backend: PHP 8.2
- Database: MySQL 8.0
- Server: Nginx + Docker
- Version Control: Git + GitHub
- Deploy: Railway (pronto!)

**Repository:** https://github.com/Eneahoxha/armoryinventory

---

**Buono sviluppo! 🚀**

Ogni commit va sul branch `test` finché non è pronto per `main`.

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>
