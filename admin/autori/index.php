<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM autori WHERE 1=1";

if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (nome LIKE '%$search_escaped%' OR cognome LIKE '%$search_escaped%')";
}

$sql .= " ORDER BY cognome, nome ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Autori</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/theme-toggle.js"></script>
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Gestione Autori</h1>
        
        <?php mostra_messaggio(); ?>
        
        <div class="toolbar">
            <a href="create.php" class="btn btn-success">+ Aggiungi Autore</a>
            
            <form method="GET" class="search-form" style="display:inline;">
                <input type="text" name="search" placeholder="Cerca autore" value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Cerca</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Nazionalità</th>
                    <th>Data Nascita</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($autore = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $autore['id']; ?></td>
                            <td><?php echo $autore['nome']; ?></td>
                            <td><?php echo $autore['cognome']; ?></td>
                            <td><?php echo $autore['nazionalita'] ?? '-'; ?></td>
                            <td><?php echo $autore['data_nascita'] ? date('d/m/Y', strtotime($autore['data_nascita'])) : '-'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $autore['id']; ?>" class="btn btn-small btn-warning">Modifica</a>
                                <a href="delete.php?id=<?php echo $autore['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Sicuro?')">Elimina</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">Nessun autore trovato</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
