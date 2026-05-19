# Armory Inventory - Project Setup

Questo documento descrive come utilizzare Docker per sviluppare e deployare il progetto.

## Requisiti

- Docker
- Docker Compose
- Node.js 18+ (per sviluppo locale senza Docker)
- PHP 8.2+ (per backend locale)
- MySQL 8.0+ (per database locale)

## Struttura Progetto

```
armoryinventory/
├── frontend/          # Vue.js + Vite (frontend)
├── backend/           # PHP API
├── database/          # SQL initialization
├── nginx/             # Nginx configuration
├── docker-compose.yml # Docker services
└── Dockerfile         # Production build
```

## Quick Start con Docker

### 1. Avviare i container

```bash
docker-compose up -d
```

I servizi disponibili:
- **Frontend (Vite)**: http://localhost:5173
- **API (PHP/Nginx)**: http://localhost/api
- **phpMyAdmin**: http://localhost:8080 (user: armory_user / password: armory_password)
- **MySQL**: localhost:3306

### 2. Installare dipendenze frontend

```bash
docker-compose exec frontend npm install
```

### 3. Verificare lo stato

```bash
docker-compose ps
```

### 4. Accedere ai log

```bash
# Tutti i container
docker-compose logs -f

# Specifico (es. PHP)
docker-compose logs -f php

# Frontend
docker-compose logs -f frontend
```

### 5. Arrestare i container

```bash
docker-compose down
```

## Database

### Accesso phpMyAdmin

- URL: http://localhost:8080
- Server: mysql
- User: armory_user
- Password: armory_password
- Database: USNAVY

### Schema Database

Le tabelle vengono create automaticamente al primo avvio:
- `squadre` - Squadre Navy SEAL
- `personale` - Dati operatori
- `relazioni_personali` - Familiari e contatti
- `affiliazioni` - Storico reparti
- `categorie_equipaggiamento` - Categorie armi/equipaggiamento
- `equipaggiamento` - Dettagli armi e attrezzature
- `dotazioni_personale` - Assegnazioni equipaggiamento

## Sviluppo Locale (senza Docker)

### Frontend

```bash
# Installare dipendenze
npm install

# Avviare server Vite
npm run dev

# Build per produzione
npm run build
```

### Backend PHP

Configurare i parametri di connessione in `backend/api/config/Database.php`

```bash
# Avviare PHP server locale
php -S localhost:8000 -t backend/
```

## Environment Variables

Configurare in `docker-compose.yml`:

```yaml
environment:
  DB_HOST: mysql
  DB_DATABASE: USNAVY
  DB_USERNAME: armory_user
  DB_PASSWORD: armory_password
```

## Troubleshooting

### MySQL non si avvia
```bash
docker-compose down -v  # Rimuove i volumi
docker-compose up -d
```

### Permessi PHP-FPM
```bash
docker-compose exec php chown -R www-data:www-data /app
```

### Port già in uso
Modificare le porte in `docker-compose.yml`:
```yaml
ports:
  - "8000:80"   # Nginx su porta 8000
  - "3307:3306" # MySQL su porta 3307
```

## Git Workflow

Usiamo il branch `test` per lo sviluppo:

```bash
# Switch al branch test
git checkout test

# Dopo le modifiche
git add .
git commit -m "Descrizione del cambio"
git push -u origin test
```

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>
