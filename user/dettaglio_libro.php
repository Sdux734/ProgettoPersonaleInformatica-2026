<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

$id = $_GET['id'] ?? 0;

// Carica dettagli libro
$stmt = $conn->prepare("SELECT l.*, a.nome as autore_nome, a.cognome as autore_cognome, c.nome as categoria_nome 
                        FROM libri l 
                        LEFT JOIN autori a ON l.id_autore = a.id 
                        LEFT JOIN categorie c ON l.id_categoria = c.id 
                        WHERE l.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    redirect('catalogo.php', 'Libro non trovato', 'error');
}

// Verifica se l'utente ha già questo libro in prestito
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM prestiti WHERE id_utente = ? AND id_libro = ? AND stato = 'attivo'");
$stmt->bind_param("ii", $user_id, $id);
$stmt->execute();
$prestito_attivo = $stmt->get_result()->num_rows > 0;

// Conta prestiti attivi dell'utente
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prestiti WHERE id_utente = ? AND stato = 'attivo'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$prestiti_count = $stmt->get_result()->fetch_assoc()['count'];

$puo_richiedere = !$prestito_attivo && 
                  $libro['copie_disponibili'] > 0 && 
                  $prestiti_count < MAX_PRESTITI_UTENTE;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?php echo $libro['titolo']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/theme-toggle.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="book-style">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="book-detail">
            <div class="book-image">
                <?php if ($libro['copertina']): ?>
                    <img src="../uploads/copertine/<?php echo $libro['copertina']; ?>" alt="Copertina">
                <?php else: ?>
                    <div class="no-image-large">📖</div>
                <?php endif; ?>
            </div>
            
            <div class="book-info">
                <h1><?php echo htmlspecialchars($libro['titolo']); ?></h1>
                <h2>di <?php echo htmlspecialchars(($libro['autore_cognome'] ?? '') . ' ' . ($libro['autore_nome'] ?? '')); ?></h2>
                
                <div class="details">
                    <p><strong>ISBN:</strong> <span><?php echo htmlspecialchars($libro['isbn']); ?></span></p>
                    <p><strong>Categoria:</strong> <span><?php echo htmlspecialchars($libro['categoria_nome'] ?? 'N/A'); ?></span></p>
                    <p><strong>Anno:</strong> <span><?php echo htmlspecialchars($libro['anno_pubblicazione'] ?? 'N/A'); ?></span></p>
                    <p><strong>Editore:</strong> <span><?php echo htmlspecialchars($libro['editore'] ?? 'N/A'); ?></span></p>
                    <p><strong>Pagine:</strong> <span><?php echo htmlspecialchars($libro['numero_pagine'] ?? 'N/A'); ?></span></p>
                    <p><strong>Disponibilità:</strong> <span><?php echo $libro['copie_disponibili']; ?> / <?php echo $libro['copie_totali']; ?> copie</span></p>
                </div>
                
                <?php if ($libro['descrizione']): ?>
                    <div class="description">
                        <h3>Sinossi</h3>
                        <p><?php echo nl2br(htmlspecialchars($libro['descrizione'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <?php if ($puo_richiedere): ?>
                        <a href="richiedi_prestito.php?id=<?php echo $libro['id']; ?>" 
                           class="btn btn-success"
                           onclick="return confirm('Vuoi richiedere il prestito di questo libro?')">
                            📚 Richiedi Prestito
                        </a>
                    <?php elseif ($prestito_attivo): ?>
                        <span class="badge badge-warning">⚠ Hai già questo libro in prestito</span>
                    <?php elseif ($libro['copie_disponibili'] == 0): ?>
                        <span class="badge badge-danger">✕ Non disponibile</span>
                    <?php elseif ($prestiti_count >= MAX_PRESTITI_UTENTE): ?>
                        <span class="badge badge-warning">⚠ Limite di prestiti raggiunto</span>
                    <?php endif; ?>
                    
                    <a href="catalogo.php" class="btn btn-secondary">← Torna al catalogo</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
