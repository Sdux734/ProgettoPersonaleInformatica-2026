<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM utenti WHERE 1=1";

if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (nome LIKE '%$search_escaped%' OR cognome LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%')";
}

$sql .= " ORDER BY cognome, nome ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Utenti</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/theme-toggle.js"></script>
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Gestione Utenti</h1>
        
        <?php mostra_messaggio(); ?>
        
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cerca utente" value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-primary">Cerca</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </form>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Email</th>
                    <th>Ruolo</th>
                    <th>Data Registrazione</th>
                    <th>Attivo</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($utente = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $utente['id']; ?></td>
                            <td><?php echo $utente['nome']; ?></td>
                            <td><?php echo $utente['cognome']; ?></td>
                            <td><?php echo $utente['email']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo $utente['ruolo'] == 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo ucfirst($utente['ruolo']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($utente['data_registrazione'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $utente['attivo'] ? 'success' : 'danger'; ?>">
                                    <?php echo $utente['attivo'] ? 'Sì' : 'No'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $utente['id']; ?>" class="btn btn-small btn-warning">Modifica</a>
                                <?php if ($utente['id'] != $_SESSION['user_id']): ?>
                                    <?php if ($utente['attivo']): ?>
                                        <a href="ban.php?id=<?php echo $utente['id']; ?>&action=ban" class="btn btn-small btn-danger" onclick="return confirm('Vuoi davvero bannare questo utente?')">Banna</a>
                                    <?php else: ?>
                                        <a href="ban.php?id=<?php echo $utente['id']; ?>&action=unban" class="btn btn-small btn-success" onclick="return confirm('Vuoi ripristinare questo utente?')">Ripristina</a>
                                    <?php endif; ?>
                                    <a href="delete.php?id=<?php echo $utente['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Sicuro?')">Elimina</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">Nessun utente trovato</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
