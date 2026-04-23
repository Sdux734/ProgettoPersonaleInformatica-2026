<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

// Filtri
$stato = $_GET['stato'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT p.*, l.titolo, l.isbn, 
        CONCAT(u.nome, ' ', u.cognome) as utente_nome, u.email
        FROM prestiti p 
        JOIN libri l ON p.id_libro = l.id 
        JOIN utenti u ON p.id_utente = u.id 
        WHERE 1=1";

if ($stato) {
    $stato_escaped = $conn->real_escape_string($stato);
    $sql .= " AND p.stato = '$stato_escaped'";
}

if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (l.titolo LIKE '%$search_escaped%' OR u.nome LIKE '%$search_escaped%' OR u.cognome LIKE '%$search_escaped%')";
}

$sql .= " ORDER BY p.data_prestito DESC";
$result = $conn->query($sql);

// Aggiorna stati scaduti
$conn->query("UPDATE prestiti SET stato = 'scaduto' WHERE stato = 'attivo' AND data_scadenza < CURDATE()");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Prestiti</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/theme-toggle.js"></script>
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Gestione Prestiti</h1>
        
        <?php mostra_messaggio(); ?>
        
        <!-- Filtri -->
        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Cerca per libro o utente" value="<?php echo $search; ?>">
            
            <select name="stato">
                <option value="">Tutti gli stati</option>
                <option value="attivo" <?php echo $stato == 'attivo' ? 'selected' : ''; ?>>Attivi</option>
                <option value="scaduto" <?php echo $stato == 'scaduto' ? 'selected' : ''; ?>>Scaduti</option>
                <option value="restituito" <?php echo $stato == 'restituito' ? 'selected' : ''; ?>>Restituiti</option>
            </select>
            
            <button type="submit" class="btn btn-primary">Filtra</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </form>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utente</th>
                    <th>Libro</th>
                    <th>Data Prestito</th>
                    <th>Scadenza</th>
                    <th>Restituzione</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $result->fetch_assoc()): ?>
                    <tr class="<?php echo $p['stato'] == 'scaduto' ? 'row-danger' : ''; ?>">
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <?php echo $p['utente_nome']; ?><br>
                            <small><?php echo $p['email']; ?></small>
                        </td>
                        <td><?php echo $p['titolo']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['data_prestito'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['data_scadenza'])); ?></td>
                        <td><?php echo $p['data_restituzione'] ? date('d/m/Y', strtotime($p['data_restituzione'])) : '-'; ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $p['stato'] == 'attivo' ? 'success' : 
                                    ($p['stato'] == 'scaduto' ? 'danger' : 'info'); 
                            ?>">
                                <?php echo ucfirst($p['stato']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['stato'] == 'attivo' || $p['stato'] == 'scaduto'): ?>
                                <a href="restituisci.php?id=<?php echo $p['id']; ?>" 
                                   class="btn btn-small btn-success"
                                   onclick="return confirm('Confermi la restituzione?')">
                                    Restituisci
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
