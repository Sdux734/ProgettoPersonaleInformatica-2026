<?php
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Home - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="hero">
            <h1>📚 Benvenuto nella <?php echo SITE_NAME; ?></h1>
            <p>Scopri migliaia di libri e gestisci i tuoi prestiti online</p>
            
            <button id="book-style-toggle" onclick="toggleBookStyle()">📖 <span>Stile Libro</span></button>
            
            <?php if (!is_logged_in()): ?>
                <div class="hero-buttons">
                    <a href="auth/login.php" class="btn btn-primary btn-lg">Accedi</a>
                    <a href="auth/register.php" class="btn btn-success btn-lg">Registrati</a>
                </div>
            <?php else: ?>
                <div class="hero-buttons">
                    <?php if (is_admin()): ?>
                        <a href="admin/dashboard.php" class="btn btn-primary btn-lg">Dashboard Admin</a>
                    <?php else: ?>
                        <a href="user/catalogo.php" class="btn btn-primary btn-lg">Sfoglia Catalogo</a>
                        <a href="user/miei_prestiti.php" class="btn btn-secondary btn-lg">I Miei Prestiti</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="features">
            <div class="feature">
                <h3>🔍 Ricerca Avanzata</h3>
                <p>Trova rapidamente i libri che cerchi grazie ai filtri per autore, categoria e titolo</p>
            </div>
            
            <div class="feature">
                <h3>📖 Prestiti Facili</h3>
                <p>Richiedi il prestito dei libri che ti interessano in pochi click</p>
            </div>
            
            <div class="feature">
                <h3>💾 Storico Prestiti</h3>
                <p>Visualizza lo storico dei tuoi prestiti passati e attuali</p>
            </div>
            
            <div class="feature">
                <h3>⚙️ Gestione Admin</h3>
                <p>Se sei admin, gestisci catalogo, utenti e prestiti comodamente</p>
            </div>
        </div>
    </div>
    
    <script>
        function toggleBookStyle() {
            document.body.classList.toggle('book-style');
            const button = document.getElementById('book-style-toggle');
            if (document.body.classList.contains('book-style')) {
                button.innerHTML = '🔄 <span>Stile Moderno</span>';
            } else {
                button.innerHTML = '📖 <span>Stile Libro</span>';
            }
            // Salva la preferenza nel localStorage
            localStorage.setItem('bookStyle', document.body.classList.contains('book-style'));
        }
        
        // Carica la preferenza al caricamento della pagina
        document.addEventListener('DOMContentLoaded', function() {
            const bookStyleEnabled = localStorage.getItem('bookStyle') === 'true';
            if (bookStyleEnabled) {
                document.body.classList.add('book-style');
                document.getElementById('book-style-toggle').innerHTML = '🔄 <span>Stile Moderno</span>';
            }
        });
    </script>
</body>
</html>
