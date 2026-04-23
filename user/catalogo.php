<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

// Carica tutti i libri disponibili per ricerca lato client
$sql = "SELECT l.*, a.nome as autore_nome, a.cognome as autore_cognome, c.nome as categoria_nome
        FROM libri l
        LEFT JOIN autori a ON l.id_autore = a.id
        LEFT JOIN categorie c ON l.id_categoria = c.id
        WHERE l.copie_disponibili > 0
        ORDER BY l.titolo ASC";
$result = $conn->query($sql);

$categorie = $conn->query("SELECT * FROM categorie ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Catalogo Libri</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>

    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Catalogo Libri</h1>
        
        <?php mostra_messaggio(); ?>
        
        <!-- Form ricerca -->
        <div class="search-form admin-form">
            <input type="text" id="search-input" placeholder="Cerca per titolo o autore" value="">
            <select id="category-select">
                <option value="">Tutte le categorie</option>
                <?php
                $categorie->data_seek(0); // Reset pointer
                while($cat = $categorie->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nome']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="button" id="reset-search" class="btn btn-secondary">Reset</button>
        </div>
        
        <!-- Griglia libri -->
        <div class="books-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($libro = $result->fetch_assoc()): ?>
                    <div class="book-card" data-title="<?php echo htmlspecialchars(strtolower($libro['titolo'])); ?>" data-author="<?php echo htmlspecialchars(strtolower(($libro['autore_cognome'] ?? '') . ' ' . ($libro['autore_nome'] ?? ''))); ?>" data-category="<?php echo $libro['id_categoria']; ?>">
                        <?php if ($libro['copertina']): ?>
                            <img src="../uploads/copertine/<?php echo $libro['copertina']; ?>" alt="Copertina">
                        <?php else: ?>
                            <div class="no-image">📖 Nessuna immagine</div>
                        <?php endif; ?>
                        
                        <h3><?php echo substr($libro['titolo'], 0, 50); ?></h3>
                        <p class="author"><?php echo ($libro['autore_cognome'] ?? 'Unknown') . ' ' . ($libro['autore_nome'] ?? ''); ?></p>
                        <p class="category"><?php echo $libro['categoria_nome'] ?? 'N/A'; ?></p>
                        <p class="availability">Copie: <?php echo $libro['copie_disponibili']; ?></p>
                        
                        <a href="dettaglio_libro.php?id=<?php echo $libro['id']; ?>" class="btn btn-primary">Dettagli</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nessun libro disponibile al momento.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
