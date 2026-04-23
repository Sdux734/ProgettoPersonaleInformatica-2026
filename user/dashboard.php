<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utente</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>

    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Dashboard Utente</h1>
        
        <?php mostra_messaggio(); ?>
        
        <div class="dashboard-grid">
            <div class="card">
                <h2>📚 Catalogo</h2>
                <p>Sfoglia e cerca i libri disponibili nella biblioteca</p>
                <a href="catalogo.php" class="btn btn-primary">Vai al Catalogo</a>
            </div>
            
            <div class="card">
                <h2>📖 I Miei Prestiti</h2>
                <p>Visualizza lo stato dei tuoi prestiti attuali e passati</p>
                <a href="miei_prestiti.php" class="btn btn-primary">I Miei Prestiti</a>
            </div>
            
            <div class="card">
                <h2>👤 Profilo</h2>
                <p>Gestisci le tue informazioni personali</p>
                <a href="profilo.php" class="btn btn-primary">Vai al Profilo</a>
            </div>
        </div>
    </div>
</body>
</html>
