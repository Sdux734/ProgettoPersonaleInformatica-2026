<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id = $_GET['id'] ?? 0;

// Non può eliminare se stesso
if ($id == $_SESSION['user_id']) {
    redirect('index.php', 'Non puoi eliminare il tuo account', 'error');
}

// Elimina utente e i suoi prestiti
$stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirect('index.php', 'Utente eliminato con successo', 'success');
} else {
    redirect('index.php', 'Errore durante l\'eliminazione', 'error');
}
?>
