<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../auth/check_login.php';

if (!is_admin()) {
    redirect(SITE_URL . 'user/dashboard.php', 'Accesso negato', 'error');
}

// Conta statistiche
$stats = [];
$stats['libri'] = $conn->query("SELECT COUNT(*) as count FROM libri")->fetch_assoc()['count'];
$stats['utenti'] = $conn->query("SELECT COUNT(*) as count FROM utenti")->fetch_assoc()['count'];
$stats['prestiti_attivi'] = $conn->query("SELECT COUNT(*) as count FROM prestiti WHERE stato = 'attivo'")->fetch_assoc()['count'];
$stats['prestiti_scaduti'] = $conn->query("SELECT COUNT(*) as count FROM prestiti WHERE stato = 'scaduto'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/theme-toggle.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Dashboard Admin</h1>
        
        <?php mostra_messaggio(); ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Libri</h3>
                <p class="stat-number"><?php echo $stats['libri']; ?></p>
                <a href="libri/index.php" class="btn btn-small btn-primary">Gestisci</a>
            </div>
            
            <div class="stat-card">
                <h3>Utenti</h3>
                <p class="stat-number"><?php echo $stats['utenti']; ?></p>
                <a href="utenti/index.php" class="btn btn-small btn-primary">Gestisci</a>
            </div>
            
            <div class="stat-card">
                <h3>Prestiti Attivi</h3>
                <p class="stat-number" style="color: green;"><?php echo $stats['prestiti_attivi']; ?></p>
                <a href="prestiti/index.php" class="btn btn-small btn-primary">Visualizza</a>
            </div>
            
            <div class="stat-card">
                <h3>Prestiti Scaduti</h3>
                <p class="stat-number" style="color: red;"><?php echo $stats['prestiti_scaduti']; ?></p>
                <a href="prestiti/index.php?stato=scaduto" class="btn btn-small btn-danger">Visualizza</a>
            </div>
        </div>
        
        <hr>
        
        <div class="manage-grid">
            <a href="libri/index.php" class="manage-card">
                <h2>Libri</h2>
                <p>Aggiungi, modifica o elimina libri dal catalogo</p>
            </a>
            
            <a href="autori/index.php" class="manage-card">
                <h2>Autori</h2>
                <p>Gestisci gli autori del catalogo</p>
            </a>
            
            <a href="categorie/index.php" class="manage-card">
                <h2>Categorie</h2>
                <p>Gestisci le categorie di libri</p>
            </a>
            
            <a href="prestiti/index.php" class="manage-card">
                <h2>Prestiti</h2>
                <p>Gestisci e monitora i prestiti attivi</p>
            </a>
            
            <a href="utenti/index.php" class="manage-card">
                <h2>Utenti</h2>
                <p>Gestisci gli utenti della biblioteca</p>
            </a>
        </div>
    </div>
</body>
</html>



