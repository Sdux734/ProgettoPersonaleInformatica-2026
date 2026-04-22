<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

// Verifica che sia admin
if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

// Ricerca e filtri
$search = $_GET['search'] ?? '';
$categoria = $_GET['categoria'] ?? '';

// Query con JOIN per ottenere nome autore e categoria
$sql = "SELECT l.*, a.nome as autore_nome, a.cognome as autore_cognome, c.nome as categoria_nome 
        FROM libri l 
        LEFT JOIN autori a ON l.id_autore = a.id 
        LEFT JOIN categorie c ON l.id_categoria = c.id 
        WHERE 1=1";

if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (l.titolo LIKE '%$search_escaped%' OR l.isbn LIKE '%$search_escaped%')";
}

if ($categoria) {
    $categoria = intval($categoria);
    $sql .= " AND l.id_categoria = $categoria";
}

$sql .= " ORDER BY l.titolo ASC";

$result = $conn->query($sql);

// Ottieni categorie per filtro
$categorie = $conn->query("SELECT * FROM categorie ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Libri - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Gestione Libri</h1>
        
        <?php mostra_messaggio(); ?>
        
        <div class="toolbar">
            <a href="create.php" class="btn btn-success">+ Aggiungi Libro</a>
            
            <!-- Form di ricerca -->
            <form method="GET" class="search-form" style="display:inline;">
                <input type="text" name="search" placeholder="Cerca per titolo o ISBN" value="<?php echo $search; ?>">
                
                <select name="categoria">
                    <option value="">Tutte le categorie</option>
                    <?php while($cat = $categorie->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $categoria == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="btn btn-primary">Cerca</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Copertina</th>
                    <th>Titolo</th>
                    <th>Autore</th>
                    <th>ISBN</th>
                    <th>Categoria</th>
                    <th>Copie Disp./Totali</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($libro = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $libro['id']; ?></td>
                            <td>
                                <?php if ($libro['copertina']): ?>
                                    <img src="../../uploads/copertine/<?php echo $libro['copertina']; ?>" 
                                         alt="Copertina" width="50">
                                <?php else: ?>
                                    <span>No img</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $libro['titolo']; ?></td>
                            <td><?php echo ($libro['autore_nome'] ?? 'N/A') . ' ' . ($libro['autore_cognome'] ?? ''); ?></td>
                            <td><?php echo $libro['isbn']; ?></td>
                            <td><?php echo $libro['categoria_nome'] ?? 'N/A'; ?></td>
                            <td><?php echo $libro['copie_disponibili'] . '/' . $libro['copie_totali']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $libro['id']; ?>" class="btn btn-small btn-warning">Modifica</a>
                                <a href="delete.php?id=<?php echo $libro['id']; ?>" 
                                   class="btn btn-small btn-danger" 
                                   onclick="return confirm('Sicuro di voler eliminare questo libro?')">Elimina</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">Nessun libro trovato</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
