-- Create USNAVY Database
CREATE DATABASE IF NOT EXISTS USNAVY;
USE USNAVY;

-- 1. Tabella Squadre
CREATE TABLE IF NOT EXISTS squadre (
    squadra_id INT AUTO_INCREMENT PRIMARY KEY,
    nome_squadra VARCHAR(255) NOT NULL UNIQUE,
    stato ENUM('Disponibile', 'Dispiegata', 'Sospesa', 'Sciolta') DEFAULT 'Disponibile',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabella Personale (Il cuore del database)
CREATE TABLE IF NOT EXISTS personale (
    personale_id VARCHAR(255) PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    soprannome VARCHAR(50),
    nome_in_codice VARCHAR(20),
    occupazione VARCHAR(100),
    grado VARCHAR(100),
    data_nascita DATE,
    luogo_origine VARCHAR(100),
    sesso ENUM('Maschio', 'Femmina', 'Non specificato') NOT NULL,
    altezza VARCHAR(10),
    stato_servizio ENUM('Attivo', 'Congedato', 'Deceduto', 'Infortunato', 'Sospeso') DEFAULT 'Attivo',
    squadra_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (squadra_id) REFERENCES squadre(squadra_id) ON DELETE SET NULL,
    INDEX idx_stato_servizio (stato_servizio),
    INDEX idx_squadra (squadra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabella Familiari e Relazioni
CREATE TABLE IF NOT EXISTS relazioni_personali (
    relazione_id INT AUTO_INCREMENT PRIMARY KEY,
    personale_id VARCHAR(255),
    nome_parente VARCHAR(100),
    tipo_relazione VARCHAR(255),
    stato_vitale ENUM('Vivo', 'Deceduto') DEFAULT 'Vivo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personale_id) REFERENCES personale(personale_id) ON DELETE CASCADE,
    INDEX idx_personale (personale_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabella Affiliazioni (Storico reparti)
CREATE TABLE IF NOT EXISTS affiliazioni (
    affiliazione_id INT AUTO_INCREMENT PRIMARY KEY,
    personale_id VARCHAR(255),
    nome_ente VARCHAR(100),
    stato_affiliazione ENUM('Attuale', 'Passata') DEFAULT 'Passata',
    data_inizio DATE,
    data_fine DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personale_id) REFERENCES personale(personale_id) ON DELETE CASCADE,
    INDEX idx_personale (personale_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Categorie Equipaggiamento
CREATE TABLE IF NOT EXISTS categorie_equipaggiamento (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(50) NOT NULL UNIQUE,
    descrizione TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabella Equipaggiamento
CREATE TABLE IF NOT EXISTS equipaggiamento (
    equip_id INT AUTO_INCREMENT PRIMARY KEY,
    nome_modello VARCHAR(100) NOT NULL,
    descrizione TEXT,
    categoria_id INT,
    stato ENUM('Disponibile', 'Assegnato', 'Manutenzione', 'Rottamato') DEFAULT 'Disponibile',
    numero_seriale VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorie_equipaggiamento(categoria_id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_stato (stato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Assegnazione Equipaggiamento (Relazione molti-a-molti)
CREATE TABLE IF NOT EXISTS dotazioni_personale (
    dotazione_id INT AUTO_INCREMENT PRIMARY KEY,
    personale_id VARCHAR(255),
    equip_id INT,
    tipo_uso ENUM('Primaria', 'Secondaria', 'Tattico', 'Speciale') NOT NULL,
    note_configurazione TEXT,
    data_assegnazione DATE NOT NULL,
    data_ritiro DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personale_id) REFERENCES personale(personale_id) ON DELETE CASCADE,
    FOREIGN KEY (equip_id) REFERENCES equipaggiamento(equip_id) ON DELETE CASCADE,
    INDEX idx_personale (personale_id),
    INDEX idx_equip (equip_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserimenti di base
INSERT INTO squadre (nome_squadra, stato) VALUES
('Bravo Team', 'Disponibile'),
('Alpha Team', 'Disponibile'),
('Echo Team', 'Disponibile'),
('Supporto/CIA', 'Disponibile');

-- Users Table for Authentication
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nome VARCHAR(100),
    cognome VARCHAR(100),
    ruolo ENUM('admin', 'capo_sm', 'armaiolo', 'ufficiale', 'operatore') DEFAULT 'operatore',
    stato ENUM('Attivo', 'Disattivato') DEFAULT 'Attivo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_ruolo (ruolo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed admin user (password: admin123456) - CHANGE THIS ON PRODUCTION!
INSERT IGNORE INTO users (email, password_hash, nome, cognome, ruolo, stato) VALUES
('admin@armory.local', '$2y$10$N9qo8uLOickgx2ZMRZoMye4QJq6H9Y8K3y7k7DxuXYV8VjXWp5aQa', 'Admin', 'User', 'admin', 'Attivo');

INSERT INTO categorie_equipaggiamento (nome_categoria, descrizione) VALUES
('Fucili d''Assalto', 'Fucili da assalto e carabine tattiche'),
('Pistole', 'Pistole da combattimento'),
('Precisione', 'Fucili di precisione e sniper'),
('Supporto Pesante', 'Mitragliatrici e armi di supporto'),
('Accessori/K9', 'Accessori ottici, silenziatori, cani da combattimento');
