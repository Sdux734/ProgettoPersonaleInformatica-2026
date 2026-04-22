# 📚 Gestione Biblioteca - Sistema Web

Un sistema completo per la gestione di una biblioteca online con funzionalità di prestito libri, catalogo ricercabile e pannello amministrativo.

## 🎯 Caratteristiche

### Per gli Utenti
- ✅ Registrazione e login sicuro
- ✅ Ricerca avanzata dei libri per titolo, autore e categoria
- ✅ Visualizzazione dettagli libro
- ✅ Richiesta prestiti (max 3 contemporanei)
- ✅ Visualizzazione storico prestiti (attivi, scaduti, restituiti)
- ✅ Gestione profilo personale

### Per gli Admin
- ✅ **CRUD Completo Libri** - Aggiungi, modifica, visualizza, elimina
- ✅ **CRUD Autori** - Gestione completa
- ✅ **CRUD Categorie** - Gestione classificazioni
- ✅ **Gestione Prestiti** - Restituzione e monitoraggio
- ✅ **Gestione Utenti** - Attivazione, ruoli, disattivazione
- ✅ Dashboard con statistiche
- ✅ Upload immagini copertine libri

## 🛠️ Installazione

### Requisiti
- PHP 7.4+
- MySQL 5.7+
- XAMPP o server web equivalente

### Step 1: Preparazione Database
1. Apri phpMyAdmin
2. Crea un nuovo database (Click su "New")
3. Copia tutto il contenuto di `database.sql`
4. Esegui lo script

**Oppure da riga di comando:**
```bash
mysql -u root -p < database.sql
```

### Step 2: Configurazione
1. Verifica le credenziali in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'biblioteca_db');
   ```

2. Assicurati che la cartella `uploads/copertine/` ha permessi di scrittura:
   ```bash
   chmod 755 uploads/copertine/
   ```

### Step 3: Accesso
1. Avvia XAMPP e attiva Apache + MySQL
2. Accedi a `http://localhost/Progetto/`

## 👥 Credenziali di Test

| Tipo | Email | Password | Ruolo |
|------|-------|----------|-------|
| Admin | admin@biblioteca.it | test123456 | Admin |
| User | luca@email.it | test123456 | Utente |
| User | anna@email.it | test123456 | Utente |

## 📁 Struttura Progetto

```
Progetto/
├── config/              # Configurazioni
│   ├── database.php     # Connessione DB
│   └── config.php       # Costanti globali
│
├── includes/            # Include comuni
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── functions.php
│
├── auth/                # Sistema autenticazione
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── check_login.php
│
├── user/                # Pagine utente
│   ├── dashboard.php
│   ├── catalogo.php
│   ├── dettaglio_libro.php
│   ├── richiedi_prestito.php
│   ├── miei_prestiti.php
│   └── profilo.php
│
├── admin/               # Pannello amministrativo
│   ├── dashboard.php
│   ├── libri/          # CRUD Libri
│   ├── autori/         # CRUD Autori
│   ├── categorie/      # CRUD Categorie
│   ├── prestiti/       # Gestione Prestiti
│   └── utenti/         # Gestione Utenti
│
├── assets/
│   ├── css/style.css
│   └── js/script.js
│
├── uploads/
│   └── copertine/      # Immagini libri
│
├── database.sql        # SQL di inizializzazione
└── index.php           # Homepage
```

## 🚀 Funzionalità Principali

### Ricerca Libri
- Ricerca per titolo
- Ricerca per autore
- Filtro per categoria
- Visualizzazione disponibilità

### Prestiti
- **Durata**: 14 giorni (configurabile in `config/config.php`)
- **Limite**: max 3 prestiti contemporanei per utente
- **Stati**: Attivo, Restituito, Scaduto

### Sicurezza
- Password hashate con bcrypt
- Protezione CSRF (tramite validazione sessione)
- Sanificazione input (stripslashes, htmlspecialchars)
- Autorizzazione per ruoli

## ⚙️ Personalizzazione

### Modificare Giorni Prestito
Modifica in `config/config.php`:
```php
define('GIORNI_PRESTITO', 14); // Cambia numero
```

### Modificare Limite Prestiti
```php
define('MAX_PRESTITI_UTENTE', 3); // Cambia numero
```

### URL Base
Se non usi `localhost/Progetto`, modifica:
```php
define('SITE_URL', 'http://tuodominio.com/biblioteca/');
```

## 📋 Principali Tabelle Database

### utenti
- id, nome, cognome, email, password, ruolo, data_registrazione, attivo

### libri
- id, titolo, isbn, id_autore, id_categoria, anno_pubblicazione, editore, numero_pagine, descrizione, copertina, copie_disponibili, copie_totali

### prestiti
- id, id_utente, id_libro, data_prestito, data_scadenza, data_restituzione, stato, note

### autori
- id, nome, cognome, nazionalita, data_nascita, biografia

### categorie
- id, nome, descrizione

### recensioni
- id, id_utente, id_libro, voto, commento, data_recensione

## 🔧 Soluzione Problemi

### Errore "Connessione fallita"
- Verifica credenziali MySQL in `config/database.php`
- Assicurati che MySQL sia in esecuzione
- Controlla se il database `biblioteca_db` esiste

### Upload immagini non funziona
- Verifica permessi cartella `uploads/copertine/`
- Su Windows potrebbe non essere necessario
- Controlla il limite di upload in `php.ini`

### Prestiti non si decrementano
- Verifica che le transazioni MySQL siano supportate (InnoDB)
- Controlla i permessi di inserimento/aggiornamento tabelle

## 📝 Note di Sviluppo

- Tutte le celle form sono protette da XSS
- I database usa transazioni per operazioni critiche
- Le password sono sempre richeste tramite HTTPS in produzione
- Implementare HTTPS con certificato SSL prima di rilasciare in produzione

## 🔐 Sicurezza (Consigli per Produzione)

1. **SSL/TLS**: Configura HTTPS obbligatorio
2. **CSRF Token**: Aggiungi token CSRF alle form
3. **Rate Limiting**: Implementa limitazione accessi
4. **Sanitizzazione**: Usa prepared statements (già implementato)
5. **Backup**: Effettua backup regolari del database
6. **Logs**: Implementa logging errori

## 📞 Support

Per problemi o suggerimenti, contatta l'amministratore.

---

**Versione**: 1.0  
**Ultimo Aggiornamento**: Aprile 2026
