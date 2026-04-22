<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

$user_id = $_SESSION['user_id'];

// Aggiorna prestiti scaduti
$conn->query("UPDATE prestiti SET stato = 'scaduto' WHERE stato = 'attivo' AND data_scadenza < CURDATE()");

// Carica prestiti dell'utente
$sql = "SELECT p.*, l.titolo, l.copertina, l.isbn, 
        CONCAT(a.nome, ' ', a.cognome) as autore
        FROM prestiti p 
        JOIN libri l ON p.id_libro = l.id 
        LEFT JOIN autori a ON l.id_autore = a.id
        WHERE p.id_utente = ? 
        ORDER BY p.data_prestito DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$prestiti = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>I Miei Prestiti</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>I Miei Prestiti</h1>
        
        <?php mostra_messaggio(); ?>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Copertina</th>
                    <th>Libro</th>
                    <th>Autore</th>
                    <th>Data Prestito</th>
                    <th>Data Scadenza</th>
                    <th>Data Restituzione</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestiti->num_rows > 0): ?>
                    <?php while($p = $prestiti->fetch_assoc()): ?>
                        <tr class="<?php echo $p['stato'] == 'scaduto' ? 'row-danger' : ''; ?>">
                            <td>
                                <?php if ($p['copertina']): ?>
                                    <img src="../uploads/copertine/<?php echo $p['copertina']; ?>" 
                                         alt="Copertina" width="40">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $p['titolo']; ?></td>
                            <td><?php echo $p['autore']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['data_prestito'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['data_scadenza'])); ?></td>
                            <td>
                                <?php echo $p['data_restituzione'] ? date('d/m/Y H:i', strtotime($p['data_restituzione'])) : '-'; ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = '';
                                switch($p['stato']) {
                                    case 'attivo': $badge_class = 'badge-success'; break;
                                    case 'restituito': $badge_class = 'badge-info'; break;
                                    case 'scaduto': $badge_class = 'badge-danger'; break;
                                }
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo ucfirst($p['stato']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (in_array($p['stato'], ['attivo', 'scaduto'])): ?>
                                    <a href="restituisci_prestito.php?id=<?php echo $p['id']; ?>" 
                                       class="btn btn-small btn-warning"
                                       onclick="return confirm('Vuoi restituire questo libro?')">
                                        Restituisci
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">Nessun prestito effettuato</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
