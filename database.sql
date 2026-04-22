-- Creazione Database
CREATE DATABASE IF NOT EXISTS biblioteca_db;
USE biblioteca_db;

-- Tabella utenti
CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    ruolo ENUM('utente', 'admin') DEFAULT 'utente',
    data_registrazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    attivo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella autori
CREATE TABLE IF NOT EXISTS autori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    nazionalita VARCHAR(100),
    data_nascita DATE,
    biografia TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella categorie
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descrizione TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella libri
CREATE TABLE IF NOT EXISTS libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(200) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    id_autore INT,
    id_categoria INT,
    anno_pubblicazione YEAR,
    editore VARCHAR(100),
    numero_pagine INT,
    descrizione TEXT,
    copertina VARCHAR(255),
    copie_disponibili INT DEFAULT 1,
    copie_totali INT DEFAULT 1,
    data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_autore) REFERENCES autori(id) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorie(id) ON DELETE SET NULL,
    INDEX idx_titolo (titolo),
    INDEX idx_isbn (isbn),
    INDEX idx_categoria (id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella prestiti
CREATE TABLE IF NOT EXISTS prestiti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_libro INT NOT NULL,
    data_prestito DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_scadenza DATE NOT NULL,
    data_restituzione DATETIME NULL,
    stato ENUM('attivo', 'restituito', 'scaduto') DEFAULT 'attivo',
    note TEXT,
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (id_libro) REFERENCES libri(id) ON DELETE CASCADE,
    INDEX idx_utente (id_utente),
    INDEX idx_libro (id_libro),
    INDEX idx_stato (stato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella recensioni
CREATE TABLE IF NOT EXISTS recensioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_libro INT NOT NULL,
    voto INT CHECK(voto BETWEEN 1 AND 5),
    commento TEXT,
    data_recensione DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (id_libro) REFERENCES libri(id) ON DELETE CASCADE,
    UNIQUE KEY unica_recensione (id_utente, id_libro),
    INDEX idx_libro (id_libro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserimento dati di test
INSERT INTO utenti (nome, cognome, email, password, ruolo) VALUES
('Mario', 'Rossi', 'admin@biblioteca.it', '$2y$10$BgoR5kP7d/bcHo/16LrfpO/nv6Q8tM8pOxyZKwgRIebPxlAxJ0J1K', 'admin'),
('Luca', 'Bianchi', 'luca@email.it', '$2y$10$BgoR5kP7d/bcHo/16LrfpO/nv6Q8tM8pOxyZKwgRIebPxlAxJ0J1K', 'utente'),
('Anna', 'Verdi', 'anna@email.it', '$2y$10$BgoR5kP7d/bcHo/16LrfpO/nv6Q8tM8pOxyZKwgRIebPxlAxJ0J1K', 'utente');

INSERT INTO categorie (nome, descrizione) VALUES
('Narrativa', 'Romanzi e racconti'),
('Fantascienza', 'Libri di fantascienza e distopia'),
('Giallo', 'Romanzi gialli e thriller'),
('Storia', 'Libri di storia e saggistica'),
('Filosofia', 'Testi filosofici'),
('Avventura', 'Romanzi di avventura');

INSERT INTO autori (nome, cognome, nazionalita, data_nascita) VALUES
('Isaac', 'Asimov', 'USA', '1920-01-02'),
('Agatha', 'Christie', 'Regno Unito', '1890-01-15'),
('Stephen', 'King', 'USA', '1947-09-21'),
('Donna', 'Tartt', 'USA', '1963-12-23'),
('John', 'Grisham', 'USA', '1955-02-08'),
('Joanne', 'Rowling', 'Regno Unito', '1965-07-31');

INSERT INTO libri (titolo, isbn, id_autore, id_categoria, anno_pubblicazione, editore, numero_pagine, descrizione, copie_totali, copie_disponibili) VALUES
('Fondazione', '9788854120159', 1, 2, 1951, 'Mondadori', 412, 'Un capolavoro della fantascienza che racconta la fondazione di un ampio impero per preservare la civiltà.', 3, 3),
('Il Signore degli Anelli', '9788833570013', NULL, 6, 1954, 'Bompiani', 587, 'L\'epica fantasy più celebre di sempre.', 4, 4),
('Delitto e Castigo', '9788804742234', NULL, 3, 1866, 'Mondadori', 671, 'Uno dei più grandi romanzi psicologici della letteratura.', 2, 2),
('Assassinio sull\'Orient Express', '9788845440113', 2, 3, 1934, 'Mondadori', 256, 'Hercule Poirot a bordo di un treno lussuoso con un mistero da risolvere.', 3, 3),
('Shining', '9788820030116', 3, 2, 1977, 'Sperling & Kupfer', 596, 'Un horror psicologico che lascia senza fiato.', 2, 2);

-- Password di test (test123456): $2y$10$BgoR5kP7d/bcHo/16LrfpO/nv6Q8tM8pOxyZKwgRIebPxlAxJ0J1K
