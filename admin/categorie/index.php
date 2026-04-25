<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM categorie WHERE 1=1";

if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND nome LIKE '%$search_escaped%'";
}

$sql .= " ORDER BY nome ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Categorie</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/theme-toggle.js"></script>
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Gestione Categorie</h1>
        
        <?php mostra_messaggio(); ?>
        
        <div class="toolbar">
            <a href="create.php" class="btn btn-success">+ Aggiungi Categoria</a>
            
            <form method="GET" class="search-form" style="display:inline;">
                <input type="text" name="search" placeholder="Cerca categoria" value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Cerca</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($cat = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo $cat['nome']; ?></td>
                            <td><?php echo substr($cat['descrizione'], 0, 50) . (strlen($cat['descrizione']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-small btn-warning">Modifica</a>
                                <a href="delete.php?id=<?php echo $cat['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Sicuro?')">Elimina</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Nessuna categoria trovata</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>



